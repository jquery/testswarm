<?php
/**
 * Refresh project token
 *
 * @author Timo Tijhof, 2012-2013
 * @since 1.0.0
 * @package TestSwarm
 */
define( 'SWARM_ENTRY', 'SCRIPT' );
require_once __DIR__ . '/../inc/init.php';

class RefreshProjectTokenScript extends MaintenanceScript {

	protected function init() {
		$this->setDescription(
			'Refresh the authentication token of a project. Invalides the current token and returns an newly generated one.'
		);
		$this->registerOption( 'id', 'value', 'ID of project.' );
		$this->registerOption( 'quick', 'boolean', 'Skip confirmation.' );
	}

	protected function execute() {
		$db = $this->getContext()->getDB();

		$id = $this->getOption( 'id' );

		// Verify parameters
		if ( !$id ) {
			$this->error( '--id is required.' );
		}

		$checkId = $db->getOne(str_queryf(
			'SELECT
				id
			FROM projects
			WHERE id = %s;',
			$id
		));

		if ( !$checkId || $checkId !== $id ) {
			$this->error( 'Project "' . $id . '" does not exist.' );
		}

		if ( !$this->getOption( 'quick' ) ) {
			$this->timeWarningForScriptWill( 'invalidate the existing token' );
		}

		// New token
		$authToken = LoginAction::generateRandomHash( 40 );
		$authTokenHash = sha1( $authToken );

		$isUpdated = $db->query(str_queryf(
			'UPDATE projects
			SET
				auth_token = %s
			WHERE id = %s
			LIMIT 1;',
			$authTokenHash,
			$id
		));
		if ( !$isUpdated ) {
			$this->error( 'Updating of row into database failed.' );
		}

		$this->out(
			'Authentication token of project "' . $id . '" has been succesfully refreshed!' . PHP_EOL
			. 'The following auth token has been generated for this project:' . PHP_EOL
			. $authToken . PHP_EOL . PHP_EOL
		);
	}
}

$script = RefreshProjectTokenScript::newFromContext( $swarmContext );
$script->run();
