<?php
/**
 * "Saverun" page.
 *
 * @since 0.1.0
 * @package TestSwarm
 */
class SaverunPage extends Page {

	public function execute() {
		$action = SaverunAction::newFromContext( $this->getContext() );

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
		$this->saveRunActionResponse( $response );
		exit;
		#$this->setAction( $action );
		#$this->content = $this->initContent();
	}

	public function saveRunActionResponse( $response ) {
		/**
		 * action=saverun is used in 3 scenarios:
		 *
		 * - A modern browser is viewing action=run&item=username,
		 *   running tests in an iframe with a test suite and inject.js,
		 *   the test suite is done and uses postMessage to contact the parent frame where a
		 *   handler from run.js takes it, and fires AJAX request to action=saverun.
		 *
		 * - An old browser is running tests like above but has no postMessage support.
		 *   In that case inject.js will build a <form> that POSTs to action=saverun,
		 *   The reponse of the form submission will still be in the iframe.
		 *
		 * - In either an old or a new browser, if a test times out something in run.js
		 *   will make an ajax request here to report the time out failure
		 *
		 * In the first and last case we should respond with JSON, becuase that's what the
		 * handlers expect. If the response is valid JSON, it will call SWARM.runDone() (or
		 * something like it) and continue.
		 * In the second case we want to output a little bit of HTML, that will contact the
		 * parent frame to let it know that the form submission completed and it should
		 * continue on.
		 */
		if ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] )
			&& strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest'
		) {
			echo json_encode( $response );
		} else {
			echo '<script>window.parent.SWARM.runDone();</script>';
		}

	}
}

