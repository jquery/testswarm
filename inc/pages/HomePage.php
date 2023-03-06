<?php
/**
 * Default dashboard for the TestSwarm front-end.
 *
 * @author John Resig
 * @author Timo Tijhof
 * @since 0.1.0
 * @package TestSwarm
 */
class HomePage extends Page {

	var $userHasKnownUA = false;

	public function execute() {
		$action = SwarmstateAction::newFromContext( $this->getContext() );
		$action->doAction();

		$this->setAction( $action );
		$this->content = $this->initContent();
	}

	protected function initContent() {
		$context = $this->getContext();
		$conf = $context->getConf();
		$request = $context->getRequest();
		$browserInfo = $context->getBrowserInfo();

		$siteNameHtml = htmlspecialchars( $conf->web->title );

		$this->setTitle( 'Home' );
		$this->setRawDisplayTitle(
			'<div style="text-align: center;">' . $siteNameHtml . '</div>'
		);

		$html = '<div class="row">'
			. '<div class="span7">'
			. '<h3>Distributed Continuous Integration for JavaScript</h3>'
			. '<blockquote><p>'
			. str_replace( '$1', $siteNameHtml, $conf->customMsg->homeIntro_html )
			. '</p></blockquote>'
			. '</div>';

		$html .= '<div class="span5"><div class="well">';
		if ( !$conf->client->runTokenHash ) {
			if ( $browserInfo->isInSwarmUaIndex() ) {
					$auth = $context->getAuth();
					$suggestedClientName = $auth ? $auth->project->id : '';
					$html .= '<p>Your browser is in our index, run some tests!</p>'
					. '<form action="' . swarmpath( '' ) . '" method="get" class="form-horizontal swarm-form-join">'
					. '<input type="hidden" name="action" value="run">'
					. '<div class="input-append">'
					. '<input type="text" name="item" placeholder="Enter name.." value="' . htmlspecialchars( $suggestedClientName ) . '" required pattern="' . htmlspecialchars( Client::getNameValidationRegex() ) . '">'
					. '<input type="submit" value="Join the swarm" class="btn btn-primary">'
					. '</div>'
					. '</form>';
			} else {
				$uaData = $browserInfo->getUaData();
				unset( $uaData->displayInfo );
				$html .= '<div class="alert alert-info">'
					. '<h4 class="alert-heading">Your browser is not needed by this swarm.</h4>'
					. '<p>Please join with one the below browsers.</p></div>'
					. '<p>If you feel that this may be an error, please report it to the TestSwarm '
					. ' <a href="https://github.com/jquery/testswarm/issues">Issue Tracker</a>,'
					. ' including the following 2 codes:'
					. '<br><strong><a href="https://github.com/tobie/ua-parser">ua-parser</a>:</strong> <code>'
					. htmlspecialchars( print_r( $uaData, true ) )
					. '</code><br><strong><a href="https://en.wikipedia.org/wiki/User_agent" title="Read about User agent on Wikipedia!">User-Agent</a> string:</strong> <code>'
					. htmlspecialchars( $browserInfo->getRawUA() )
					. '</code></p>';
			}
		} else {
			$html .= '<div class="alert">'
				. '<h4 class="alert-heading">Join access restricted</h4>'
				. '<p>Public joining of the swarm has been disabled.</p>'
				. '<button type="button" class="btn btn-large disabled" disabled><s>Join the swarm</s></button>'
				. '</div>';
		}
		$html .= '</div></div>';
		$html .= '</div>';

		$html .= $this->getBrowsersOnlineHtml();

		return $html;
	}


	/** @return string HTML */
	public function getBrowsersOnlineHtml() {
		$conf = $this->getContext()->getConf();
		$db = $this->getContext()->getDB();
		$browserInfo = $this->getContext()->getBrowserInfo();

		$data = $this->getAction()->getData();

		$html = '';

		$itemsPerRow = 6;

		$browsersHtml = '<h2>State of the Swarm</h2>';
		$browserItemCount = 0;

		foreach ( $data['userAgents'] as $uaID => $userAgent ) {
			$isCurr = $uaID == $browserInfo->getSwarmUaID();

			$displayInfo = $userAgent['data']['displayInfo'];

			$item = ''
				. '<div class="span2">'
				. '<div class="well well-swarm-icon' . ( $isCurr ? ' alert-info' : '' ) . '">'

				. html_tag( 'div', array(
					'class' => $displayInfo['class'],
					'title' => $displayInfo['title'],
				) )
				. '<br>'

				. html_tag_open( 'span', array(
					'class' => 'label swarm-browsername',
				) ) . $displayInfo['labelHtml'] . '</span>'

				. '<br>'

				. html_tag( 'span', array(
					'class' => 'swarm-onlineclients ' . (
						$userAgent["stats"]["onlineClients"] > 0
						 ? "badge"
						 : ( $userAgent['stats']['pendingRuns'] > 0 ? 'badge badge-important' : 'badge' )
						),
					"title" => $userAgent["stats"]["onlineClients"] . ' clients online',
				), $userAgent["stats"]["onlineClients"] )

				. html_tag( "span", array(
					"class" => "swarm-pendingruns " . (
						$userAgent["stats"]["pendingRuns"] > 0
						 ? ( $userAgent["stats"]["onlineClients"] > 0 ? "badge badge-info" : "badge badge-warning" )
						 : "badge badge-success"
						)
				), $userAgent["stats"]["pendingRuns"] . ' runs' )

				. ( $userAgent["stats"]["pendingReRuns"] > 0
					? ' ' . html_tag( "span", array(
						"class" => "swarm-pendingreruns " . (
							$userAgent["stats"]["onlineClients"] > 0 ? "badge badge-info" : "badge badge-warning"
							)
						), $userAgent["stats"]["pendingReRuns"] . ' re-runs' )
					: ""
				)

				. '</div>'
				. '</div>';

			// Properly close and start new rows
			if ( $browserItemCount % $itemsPerRow === 0 ) {
				$browsersHtml .= '<div class="row">';
			}
			$browserItemCount += 1;
			$browsersHtml .= $item;
			if ( $browserItemCount % $itemsPerRow === 0 ) {
				$browsersHtml .= '</div><!--/.row -->';
			}
		}

		// Close un-even items rows
		if ( $browserItemCount % $itemsPerRow !== 0 ) {
			$browsersHtml .= '</div><!--/.row -->';
		}

		if ( $browserItemCount === 0 ) {
			$browsersHtml .= '<p><em>This swarm is empty!</em></p>';
		}

		$html .= $browsersHtml;

		return $html;
	}

}

