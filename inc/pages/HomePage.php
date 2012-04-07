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
		$request = $this->getContext()->getRequest();
		$browserInfo = $this->getContext()->getBrowserInfo();

		$this->setTitle( "Home" );
		$this->setRawDisplayTitle(
			'<p style="text-align: center;"><img src="' . swarmpath( "img/testswarm_logo_wordmark.png" ) . '" alt="TestSwarm logo"></p>'
		);
		$this->headScripts[] = swarmpath( "js/jquery.js" );

		$html = '<div class="row">'
			. '<div class="span7">'
			. '<h3>Distributed Continuous Integration for JavaScript</h3>'
			. '<blockquote>Welcome to the TestSwarm Alpha! Please be aware that'
			. ' TestSwarm is still under heavy testing and during this alpha period'
			. ' data may be lost or corrupted and clients may be unexpectedly'
			. ' disconnected. More information about TestSwarm can be found'
			. ' <a href="//github.com/jquery/testswarm/wiki">on the TestSwarm wiki</a>.</blockquote>'
			. '</div>';

		$html .= '<div class="span5"><div class="well">';
		if ( $browserInfo->isInSwarmUaIndex() ) {
				$html .= '<p><strong>TestSwarm needs your help!</strong><br>'
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
			$html .= '<div class="alert alert-info">TestSwarm currently does not recognize your browser. If you wish to'
				. ' help run tests you should join with one the below browsers.</div>'
				. '<p>If you feel that this may be a mistake, please report it to the TestSwarm'
				. ' <a href="https://github.com/jquery/testswarm/issues">Issue Tracker</a>'
				. ' and include the following information:</p><p><strong>browscap:</strong> <code>'
				. htmlspecialchars( print_r( array(
						"Platform" => $browscap["Platform"],
						"Browser" => $browscap["Browser"],
						"Version" => $browscap["Version"],
						"MajorVer" => $browscap["MajorVer"],
						"MinorVer" => $browscap["MinorVer"],
				), true ) )
				. '</code></p><p><strong><a href="http://useragentstring.com/">useragent string</a>:</strong> <code>'
				. htmlspecialchars( $browserInfo->getRawUA() )
				. '</code></p>';
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

		$desktopHtml = '<h2>Desktop Browsers</h2><div class="row">';
		$mobileHtml = '<h2>Mobile Browsers</h2><div class="row">';

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
				. '</div>'
				. '</div>';

			if ( $userAgent["data"]["mobile"] ) {
				$mobileHtml .= $item;
			} else {
				$desktopHtml .= $item;
			}
		}

		$desktopHtml .= '</div>';
		$mobileHtml .= '</div>';

		$html .= $desktopHtml . $mobileHtml;

		return $html;
	}

}

