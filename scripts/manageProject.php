<?php
/**
 * Manage project.
 *
 * @author Timo Tijhof
 * @since 1.0.0
 * @package TestSwarm
 */
define( 'SWARM_ENTRY', 'SCRIPT' );
require_once __DIR__ . '/../inc/init.php';

class ManageProjectScript extends MaintenanceScript {

	protected function init() {
		$this->setDescription(
			'Create or update a TestSwarm project.'
		);
		$this->registerOption( 'create', 'boolean', 'Pass this to the create if it doesn\'t exist.' );
		$this->registerOption( 'delete', 'boolean', 'Pass this to remove a project and recursively delete all its jobs and runs.' );
		$this->registerOption( 'id', 'value', 'ID of project (must be in format: "' . LoginAction::getNameValidationRegex() . '").' );
		$this->registerOption( 'display-title', 'value', 'Display title (free form text, max: 255 chars)' );
		$this->registerOption( 'password', 'value', 'Password for this project (omit to enter in interactive mode)' );
		$this->registerOption( 'site-url', 'value', 'URL for this project (optional)' );
	}

	protected function execute() {
		if ( $this->getOption( 'create' ) ) {
			$this->create();
		} elseif ( $this->getOption( 'delete' ) ) {
			$this->delete();
		} else {
			$this->update();
		}
	}

	protected function create() {
		$action = ProjectAction::newFromContext( $this->getContext() );

		$id = $this->getOption( 'id' );
		$displayTitle = $this->getOption( 'display-title' );
		$password = $this->getOption( 'password' );
		$siteUrl = $this->getOption( 'site-url' );

		if ( !$id || !$displayTitle ) {
			$this->error( '--id and --display-title are required.' );
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

		$data = $action->create( $id, array(
			'password' => $password,
			'displayTitle' => $displayTitle,
			'siteUrl' => $siteUrl,
		) ) ;
		$error = $action->getError();

		if ( $error ) {
			$this->error( $error['info'] );
		}

		$this->out(
			'Project ' . $displayTitle . ' has been successfully created!' . PHP_EOL
			. 'The following auth token has been generated for this project:' . PHP_EOL
			. PHP_EOL
			. "\t" . $data['authToken'] . PHP_EOL
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

	public function delete() {
		$db = $this->getContext()->getDB();

		$projectID = $this->getOption( 'id' );
		if ( !$projectID ) {
			$this->error( '--id is required.' );
		}

		// Ensure the project exists.
		$field = $db->getOne(str_queryf( 'SELECT id FROM projects WHERE id = %s;', $projectID ));
		if ( !$field ) {
			$this->error( 'Project does not exist.' );
		}

		$this->out( "Are you sure you want to remove project '$projectID' and all associated jobs and runs? (Y/N)" );
		$answer = $this->cliInput();
		if ( $answer !== 'Y' ) {
			$this->error( 'Aborted.' );
			return;
		}

		$batchSize = 100;
		$stats = array();
		while ( true ) {
			$jobRows = $db->getRows(str_queryf(
				'SELECT id
				FROM jobs
				WHERE project_id = %s
				ORDER BY id
				LIMIT %u;',
				$projectID,
				$batchSize
			));
			if ( !$jobRows ) {
				// Done
				break;
			}
			$jobIDs = array_map( function ( $row ) { return $row->id; }, $jobRows );
			$this->out( '...deleting ' . count( $jobIDs ) . ' jobs' );
			$action = WipejobAction::newFromContext( $this->getContext() );
			$result = $action->doWipeJobs( 'delete', $jobIDs, $batchSize );
			$this->mergeStats( $stats, $result );
		}
		foreach ( $stats as $key => $val ) {
			$this->out( "deleted $key rows: $val");
		}

		$this->out( '' );
		$this->out( '...deleting project credentials' );
		$db->query(str_queryf( 'DELETE FROM projects WHERE id = %s;', $projectID ));

		$this->out( 'project has been deleted.' );

		$this->out( '' );
		$this->out( 'Done!' );
	}

	protected function mergeStats( array &$target, array $add ) {
		foreach ( $add as $key => $val ) {
			if ( isset( $target[$key] ) ) {
				$target[$key] += $val;
			} else {
				$target[$key] = $val;
			}
		}
	}
}

$script = ManageProjectScript::newFromContext( $swarmContext );
$script->run();
