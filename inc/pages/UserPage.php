<?php
/**
 * "User" page.
 *
 * @author John Resig, 2008-2011
 * @author Jörn Zaefferer, 2012
 * @since 0.1.0
 * @package TestSwarm
 */
class UserPage extends Page {
	public function execute() {
		$action = UserAction::newFromContext( $this->getContext() );
		$action->doAction();

		$this->setAction( $action );
		$this->content = $this->initContent();
	}

	protected function initContent() {

		$this->setTitle( "User" );
		$this->bodyScripts[] = swarmpath( "js/pretty.js" );
		$this->bodyScripts[] = swarmpath( "js/user.js" );

		$html = "";

		$error = $this->getAction()->getError();
		$data = $this->getAction()->getData();
		if ( $error ) {
			$html .= html_tag( "div", array( "class" => "alert alert-error" ), $error["info"] );
			return $html;
		}

		$this->setSubTitle( $data["userName"] );

		if ( count( $data["activeClients"] ) ) {

			$html .= '<h2>Active clients</h2><div class="row">';

			foreach ( $data["activeClients"] as $activeClient ) {
				if ( $activeClient["uaData"] ) {
					$diplayicon = $activeClient["uaData"]["displayicon"];
					$label = $activeClient["uaData"]["displaytitle"];
				} else {
					$diplayicon = "unknown";
					$label = "Unrecognized [{$activeClient["uaID"]}]";
				}
				$html .=
					'<div class="span4"><div class="well">'
					. '<img class="pull-right" src="' . swarmpath( "img/{$diplayicon}.sm.png" ) . '" alt="">'
					. '<strong class="label">' . htmlspecialchars( $label ) . '</strong>'
					. '<p><small>Platform: ' . htmlspecialchars( $activeClient["uaBrowscap"]["Platform"] )
					. '</small><br><small>Connected <span title="'
					. htmlspecialchars( $activeClient["connectedISO"] ) . '" class="pretty">'
					. htmlspecialchars( $activeClient["connectedLocalFormatted"] ) . '</small></p>'
					. '</div></div>';
			}

			$html .= '</div>';

		}

		if ( count( $data["recentJobs"] ) ) {

			$html .= '<h2>Recent jobs</h2><table class="table table-bordered swarm-results">';

			// Build table header
			$html .= '<thead><tr><td></td>';
			foreach ( $data["uasInJobs"] as $uaID => $uaData ) {
				$html .= '<th>' .
					'<img src="' . swarmpath( "img/{$uaData["displayicon"]}.sm.png" )  .
					'" class="swarm-browsericon' .
					'" alt="' . htmlspecialchars( $uaData["displaytitle"] ) .
					'" title="' . htmlspecialchars( $uaData["displaytitle"] ) .
					'"><br>' .
					htmlspecialchars( preg_replace( "/\w+ /", "", $uaData["displaytitle"] ) ) .
					'</th>';
			}
			$html .= '</tr></thead><tbody>';

			foreach ( $data["recentJobs"] as $job ) {

				$html .= '<tr><th><a href="' . htmlspecialchars( $job["url"] ) . '">' . htmlspecialchars( strip_tags( $job["name"] ) ) . "</a></th>\n";

				foreach ( $data["uasInJobs"] as $uaID => $uaData ) {
					$html .= isset( $job["uaSummary"][$uaID] )
						? '<td class="status-' . $job["uaSummary"][$uaID] . '"></td>'
						: '<td class="status-notscheduled"></td>';
				}

				$html .= '</tr>';
			}

			$html .= '</tbody></table>';
		}

		if ( $html == '' ) {
			return '<div class="alert alert-info">No active useragents or jobs.</div>';
		}

		return $html;
	}

}
