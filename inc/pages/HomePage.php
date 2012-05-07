<?php
/**
 * Default "Home" page.
 * The dashboard of the TestSwarm install.
 *
 * @author John Resig, 2008-2011
 * @author Timo Tijhof, 2012
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
		$conf = $this->getContext()->getConf();
		$request = $this->getContext()->getRequest();
		$browserInfo = $this->getContext()->getBrowserInfo();

		$siteNameHtml = htmlspecialchars( $conf->web->title );

		$this->setTitle( "Home" );
		$this->setRawDisplayTitle(
			'<div style="text-align: center;">' . $siteNameHtml . '</div>'
		);

		$html = '<div class="row">'
			. '<div class="span7">'
			. '<h3>Distributed Continuous Integration for JavaScript</h3>'
			. '<blockquote><p>'
			. str_replace( "$1", $siteNameHtml, $conf->customMsg->homeIntro_html )
			. '</p></blockquote>'
			. '</div>';

		$html .= '<div class="span5"><div class="well well-small">';
		if ( !$conf->client->requireRunToken ) {
			if ( $browserInfo->isInSwarmUaIndex() ) {
					$html .= '<p><strong>' . $siteNameHtml . ' needs your help!</strong><br>'
					. ' You have a browser that we need to test against, join the swarm to help us out!</p>';
				if ( !$request->getSessionData( "username" ) ) {
					$html .= '<form action="' . swarmpath( "" ) . '" method="get" class="form-horizontal">'
						. '<input type="hidden" name="action" value="run">'
						. '<label for="form-item">Username:</label>'
						. ' <input type="text" name="item" id="form-item" placeholder="Enter username..">'
						. ' <input type="submit" value="Join the swarm" class="btn btn-primary">'
						. '</form>';
				} else {
					$html .= '<p><a href="' . swarmpath( "run/{$request->getSessionData( "username" )}/" )
					. '" class="btn btn-primary btn-large">Join the swarm</a></p>';
				}
			} else {
				$browscap = $browserInfo->getBrowscap();
				$html .= '<div class="alert alert-info">'
					. '<h4 class="alert-heading">TestSwarm does not recognize your browser.</h4>'
					. '<p>Please join with one the below browsers.</p></div>'
					. '<p>If you feel that this may be an error, please report it to the TestSwarm '
					. ' <a href="https://github.com/jquery/testswarm/issues">Issue Tracker</a>,'
					. ' including the following 2 codes:'
					. '<br><strong><a href="http://browsers.garykeith.com/">browscap</a>:</strong> <code>'
					. htmlspecialchars( print_r( array(
							"Platform" => $browscap["Platform"],
							"Browser" => $browscap["Browser"],
							"Version" => $browscap["Version"],
							"MajorVer" => $browscap["MajorVer"],
							"MinorVer" => $browscap["MinorVer"],
					), true ) )
					. '</code><br><strong><a href="//en.wikipedia.org/wiki/User_agent" title="Read about User agent on Wikipedia!">User-Agent</a> string:</strong> <code>'
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


	/** @return bool: Whether the current user was found in the swarm */
	public function getBrowsersOnlineHtml() {
		$db = $this->getContext()->getDB();
		$browserInfo = $this->getContext()->getBrowserInfo();

		$data = $this->getAction()->getData();

		$html = "";

		$itemsPerRow = 6;

		$desktopHtml = '<h2>Desktop Browsers</h2>';
		$desktopItems = 0;
		$mobileHtml = '<h2>Mobile Browsers</h2>';
		$mobileItems = 0;

		foreach ( $data["userAgents"] as $uaID => $userAgent ) {
			$isCurr = $uaID == $browserInfo->getSwarmUaID();

			$item = ""
				. '<div class="span2">'
				. '<div class="well well-small swarm-browseronline' . ( $isCurr ? " alert-info" : "" ) . '">'

				. html_tag( "img", array(
					"src" => swarmpath( "img/" . $userAgent["data"]["displayicon"] . ".sm.png" ),
					"class" => "swarm-browsericon",
					"alt" => "",
					"title" => $userAgent["data"]["displaytitle"],
				) )
				. '<br>'

				. html_tag( "span", array(
					"class" => "badge swarm-browsername",
				), $userAgent["data"]["displaytitle"] )

				. '<br>'

				. html_tag( "span", array(
					"class" => "swarm-onlineclients " . (
						$userAgent["stats"]["onlineClients"] > 0
						 ? "badge"
						 : ( $userAgent["stats"]["pendingRuns"] > 0 ? "badge badge-error" : "badge" )
						),
					"title" => $userAgent["stats"]["onlineClients"] . ' clients online',
				), $userAgent["stats"]["onlineClients"] )

				. html_tag( "span", array(
					"class" => "swarm-pendingruns " . (
						$userAgent["stats"]["pendingRuns"] > 0
						 ? ( $userAgent["stats"]["onlineClients"] > 0 ? "label label-info" : "label label-warning" )
						 : "label label-success"
						)
				), $userAgent["stats"]["pendingRuns"] . ' pending runs' )

				. ( $userAgent["stats"]["pendingReRuns"] > 0
					? '<br>' . html_tag( "span", array(
						"class" => "swarm-pendingreruns " . (
							$userAgent["stats"]["onlineClients"] > 0 ? "label label-info" : "label label-warning"
							)
						), $userAgent["stats"]["pendingReRuns"] . ' pending re-runs' )
					: ""
				)

				. '</div>'
				. '</div>';

			if ( $userAgent["data"]["mobile"] ) {
				if ( $mobileItems % $itemsPerRow === 0 ) {
					$mobileHtml .= '<div class="row">';
				}
				$mobileItems += 1;

				$mobileHtml .= $item;

				if ( $mobileItems % $itemsPerRow === 0 ) {
					$mobileHtml .= '</div><!--/.row -->';
				}
			} else {
				if ( $desktopItems % $itemsPerRow === 0 ) {
					$desktopHtml .= '<div class="row">';
				}
				$desktopItems += 1;

				$desktopHtml .= $item;

				if ( $desktopItems % $itemsPerRow === 0 ) {
					$desktopHtml .= '</div><!--/.row -->';
				}
			}
		}

		// Close un-even items rows
		if ( $mobileItems % $itemsPerRow !== 0 ) {
			$mobileHtml .= '</div><!--/.row -->';
		}
		if ( $desktopItems % $itemsPerRow !== 0 ) {
			$desktopHtml .= '</div><!--/.row -->';
		}

		$html .= $desktopHtml . $mobileHtml;

		return $html;
	}

}

