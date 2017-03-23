<?php
/**
 * Update database.
 *
 * @author Timo Tijhof
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
		$this->registerOption( 'quick', 'boolean', 'Skips questions and run the updater unconditionally.' );
	}

	protected function execute() {
		global $swarmInstallDir;

		$currentVersionFile = "$swarmInstallDir/config/version.ini";
		if ( !is_readable( $currentVersionFile ) ) {
			throw new SwarmException( 'version.ini is missing or unreadable.' );
		}
		$currentVersion = trim( file_get_contents( $currentVersionFile ) );

		if ( $this->getOption( 'quick' ) ) {
			$this->doDatabaseUpdates();
			return;
		}

		$this->out( 'From which version are you upgrading? (leave empty or use --quick option to skip this)' );
		$originVersion = $this->cliInput();
		if ( !$originVersion ) {
			$originVersion = '(auto...)';
		}

		// 1 => 1.0.0
		$originVersion = explode( '.', $originVersion );
		if ( !isset( $originVersion[1] ) ) {
			$originVersion[] = '0';
		}
		if ( !isset( $originVersion[2] ) ) {
			$originVersion[] = '0';
		}
		$originVersion = implode( '.', $originVersion );

		$this->out(
			"Update origin: $originVersion\n"
			. "Current software version: $currentVersion"
		);

		$scheduledUpdates = array();
		$scheduledUpdatesStr = '';

		$prev = $originVersion;
		foreach ( self::$updates as $updateTarget ) {
			if ( version_compare( $updateTarget, $originVersion, '>' ) ) {
				$scheduledUpdates[] = $updateTarget;
				$scheduledUpdatesStr .= "* $prev -> $updateTarget\n";
				$prev = $updateTarget;
			}
		}

		if ( !count( $scheduledUpdates ) ) {
			$this->out( "No updates found for $originVersion." );
			$this->out( 'Do you want to run the updater anyway (use --quick to skip this)? (Y/N)' );
			$quick = $this->cliInput();
			if ( $quick === 'Y' ) {
				$this->doDatabaseUpdates();
			}
			return;
		}
		$this->out( "Update paths:\n" . $scheduledUpdatesStr );
		$this->doDatabaseUpdates();
	}

	/**
	 * The actual database updates
	 * Friendly reminder from http://dev.mysql.com/doc/refman/5.1/en/alter-table.html
	 * - Column name must be mentioned twice in ALTER TABLE CHANGE
	 * - Definition must be complete
	 *   So 'CHANGE foo BIGINT' on a 'foo INT UNSIGNED DEFAULT 1' will remove
	 *   the default and unsigned property.
	 *   Except for PRIMARY KEY or UNIQUE properties, those must never be
	 *   part of a CHANGE clause.
	 */
	protected function doDatabaseUpdates() {
		if ( $this->getContext()->dbLock() ) {
			$this->error( 'Database is currently locked, please remove ./cache/database.lock before updating.' );
		}

		$db = $this->getContext()->getDB();

		$this->out( 'Setting database.lock, other requests may not access the database during the update.' );
		$this->getContext()->dbLock( true );

		$this->out( 'Running tests on the database to detect which updates are needed.' );

		/**
		 * 0.2.0 -> 1.0.0-alpha (patch-new-ua-runresults.sql)
		 * useragents and run_client table removed, many column changes, new runresults table.
		 */

		// If the previous version was before 1.0.0 we won't offer an update, because most
		// changes in 1.0.0 can't be simulated without human intervention. The changes are not
		// backwards compatible. Instead do a few quick checks to verify this is in fact a
		// pre-1.0.0 database, then ask the user for a re-install from scratch
		// (except for the users table).
		$has_run_client = $db->tableExists( 'run_client' );
		$has_users_request = $db->fieldExists( 'users', 'request' );
		$clients_useragent_id = $db->fieldInfo( 'clients', 'useragent_id' );
		if ( !$clients_useragent_id ) {
			$this->unknownDatabaseState( 'clients.useragent_id not found' );
			return;
		}
		$mysql_type_varchar = 253;
		if ( !$has_run_client
			&& !$has_users_request
			// https://dev.mysql.com/doc/internals/en/myisam-column-attributes.html
			// https://secure.php.net/mysqli_fetch_field_direct#85771
			&& $clients_useragent_id->type === $mysql_type_varchar
		) {
			$this->out( '... run_client table already dropped' );
			$this->out( '... users.request already dropped' );
			$this->out( '... client.useragent_id is up to date' );
		} else {
			$this->out(
				"\n"
				. "It appears this database is from before 1.0.0. No update exists for those versions.\n"
				. "The updater could re-install TestSwarm (optionally preserving user accounts)\n"
				. "THIS WILL DELETE ALL DATA.\nContinue? (Y/N)" );
			$reinstall = $this->cliInput();
			if ( $reinstall !== 'Y' ) {
				// Nothing left to do. Remove database.lock and abort the script
				$this->getContext()->dbLock( false );
				return;
			}

			$this->out( "Import user names and tokens from the old database after re-installing?\n"
				. "(Note: password and seed cannot be restored due to incompatibility in the database.\n"
				. ' Instead the auth token will be used as the the new password) (Y/N)' );
			$reimportUsers = $this->cliInput();

			// Drop all known TestSwarm tables in the database
			// (except users, handled separately)
			foreach ( array(
				'run_client', // Removed in 1.0.0
				'clients',
				'run_useragent',
				'useragents', // Removed in 1.0.0
				'runs',
				'jobs',
			) as $dropTable ) {
				$this->outRaw( "Dropping $dropTable table..." );
				$exists = $db->tableExists( $dropTable );
				if ( $exists ) {
					$dropped = $db->query( 'DROP TABLE ' . $db->addIdentifierQuotes( $dropTable ) );
					$this->out( ' ' . ($dropped ? 'OK' : 'FAILED' ) );
				} else {
					$this->out( 'SKIPPED (didn\'t exist)' );
				}
			}

			// Handle users table (reimport or drop as well)
			$userRows = array();
			if ( $reimportUsers === 'Y' ) {
				$this->out( 'Upgrading users table' );
				$this->outRaw( 'Fetching current users...' );
				$has_users = $db->tableExists( 'users' );
				if ( !$has_users ) {
						$this->out( 'SKIPPED (users table didn\'t exist)' );
				} else {
					$userRows = $db->getRows( 'SELECT * FROM users' );
					$this->out( 'OK' );
				}
			}
			$this->outRaw( 'Dropping users table...' );
			$dropped = $db->query( 'DROP TABLE users' );
			$this->out( ' ' . ($dropped ? 'OK' : 'FAILED') );

			// Create new tables
			$this->outRaw( 'Creating new tables... (this may take a few minutes)' );

			global $swarmInstallDir;
			$fullSchemaFile = "$swarmInstallDir/config/tables.sql";
			if ( !is_readable( $fullSchemaFile ) ) {
				$this->error( 'Can\'t read schema file' );
			}
			$fullSchemaSql = file_get_contents( $fullSchemaFile );
			$executed = $db->batchQueryFromFile( $fullSchemaSql );
			if ( !$executed ) {
				$this->error( 'Creating new tables failed' );
			}
			$this->out( 'OK' );

			if ( $reimportUsers === 'Y' ) {
				$this->out( 'Re-importing ' . count( $userRows ) . ' users...' );
				foreach ( $userRows as $userRow ) {
					$this->outRaw( '- creating user "' . $userRow->name . '"... ' );
					if ( empty( $userRow->password ) || empty( $userRow->seed ) || empty( $userRow->auth ) ) {
						$this->out( 'SKIPPED: Not a project account but a swarm client.' );
						continue;
					}
					try {
						$signupAction = SignupAction::newFromContext( $this->getContext() );
						// Password stored in the old database is a hash of the old seed (of type 'double'
						// and the actual password. We can't create this user with the same password because
						// sha1 is not supposed to be decodable.
						// I tried overriding the created row after the creation with the old seed and password,
						// but that didn't work because the old seed doesn't fit in the new seed field (of binary(40)).
						// When inserted, mysql transforms it into something else and sha1(seed + password) will no
						// longer match the hash. So instead create the new user with the auth token as password.
						$signupAction->doCreateUser( $userRow->name, $userRow->auth );
						$err = $signupAction->getError();
						if ( !$err ) {
							$this->outRaw( 'OK. Restoring auth token... ' );
							$data = $signupAction->getData();
							$updated = $db->query(str_queryf(
								'UPDATE users
								SET
									auth = %s
								WHERE id = %u',
								$userRow->auth,
								$data['userID']
							));
							$this->out( $updated ? 'OK.' : 'FAILED.' );
						} else {
							$this->out( "FAILED. SignupAction error. {$err['info']}" );
						}
					} catch ( Exception $e ) {
						$this->out( "FAILED. Unexpected exception thrown while creating account. {$e->getMessage()}" );
					}
				}

			} // End of users re-import
		} // End of patch-new-ua-runresults.sql

		/**
		 * 1.0.0-alpha (patch-users-projects-conversion.sql)
		 * users table removed, new projects table, various column changes.
		 */

		$has_users = $db->tableExists( 'users' );
		$has_clients_user_id = $db->fieldInfo( 'clients', 'user_id' );
		$has_jobs_user_id = $db->fieldInfo( 'jobs', 'user_id' );
		$has_projects = $db->tableExists( 'projects' );
		$has_clients_name = $db->fieldInfo( 'clients', 'name' );
		$has_jobs_project_id = $db->fieldInfo( 'jobs', 'project_id' );
		$has_clients_useragent_id = $db->fieldInfo( 'clients', 'useragent_id' );

		if ( !$has_users && !$has_clients_user_id && !$has_jobs_user_id ) {
			$this->out( '... users table already dropped' );
			$this->out( '... clients.user_id already dropped' );
			$this->out( '... jobs.user_id already dropped' );
		} else {
			// Verify that the entire database is in the 1.0.0-alpha2012 state,
			// not just part of it.
			foreach ( array(
				'users table' => $has_users,
				'clients.user_id' => $has_clients_user_id,
				'jobs.user_id' => $has_jobs_user_id,
				'projects table' => ! $has_projects,
				'clients.name' => ! $has_clients_name,
				'jobs.project_id' => ! $has_jobs_project_id,
				'clients.useragent_id' => $has_clients_useragent_id,
			) as $label => $isAsExpected)
			if ( !$isAsExpected ) {
				$this->unknownDatabaseState( $label . ' not found' );
				return;
			}

			$this->out( 'Schema changes before users-projects-conversion migration...' );

			$this->out( '... creating projects table' );
			$db->query(
"CREATE TABLE `projects` (
  `id` varchar(255) binary NOT NULL PRIMARY KEY,
  `display_title` varchar(255) binary NOT NULL,
  `site_url` blob NOT NULL default '',
  `password` tinyblob NOT NULL,
  `auth_token` tinyblob NOT NULL,
  `updated` binary(14) NOT NULL,
  `created` binary(14) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
			);

			$this->out( '... adding clients.name' );
			$db->query(
"ALTER TABLE clients
  ADD `name` varchar(255) binary NOT NULL AFTER `id`"
			);

			$this->out( '... adding jobs.project_id' );
			$db->query(
"ALTER TABLE jobs
  ADD `project_id` varchar(255) binary NOT NULL AFTER `name`"
			);

			$this->out( '... dropping constraint fk_clients_user_id' );
			$db->query(
"ALTER TABLE clients
  DROP FOREIGN KEY fk_clients_user_id"
			);

			$this->out( '... dropping constraint fk_jobs_user_id' );
			$db->query(
"ALTER TABLE jobs
  DROP FOREIGN KEY fk_jobs_user_id"
			);

			$this->out( '... dropping constraint fk_runs_job_id' );
			$db->query(
"ALTER TABLE runs
  DROP FOREIGN KEY fk_runs_job_id"
			);

			$this->out( '... dropping constraint fk_run_useragent_run_id' );
			$db->query(
"ALTER TABLE run_useragent
  DROP FOREIGN KEY fk_run_useragent_run_id"
			);

			$this->out( '... dropping constraint fk_runresults_client_id' );
			$db->query(
"ALTER TABLE runresults
  DROP FOREIGN KEY fk_runresults_client_id"
			);

			$this->out( '... dropping index idx_users_name' );
			$db->query(
"ALTER TABLE users
  DROP INDEX idx_users_name"
			);

			$this->out( '... dropping index idx_clients_user_useragent_updated' );
			$db->query(
"ALTER TABLE clients
  DROP INDEX idx_clients_user_useragent_updated"
			);

			$this->out( '... dropping index idx_jobs_user' );
			$db->query(
"ALTER TABLE jobs
  DROP INDEX idx_jobs_user"
			);

			$this->out( 'Migrating old content into new schema...' );

			$this->out( '... fetching users table' );
			$userRows = $db->getRows( 'SELECT * FROM users' ) ?: array();
			$this->out( '... found ' . count( $userRows ) . ' users' );
			foreach ( $userRows as $userRow ) {
				$this->out( '... creating project "' . $userRow->name . '"' );
				if ( !trim( $userRow->seed ) || !trim( $userRow->password ) || !trim( $userRow->auth ) ) {
					// Client.php used to create rows in the users table with blanks
					// in these "required" fields. MySQL expands the emptyness to the full
					// 40-width of the column. Hence the trim().
					$this->out( '    SKIPPED: Not a project account but a swarm client.' );
					continue;
				}
				// Validate project id
				if ( !LoginAction::isValidName( $userRow->name ) ) {
					$this->out( '    SKIPPED: User name not a valid project id. Must match: ' . LoginAction::getNameValidationRegex() );
					continue;
				}
				if ( !$db->getOne(str_queryf( 'SELECT 1 FROM jobs WHERE user_id=%u', $userRow->id )) ) {
					$this->out( '    SKIPPED: Account has 0 jobs' );
					continue;
				}
				$isInserted = $db->query(str_queryf(
					'INSERT INTO projects
					(id, display_title, site_url, password, auth_token, updated, created)
					VALUES(%s, %s, %s, %s, %s, %s, %s);',
					$userRow->name,
					$userRow->name,
					'',
					LoginAction::generatePasswordHashForUserrow( $userRow ),
					sha1( $userRow->auth ),
					swarmdb_dateformat( SWARM_NOW ),
					$userRow->created
				));
				if ( !$isInserted ) {
					$this->out( '    FAILED: Failed to insert row into projects table.' );
					continue;
				}
				$this->out( '... updating references for project "' . $userRow->name . '"' );
				$isUpdated = $db->query(str_queryf(
					'UPDATE clients
					SET name=%s
					WHERE user_id=%u',
					$userRow->name,
					$userRow->id
				));
				if ( !$isUpdated ) {
					$this->out( '    FAILED: Failed to update rows in clients table.' );
					continue;
				}
				$isUpdated = $db->query(str_queryf(
					'UPDATE jobs
					SET project_id=%s
					WHERE user_id=%u',
					$userRow->name,
					$userRow->id
				));
				if ( !$isUpdated ) {
					$this->out( '    FAILED: Failed to update rows in jobs table.' );
					continue;
				}
			}

			$this->out( 'Schema changes after users-projects-conversion migration...' );

			$this->out( '... changing clients.useragent_id' );
			$db->query(
"ALTER TABLE clients
  CHANGE COLUMN `useragent_id` `useragent_id` varchar(255) NOT NULL"
			);

			$this->out( '... dropping clients.user_id' );
			$db->query(
"ALTER TABLE clients
  DROP COLUMN `user_id`"
			);

			$this->out( '... dropping jobs.user_id' );
			$db->query(
"ALTER TABLE jobs
  DROP COLUMN `user_id`"
			);

			$this->out( '... dropping users table' );
			$db->query(
"DROP TABLE users"
			);

			$this->out( '... adding index idx_clients_name_ua_created' );
			$db->query(
"ALTER TABLE clients
  ADD INDEX idx_clients_name_ua_created (name, useragent_id, created);" );

			$this->out( '... adding index idx_jobs_project_created' );
			$db->query(
"ALTER TABLE jobs
  ADD INDEX idx_jobs_project_created (project_id, created);" );


		} // End of patch-users-projects-conversion.sql


		$this->getContext()->dbLock( false );
		$this->out( "Removed database.lock.\nNo more updates." );
	}

	protected function unknownDatabaseState( $error = '' ) {
		if ( $error !== '' ) {
			$error = "\n\nLast failed check before abort: $error";
		}
		$this->error( "\nThe database was found in a state not known in any version.\n"
			. "This could be the result of a previous update run being aborted mid-update,\n"
			. "in that case the automatic update script cannot help you.\n"
			. "Please verify your local settings. Note that this is not an installer! $error" );
	}
}

$script = DBUpdateScript::newFromContext( $swarmContext );
$script->run();
