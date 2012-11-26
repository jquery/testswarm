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
		$browserInfo = $this->getContext()->getBrowserInfo();
		$conf = $this->getContext()->getConf();
		$request = $this->getContext()->getRequest();

		$uaData = $browserInfo->getUaData();

		$runToken = null;
		if ( $conf->client->requireRunToken ) {
			$runToken = $request->getVal( "run_token" );
			if ( !$runToken ) {
				throw new SwarmException( "This swarm has restricted access to join the swarm." );
			}
		}

		$this->setTitle( "Test runner" );
		$this->bodyScripts[] = swarmpath( "js/run.js?" . time() );

		$client = Client::newFromContext( $this->getContext(), $runToken );
		$displayInfo = $uaData->displayInfo;

		$html = '<script>'
			. 'SWARM.client_id = ' . json_encode( $client->getClientRow()->id ) . ';'
			. 'SWARM.run_token = ' . json_encode( $runToken ) . ';'
			. '</script>';

		$html .=
			'<div class="row">'
				. '<div class="span2">'
					. '<div class="well well-swarm-icon">'
					. html_tag( 'div', array(
						'class' => $displayInfo['class'],
						'title' => $displayInfo['title'],
					) )
					. '<br>'
					. html_tag_open( 'span', array(
						'class' => 'badge swarm-browsername',
					) ) . $displayInfo['labelHtml'] . '</span>'
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
