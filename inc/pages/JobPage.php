<?php
/**
 * "Job" page.
 *
 * @author John Resig, 2008-2011
 * @author Jörn Zaefferer, 2012
 * @since 0.1.0
 * @package TestSwarm
 */

class JobPage extends Page {

	public function execute() {
		$action = JobAction::newFromContext( $this->getContext() );
		$action->doAction();

		$this->setAction( $action );
		$this->content = $this->initContent();
	}

	protected function initContent() {
		$request = $this->getContext()->getRequest();

		$this->setTitle( "Job status" );
		$this->bodyScripts[] = swarmpath( "js/jquery.js" );
		$this->bodyScripts[] = swarmpath( "js/job.js" );

		$error = $this->getAction()->getError();
		$data = $this->getAction()->getData();
		$html = '';

		if ( $error ) {
			$html .= html_tag( 'div', array( 'class' => 'errorbox' ), $error['info'] );
		}

		if ( !isset( $data["jobInfo"] ) ) {
			return $html;
		}

		$this->setSubTitle( '#' . $data["jobInfo"]["id"] );

		$html .=
			'<h3>' . htmlspecialchars( $data["jobInfo"]["name"] ) .'</h3>'
			. '<p><em>Submitted by '
			. html_tag( "a", array( "href" => swarmpath( "user/{$data["jobInfo"]["ownerName"]}" ) ), $data["jobInfo"]["ownerName"] )
			. ' on ' . htmlspecialchars( date( "Y-m-d H:i:s", gmstrtotime( $data["jobInfo"]["creationTimestamp"] ) ) )
			. ' (UTC)' . '</em>.</p>';

		if ( $request->getSessionData( "auth" ) === "yes" && $data["jobInfo"]["ownerName"] == $request->getSessionData( "username" ) ) {
			$html .= '<script>SWARM.jobInfo = ' . json_encode( $data["jobInfo"] ) . ';</script>'
				. '<button id="swarm-job-delete">Delete job</button>'
				. '<button id="swarm-job-reset">Reset job</button>'
				. '<div class="errorbox" id="swarm-wipejob-error" style="display: none;"></div>';
		}

		$html .= '<table class="results"><thead><tr><th>&nbsp;</th>';

		// Header with user agents
		foreach ( $data["userAgents"] as $userAgent ) {
			$html .= '<th><div class="browser"><img src="' . swarmpath( "images/" . $userAgent["engine"] )
				. '.sm.png" class="browser-icon ' . $userAgent["engine"]
				. '" alt="' . $userAgent["name"]
				. '" title="' . $userAgent["name"]
				. '"><span class="browser-name">'
				. preg_replace( "/\w+ /", "", $userAgent["name"] )
				. '</span></div></th>';
		}

		$html .= '</tr></thead><tbody>';

		foreach ( $data["runs"] as $run ) {
			$html .= '<tr><th><a href="' . htmlspecialchars( $run["info"]["url"] ) . '">'
				. $run["info"]["name"] . '</a></th>';

			// Looping over $data["userAgents"] instead of $run["uaRuns"],
			// to avoid shifts in the table (github.com/jquery/testswarm/issues/13)
			foreach ( $data["userAgents"] as $uaID => $uaInfo ) {
				if ( isset( $run["uaRuns"][$uaID] ) ) {
					$uaRun = $run["uaRuns"][$uaID];
					if ( $uaRun["runStatus"] !== "new" && $uaRun["runStatus"] !== "progress" ) {
						$html .=
							'<td class="status-' . $uaRun["runStatus"] . '">'
							. html_tag( 'a', array(
								"href" => $uaRun["runResultsUrl"],
							), $uaRun["runResultsLabel"] )
							. '</td>';
					} else {
						$html .= '<td class="status-new"></a>';
					}
				} else {
					// This run isn't schedules to be ran in this UA
					$html .= '<td class="notscheduled"></td>';
				}
			}
		}

		$html .= '</tbody></table>';
		return $html;
	}
}
