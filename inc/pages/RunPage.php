<?php
/**
 * Join the swarm and run tests.
 *
 * @author John Resig
 * @since 0.1.0
 * @package TestSwarm
 */
class RunPage extends Page {

	protected function initContent() {
		$browserInfo = $this->getContext()->getBrowserInfo();
		$conf = $this->getContext()->getConf();
		$request = $this->getContext()->getRequest();

		$this->setTitle( 'Test runner' );

		$runToken = null;

		if ( $conf->client->requireRunToken ) {
			$runToken = $request->getVal( "run_token" );
			if ( !$runToken ) {
				return '<div class="alert alert-error">This swarm has restricted access to join the swarm.</div>';
			}
		}

		$this->bodyScripts[] = swarmpath( "js/run.js?" . time() );

		$client = Client::newFromContext( $this->getContext(), $runToken );

		$html = '<script>'
			. 'SWARM.client_id = ' . json_encode2( $client->getClientRow()->id ) . ';'
			. 'SWARM.run_token = ' . json_encode2( $runToken ) . ';'
			. '</script>';

		$html .=
			'<div class="row">'
				. '<div class="span2">'
					. $browserInfo->getIconHtml()
				. '</div>'
				. '<div class="span7">'
					. '<h2>' . htmlspecialchars( $client->getClientRow()->name ) . '</h2>'
					. '<p><strong>Status:</strong> <span id="msg"></span></p>'
				. '</div>'
			. '</div>'
			. '<div id="iframes"></div>'
			. '<div class="well">'
				. '<h3>History</h3>'
				. '<ul id="history"></ul>'
			. '</div>';

		return $html;
	}
}
