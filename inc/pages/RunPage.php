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
			'<div class="userinfo">'
				. '<div class="browser you">'
					. '<img src="' . swarmpath( "images/{$bi->getBrowserCodename()}.sm.png" )
						. '" class="browser-icon ' . $bi->getBrowserCodename()
						. '" alt="' . $bi->getSwarmUserAgentName()
						. '" title="' . $bi->getSwarmUserAgentName() . '">'
					. '<span class="browser-name">' . htmlspecialchars( $bi->getSwarmUserAgentVersion() ) . '</span>'
				. '</div>'
				. '<h3>' . htmlspecialchars( $client->getUserRow()->name ) . '</h3>'
				. '<p><strong>Status:</strong> <span id="msg"></span></p>'
			. '</div>'
			. '<div class="userinfo">'
				. '<h3>History</h3>'
				. '<ul id="history"></ul>'
			. '</div>'
			. '<div id="iframes"></div>';

		return $html;
	}
}
