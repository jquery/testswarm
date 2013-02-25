<?php
/**
 * manageProject.php
 *
 * @author Timo Tijhof, 2012
 * @since 1.0.0
 * @package TestSwarm
 */
define( 'SWARM_ENTRY', 'SCRIPT' );
require_once __DIR__ . '/../inc/init.php';

class ManageProjectScript extends MaintenanceScript {

	protected function init() {
		$this->setDescription(
			'Create a new TestSwarm project. Returns the auth token (can be re-created with refreshProjectToken.php).'
		);
		$this->registerOption( 'create', 'boolean', 'Pass this to the create if it doesn\'t exist.' );
		$this->registerOption( 'id', 'value', 'ID of project (must be in format: [a-z][-a-z0-9], max: 255 chars).' );
		$this->registerOption( 'display-title', 'value', 'Display title (free form text, max: 255 chars)' );
		$this->registerOption( 'password', 'value', 'Password for this project (omit to enter in interactive mode)' );
		$this->registerOption( 'site-url', 'value', 'URL for this project (optional)' );
	}

	protected function execute() {
		$create = $this->getOption( 'create' );

		if ( $create ) {
			$this->create();
		} else {
			$this->update();

		}
	}

	protected function create() {
		$db = $this->getContext()->getDB();

		$id = $this->getOption( 'id' );
		$displayTitle = $this->getOption( 'display-title' );
		$password = $this->getOption( 'password' );
		$siteUrl = $this->getOption( 'site-url' );

		if ( !$id || !$displayTitle ) {
			$this->error( '--id and --display-title are required.' );
		}

		// Check if a project by this id doesn't exist already
		$row = $db->getOne(str_queryf( 'SELECT id FROM projects WHERE id = %s;', $id ));
		if ( $row ) {
			$this->error( 'Unable to create project, a project by that name exists already.' );
		}

		if ( !$password ) {
			$inputConfirm = null;
			$this->out( 'Enter password for this project (leave blank to abort):' );
			while ( ( $input = $this->cliInputSecret() ) !== '' && $input !== $inputConfirm ) {
				if ( !is_string( $inputConfirm ) ) {
					$inputConfirm = $input;
					$this->out( 'Re-enter password to confirm:' );
				} else {
					$inputConfirm = null;
					$this->out( 'Passwords don\'t match, please try again:' );
				}
			}
			if ( $input === '' ) {
				$this->error( 'Password is required.' );
			}
			$password = $input;
		}

		// Site url is optional
		if ( !$siteUrl ) {
			$siteUrl = '';
		}

		// Validate project id
		if ( !LoginAction::isValidName( $id ) ) {
			$this->error( 'Project ids may only contain lowercase a-z, 0-9 and dashes and must start with a letter.' );
		}

		// maxlength (otherwise MySQL will crop it)
		if ( strlen( $id ) > 255 ) {
			$this->error( 'Project ID has to be no longer than 255 characters.' );
		}
		if ( strlen( $displayTitle ) > 255 ) {
			$this->error( 'Display title has to be no longer than 255 characters.' );
		}

		// Create the project
		$authToken = LoginAction::generateRandomHash( 40 );
		$authTokenHash = sha1( $authToken );

		$isInserted = $db->query(str_queryf(
			'INSERT INTO projects
			(id, display_title, site_url, password, auth_token, updated, created)
			VALUES(%s, %s, %s, %s, %s, %s, %s);',
			$id,
			$displayTitle,
			$siteUrl,
			LoginAction::generatePasswordHash( $password ),
			$authTokenHash,
			swarmdb_dateformat( SWARM_NOW ),
			swarmdb_dateformat( SWARM_NOW )
		));

		if ( !$isInserted ) {
			$this->error( 'Insertion of row into database failed.' );
		}

		$this->out(
			'Project ' . $displayTitle . ' has been succesfully created!' . PHP_EOL
			. 'The following auth token has been generated for this project:' . PHP_EOL
			. PHP_EOL
			. "\t" . $authToken . PHP_EOL
			. PHP_EOL
			. 'You will need it to perform actions that require authentication.' . PHP_EOL
			. 'If you ever loose it, you can generate a new token with the refreshProjectToken.php script.'
		);
	}

	protected function update() {
		$db = $this->getContext()->getDB();


		$id = $this->getOption( 'id' );
		$displayTitle = $this->getOption( 'display-title' );
		$siteUrl = $this->getOption( 'site-url' );

		if ( !$id ) {
			$this->error( '--id is required.' );
		}

		// Check if this project exists.
		$field = $db->getOne(str_queryf( 'SELECT id FROM projects WHERE id = %s;', $id ));
		if ( !$field ) {
			$this->error( 'Project does not exist. Set --create to create a project.' );
		}

		if ( !$displayTitle && !$siteUrl ) {
			$this->error( 'Unable to perform update. No values provided.' );
		}

		if ( $displayTitle ) {
			$isUpdated = $db->query(str_queryf(
				'UPDATE projects SET display_title = %s, updated = %s WHERE id = %s;',
				$displayTitle,
				swarmdb_dateformat( SWARM_NOW ),
				$id
			));
			if ( !$isUpdated ) {
				$this->error( 'Failed to update database.' );
			}
		}

		if ( $siteUrl ) {
			$isUpdated = $db->query(str_queryf(
				'UPDATE projects SET site_url = %s, updated = %s WHERE id = %s;',
				$siteUrl,
				swarmdb_dateformat( SWARM_NOW ),
				$id
			));
			if ( !$isUpdated ) {
				$this->error( 'Failed to update database.' );
			}
		}

		$this->out( 'Project has been updated.' );
	}
}

$script = ManageProjectScript::newFromContext( $swarmContext );
$script->run();
