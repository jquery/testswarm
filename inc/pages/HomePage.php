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

	protected function initContent() {
		$request = $this->getContext()->getRequest();
		$browserInfo = $this->getContext()->getBrowserInfo();

		$this->setTitle( "Home" );
		$this->setRawDisplayTitle(
			'<p style="text-align: center;"><img src="' . swarmpath( "images/testswarm_logo_wordmark.png" ) . '" alt="TestSwarm logo"></p>'
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

		$browserSections = $this->getBrowsersOnlineHtml( "Desktop Browsers", /*isMobile=*/0 );
		$browserSections .= $this->getBrowsersOnlineHtml( "Mobile Browsers", /*isMobile=*/1 );

		$html .= '<div class="span5"><div class="well">';
		if ( $this->userHasKnownUA ) {
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
				$html .= '<br><p><strong>&raquo; ' . htmlspecialchars( $request->getSessionData( "username" ) )
				. '</strong> <a href="' . swarmpath( "run/{$request->getSessionData( "username" )}/" )
				. '">Start Running Tests</a></p>';
			}
		} else {
			$html .= '<p>TestSwarm currently does not recognize your browser. If you wish to'
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
				. '</p>';
		}
		$html .= '</div></div>';
		$html .= '</div>';

		$html .= $browserSections;

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

		$html .= '<h2>' . htmlspecialchars( $headingTitle ) . '</h2>';
		$html .= '<div class="thumbnails">';

		foreach ( $rows as $row ) {
			if ( $row->found ) {
				$this->userHasKnownUA = true;
			}

			$namePart = preg_replace( "/\w+ /", "", $row->name );
			$onlineCount = $row->clients;

			$html .= '<div class="well span1 pagination-centered swarm-browseronline' . ( $row->found ? " alert-info" : "" ) . '">'
				. '<div class="thumbnail">'
				. '<img src="' . swarmpath( "images/{$row->engine}.sm.png" ) . '"'
				. ' class="swarm-browsericon ' . htmlspecialchars( $row->engine ) . '"'
				. ' alt="' . htmlspecialchars( $row->name ) . '"'
				. ' title="' . htmlspecialchars( $row->name ) . '"'
				. '>';
			if ( $onlineCount > 0 ) {
				$html .= '<span class="badge badge-error">' . $onlineCount . '</span>';
			}
			$html .= '</div>';
			$html .= '<span class="label">' . htmlspecialchars( $namePart ) . '</span>';
			$html .= '</div>';

			$prevEngine = $row->engine;
		}

		$html .= '</div>';

		return $html;
	}

}

