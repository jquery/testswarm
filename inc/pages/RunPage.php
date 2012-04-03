<?php
/**
 * "Run" page.
 *
 * @author John Resig, 2008-2011
 * @since 0.1.0
 * @package TestSwarm
 */

class RunPage extends Page {

	protected function initContent() {
		$bi = $this->getContext()->getBrowserInfo();
		$request = $this->getContext()->getRequest();

		$this->setTitle( "Test runner" );
		$this->bodyScripts[] = swarmpath( "js/jquery.js" );
		$this->bodyScripts[] = swarmpath( "js/run.js?" . time() );

		$client = Client::newFromContext( $this->getContext() );

		$html = '<script>window.SWARM.client_id = ' . json_encode( $client->getClientRow()->id ) . ';</script>';

		$html .=
			'<div class="row">'
				. '<div class="span2">'
					. '<div class="well pagination-centered thumbnail">'
					. '<img src="' . swarmpath( "images/{$bi->getBrowserCodename()}.sm.png" )
						. '" class="swarm-browsericon ' . $bi->getBrowserCodename()
						. '" alt="' . $bi->getSwarmUserAgentName()
						. '" title="' . $bi->getSwarmUserAgentName() . '">'
					. '<span class="label">' . htmlspecialchars( $bi->getSwarmUserAgentVersion() ) . '</span>'
					. '</div>'
				. '</div>'
				. '<div class="span7">'
					. '<h2>' . htmlspecialchars( $client->getUserRow()->name ) . '</h2>'
					. '<p><strong>Status:</strong> <span id="msg"></span></p>'
				. '</div>'
			. '</div>'
			. '<div class="well">'
				. '<h3>History</h3>'
				. '<ul id="history"></ul>'
			. '</div>'
			. '<div id="iframes"></div>';

		return $html;
	}
}
