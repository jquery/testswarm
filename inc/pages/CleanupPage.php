<?php
/**
 * "Cleanup" page.
 *
 * @author John Resig, 2008-2011
 * @since 0.1.0
 * @package TestSwarm
 */
class CleanupPage extends Page {

	public function execute() {
		$action = CleanupAction::newFromContext( $this->getContext() );

		try {
			$action->doAction();

			if ( $action->getError() ) {
				$response = array(
					"error" => $action->getError(),
				);
			} else {
				$response = $action->getData();
			}

		} catch ( Exception $e ) {
			$response = array(
				"error" => array(
					"code" => "internal-error",
					"info" => "An internal error occurred. Action could not be performed. Error message:\n" . $e->getMessage(),
				),
			);
		}

		// This should really be in the API,
		// but until we have an actual API, we do it as a Page
		echo json_encode( $response );
		exit;
		#$this->setAction( $action );
		#$this->content = $this->initContent();
	}
}
