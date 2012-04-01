<?php
/**
 * "Saverun" page.
 *
 * @author Timo Tijhof, 2012
 * @since 0.1.0
 * @package TestSwarm
 */
class SaverunPage extends Page {

	public function execute() {
		$action = SaverunAction::newFromContext( $this->getContext() );
		$action->doAction();

		$this->setAction( $action );
		parent::execute();
	}

	protected function initContent() {
		$request = $this->getContext()->getRequest();
		/**
		 * action=saverun is used in 3 scenarios:
		 *
		 * - RunPage embeds a testsuite in <iframe> that loads inject.js, uses postMessage
		 *   to contact handler in run.js and fires AJAX request to api.php?action=saverun.
		 *
		 * - RunPage embeds a testsuite in <iframe> that loads inject.js, postMessage not supported,
		 *   builds a <form> that POSTs to SaverunPage (this page).
		 *
		 * - RunPage embeds a testsuite that times out.
		 *   Handler in run.js fires AJAX request to api.php?action=saverun reporting the time out.
		 *
		 * In the first and last case api.php handles the the request.
		 * In the second case we can't use the API cross-domain, so we cross-domain submit a form,
		 * and then output a bit of HTML contacting the parent frame to as a "callback" to continue
		 * the test runner loop.
		 */
		$script =
			'<script>'
			. 'if ( window.parent !== window && window.parent.SWARM.runDone ) {'
			. 'window.parent.SWARM.runDone();'
			. '}'
			. '</script>';

		$html = '<p>This page is used as cross-domain form submission target in the fallback saving method for browsers'
			. ' that don\'t support postMessage().</p>';

		if ( $request->wasPosted() ) {
			if ( $this->getAction()->getData() === "ok" ) {
				$this->setTitle( "Saved run!" );
				return $script . $html;
			}

			$this->setTitle( "Saving run failed." );
			return $script . $html;
		}

		// If someone visits SaverunPage directly,
		// just show an informative message.
		$this->setTitle( 'Save run' );
		return $html;
	}
}

