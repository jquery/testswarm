<?php
/**
 * dbUpdate.php
 *
 * @author Timo Tijhof, 2012
 * @since 1.0.0
 * @package TestSwarm
 */
define( 'SWARM_ENTRY', 'SCRIPT' );
require_once __DIR__ . '/../inc/init.php';

class DBUpdateScript extends MaintenanceScript {

	// Versions that involve database changes
	protected static $updates = array(
		'1.0.0-alpha',
	);

	protected function init() {
		$this->setDescription(
			'Update the TestSwarm database from a past state to the current version.'
			. ' Depending on the version, some data can not be preserved. More information'
			. ' will be provided and a confirmation will be required if that is the case.'
		);
		$this->registerOption( "quick", "boolean", "Skips questions and run the updater unconditionally." );
	}

	protected function execute() {
		global $swarmInstallDir;

		$currentVersionFile = "$swarmInstallDir/config/version.ini";
		if ( !is_readable( $currentVersionFile ) ) {
			throw new SwarmException( "version.ini is missing or unreadable." );
		}
		$currentVersion = trim( file_get_contents( $currentVersionFile ) );

		if ( $this->getOption( "quick" ) ) {
			$this->doDatabaseUpdates();
			return;
		}

		$this->out( "From which version are you upgrading? (use --quick to skip this)" );
		$originVersion = $this->cliInput();

		// 1 => 1.0.0
		$originVersion = explode( ".", $originVersion );
		if ( !isset( $originVersion[1] ) ) {
			$originVersion[] = "0";
		}
		if ( !isset( $originVersion[2] ) ) {
			$originVersion[] = "0";
		}
		$originVersion = implode( ".", $originVersion );

		$this->out(
			"Update origin: $originVersion\n"
			. "Current software version: $currentVersion"
		);

		$scheduledUpdates = array();
		$scheduledUpdatesStr = "";

		$prev = $originVersion;
		foreach ( self::$updates as $updateTarget ) {
			if ( version_compare( $updateTarget, $originVersion, ">" ) ) {
				$scheduledUpdates[] = $updateTarget;
				$scheduledUpdatesStr .= "* $prev -> $updateTarget\n";
				$prev = $updateTarget;
			}
		}

		if ( !count( $scheduledUpdates ) ) {
			$this->out( "No updates found for $originVersion." );
			$this->out( "Do you want to run the updater anyway (use --quick to skip this)? (Y/N)" );
			$quick = $this->cliInput();
			if ( $quick === "Y" ) {
				$this->doDatabaseUpdates();
			}
			return;
		}
		$this->out( "Update paths:\n" . $scheduledUpdatesStr );
		$this->doDatabaseUpdates();
	}

	/**
	 * The actual database updates
	 *  Friendly reminder from http://dev.mysql.com/doc/refman/5.1/en/alter-table.html
	 * - Column name must be mentioned twice in ALTER TABLE CHANGE
	 * - Definition must be complete
	 *   So 'CHANGE foo BIGINT' on a 'foo INT UNSIGNED DEFAULT 1' will remove
	 *   the default and unsigned property.
	 *   Except for PRIMARY KEY or UNIQUE properties, those must never be
	 *   part of a CHANGE clause.
	 */
	protected function doDatabaseUpdates() {
		if ( $this->getContext()->dbLock() ) {
			$this->error( "Database is currently locked, please remove ./cache/database.lock before updating." );
		}

		$db = $this->getContext()->getDB();

		$this->out( "Setting database.lock, other requests may not access the database during the update." );
		$this->getContext()->dbLock( true );

		$this->out( "Executing tests on the database to detect where updates are needed..." );

		// 1.0.0: useragents table removed, and many column changes.
		// If the previous version was before 1.0.0 we won't bother changing a ton of stuff
		// because most changes in 1.0.0 aren't easily convertable. Instead do a few quick
		// checks to verify this is in fact a pre 1.0.0 database, ask the user for
		// confirmation, and instead re-install from scratch, re-importing only the users.
		$has_useragents = $db->tableExists( 'useragents' );
		$has_users_request = $db->fieldExists( 'users', 'request' );
		$clients_useragent_id = $db->fieldInfo( 'clients', 'useragent_id' );
		if ( !is_object( $clients_useragent_id ) ) {
			$this->unknownDatabaseState( 'clients.useragent_id not found' );
			return;
		}
		if ( !$has_useragents
			&& !$has_users_request
			&& !$clients_useragent_id->numeric
			&& $clients_useragent_id->type === "string"
		) {
			$this->out( "...useragents already dropped" );
			$this->out( "...users.request already dropped" );
			$this->out( "...client.useragent_id is up to date" );
		} else {
			$this->out(
				"\n"
				. "It appears this database is from before 1.0.0. No upgrade path exists for those versions.\n"
				. "The updater could re-install TestSwarm (optionally importing old users)\n"
				. "THIS WILL DELETE ALL DATA.\nContinue? (Y/N)" );
			$reinstall = $this->cliInput();
			if ( $reinstall !== "Y" ) {
				// Nothing left to do. Remove database.lock and abort the script
				$this->getContext()->dbLock( false );
				return;
			}

			$this->out( "Import user names and tokens from the old database after re-installing?\n"
				. "(Note: password and seed cannot be restored due to incompatibility in the database.\n"
				. " Instead the auth token will be used as the the new password) (Y/N)" );
			$reimportUsers = $this->cliInput();

			// Drop all known TestSwarm tables in the database
			// (except users, handled separately)
			foreach( array(
				'run_client',
				'clients',
				'run_useragent',
				'useragents',
				'runs',
				'jobs',
			) as $dropTable ) {
				$this->outRaw( "Dropping $dropTable table..." );
				$exists = $db->tableExists( $dropTable );
				if ( $exists ) {
					$dropped = $db->query( "DROP TABLE " . $db->addIdentifierQuotes( $dropTable ) );
					$this->out( " " . ($dropped ? "OK" : "FAILED" ) );
				} else {
					$this->out( "SKIPPED (didn't exist)" );
				}
			}

			// Handle users table (reimport or drop as well)
			$userRows = array();
			if ( $reimportUsers === "Y" ) {
				$this->out( "Upgrading users table" );
				$this->outRaw( "Fetching current users..." );
				$has_users = $db->tableExists( 'users' );
				if ( !$has_users ) {
						$this->out( "SKIPPED (users table didn't exist)" );
				} else {
					$userRows = $db->getRows( "SELECT * FROM users" );
					$this->out( "OK" );
				}
			}
			$this->outRaw( "Dropping users table..." );
			$dropped = $db->query( "DROP TABLE users" );
			$this->out( " " . ($dropped ? "OK" : "FAILED") );

			// Create new tables
			$this->outRaw( "Creating new tables... (this may take a few minutes)" );

			global $swarmInstallDir;
			$fullSchemaFile = "$swarmInstallDir/config/testswarm.sql";
			if ( !is_readable( $fullSchemaFile ) ) {
				$this->error( "Can't read testswarm.sql" );
			}
			$fullSchemaSql = file_get_contents( $fullSchemaFile );
			$executed = $db->batchQueryFromFile( $fullSchemaSql );
			if ( !$executed ) {
				$this->error( "Creating new tables failed" );
			}
			$this->out( "OK" );

			if ( $reimportUsers === "Y" ) {
				$this->out( "Re-importing " . count( $userRows ) .  " users..." );
				foreach ( $userRows as $userRow ) {
					$this->outRaw( "- creating user '" . $userRow->name . "'... " );
					if ( empty( $userRow->password ) || empty( $userRow->seed ) || empty( $userRow->auth ) ) {
						$this->out( "SKIPPED: Not a real account but a swarm client." );
						continue;
					}
					try {
						$signupAction = SignupAction::newFromContext( $this->getContext() );
						// Password stored in the old datbase is a hash of the old seed (of type 'double'_
						// and the actual password. We can't create this user with the same password because
						// sha1 is not supposed to be decodable.
						// I tried overriding the created row after the creation with the old seed and password,
						// but that didn't work because the old seed doesn't fit in the new seed field (of binary(40)).
						// When inserted mysql transforms it into something else and sha1(seed + password) will no
						// longer match the hash. So instead create the new user with the auth token as password. 
						$signupAction->doCreateUser( $userRow->name, $userRow->auth );
						$err = $signupAction->getError();
						if ( !$err ) {
							$this->outRaw( "OK. Restoring auth token... " );
							$data = $signupAction->getData();
							$updated = $db->query(str_queryf(
								"UPDATE users
								SET
									auth = %s
								WHERE id = %u",
								// authToken is used in addjob scripts.
								$userRow->auth,
								$data["userID"]
							));
							$this->out( $updated ? "OK." : "FAILED." );
						} else {
							$this->out( "FAILED. SignupAction error. {$err["info"]}" );
						}
					} catch ( Exception $e ) {
						$this->out( "FAILED. Unexpected exception thrown while creating account. {$e->getMessage()}" );
					}
				}

			} // End of users re-import
		} // End of 1.0.0-alpha update


		$this->getContext()->dbLock( false );
		$this->out( "Removed database.lock.\nNo more updates." );
	}

	protected function unknownDatabaseState( $error = "" ) {
		if ( $error !== "" ) {
			$error = "\nError: $error";
		}
		$this->error( "The database was found in a state not known in any version.\n"
			."Please verify your settings. Note that this is not an installer!$error" );
	}
}

$script = DBUpdateScript::newFromContext( $swarmContext );
$script->run();
