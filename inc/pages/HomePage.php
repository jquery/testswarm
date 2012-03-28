<?php
/**
 * Default "Home" page.
 * The dashboard of the TestSwarm install.
 *
 * @since 0.1.0
 * @package TestSwarm
 */

class HomePage extends Page {

	var $userHasKnownUA = false;

	protected function initContent() {
		$request = $this->getContext()->getRequest();
		$browserInfo = $this->getContext()->getBrowserInfo();

		$this->setTitle( "Distributed Continuous Integration for JavaScript" );
		$this->headScripts[] = swarmpath( "js/jquery.js" );

		$html = '<blockquote>Welcome to the TestSwarm Alpha! Please be aware that'
			. ' TestSwarm is still under heavy testing and during this alpha period'
			. ' data may be lost or corrupted and clients may be unexpectedly'
			. ' disconnected. More information about TestSwarm can be found'
			. ' <a href="//github.com/jquery/testswarm/wiki">on the TestSwarm wiki</a>.</blockquote>';

		$html .= $this->getBrowsersOnlineHtml( "Desktop Browsers", /*isMobile=*/0 );
		$html .= $this->getBrowsersOnlineHtml( "Mobile Browsers", /*isMobile=*/1 );

		if ( $this->userHasKnownUA ) {
			$html .= '<div class="join">'
				. '<p><strong>TestSwarm needs your help!</strong>'
				. ' You have a browser that we need to test against, join the swarm to help us out!</p>';
			if ( !$request->getSessionData( "username" ) ) {
				$html .= '<form action="" method="get">'
					. '<input type="hidden" name="action" value="run">'
					. '<br><strong>Username:</strong><br>'
					. '<input type="text" name="item" value="">'
					. ' <input type="submit" value="Join the Swarm">'
					. '</form>';
			} else {
				$html .= '<br><p><strong>&raquo; ' . htmlspecialchars( $request->getSessionData( "username" ) )
				. '</strong> <a href="' . swarmpath( "run/{$request->getSessionData( "username" )}/" )
				. '">Start Running Tests</a></p>';
			}
			$html .= '</div>';
		} else {
			$html .= '<div class="join">'
				. '<p>TestSwarm currently does not recognize your browser. If you wish to'
				. ' help run tests you should join with one the below browsers.</p>'
				. '<p>If you feel that this may be a mistake, please report it to the TestSwarm'
				. ' <a href="https://github.com/jquery/testswarm/issues">Issue Tracker</a>'
				. ' and include the following information:<br><code>clientprofile: '
				. htmlspecialchars(
					$browserInfo->getBrowserCodename()
					. '/' . $browserInfo->getBrowserVersion()
					. '/' . $browserInfo->getOsCodename()
				)
				. '</code><br><code><a href="http://useragentstring.com/">useragent string</a>: '
				. htmlspecialchars( $browserInfo->getRawUA() )
				. '</p></div>';
		}

		return $html;
	}


	/** @return bool: Whether the current user was found in the swarm */
	function getBrowsersOnlineHtml( $headingTitle, $isMobile = 0 ) {
		$bi = $this->getContext()->getBrowserInfo();
		$db = $this->getContext()->getDB();

		$html = "";

		$rows = $db->getRows(str_queryf(
			"SELECT
				useragents.engine as engine,
				useragents.name as name,
				(
					SELECT COUNT(*)
					FROM clients
					WHERE	clients.useragent_id = useragents.id
					AND clients.updated > %u
				) as clients,
				(engine=%s AND %s REGEXP version) as found
			FROM
				useragents
			WHERE	active = 1
			AND	mobile = %s
			ORDER BY engine, name;",
			swarmdb_dateformat( strtotime( '1 minute ago' ) ),
			$bi->getBrowserCodename(),
			$bi->getBrowserVersion(),
			$isMobile
		));

		$prevEngine = null;

		$html .= "<div class=\"browsers\"><h3>$headingTitle</h3>";

		foreach ( $rows as $row ) {
			if ( $row->found ) {
				$this->userHasKnownUA = true;
			}

			if ( $row->engine != $prevEngine ) {
				$html .= '<br style="clear: both;">';
			}
			$namePart = preg_replace( "/\w+ /", "", $row->name );
			$onlineCount = $row->clients;

			$html .= '<div class="browser' . ( $row->engine != $prevEngine ? " clear" : "" ) . ( $row->found ? " you" : "" ) . '">'
				. '<img src="' . swarmpath( "images/{$row->engine}.sm.png" )
				. '" class="browser-icon ' . htmlspecialchars( $row->engine )
				. '" alt="' . htmlspecialchars( $row->name )
				. '" title="' . htmlspecialchars( $row->name )
				. '">'
				. '<span class="browser-name">' . htmlspecialchars( $namePart ) . '</span>';

			if ( $onlineCount > 0 ) {
				$html .= '<span class="active">' . $onlineCount . '</span>';
			}
			$html .= '</div>';

			$prevEngine = $row->engine;
		}

		$html .= '</div>';

		return $html;
	}

}

