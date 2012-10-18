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
		$this->setRobots( "noindex,nofollow" );
		$this->bodyScripts[] = swarmpath( "js/job.js" );

		$error = $this->getAction()->getError();
		$data = $this->getAction()->getData();
		$html = '';

		if ( $error ) {
			$html .= html_tag( 'div', array( 'class' => 'alert alert-error' ), $error['info'] );
		}

		if ( !isset( $data["jobInfo"] ) ) {
			return $html;
		}

		$this->setSubTitle( '#' . $data["jobInfo"]["id"] );

		$html .=
			'<h2>' . $data["jobInfo"]["name"] .'</h2>'
			. '<p><em>Submitted by '
			. html_tag( "a", array( "href" => swarmpath( "user/{$data["jobInfo"]["ownerName"]}" ) ), $data["jobInfo"]["ownerName"] )
			. ' on ' . htmlspecialchars( date( "Y-m-d H:i:s", gmstrtotime( $data["jobInfo"]["creationTimestamp"] ) ) )
			. ' (UTC)' . '</em>.</p>';

		if ( $request->getSessionData( "auth" ) === "yes" && $data["jobInfo"]["ownerName"] == $request->getSessionData( "username" ) ) {
			$html .= '<script>SWARM.jobInfo = ' . json_encode( $data["jobInfo"] ) . ';</script>';
			$action_bar = '<div class="form-actions swarm-item-actions">'
				. ' <button class="swarm-job-reset-failed btn btn-info">Reset Failed jobs</button>'
				. ' <button class="swarm-job-reset btn btn-info">Reset job</button>'
				. ' <button class="swarm-job-delete btn btn-danger">Delete job</button>'
				. '</div>'
				. '<div class="alert alert-error" id="swarm-wipejob-error" style="display: none;"></div>';
			$html .= $action_bar;
		}

		$html .= '<table class="table table-bordered swarm-results"><thead>'
			. self::getUaHtmlHeader( $data['userAgents'] )
			. '</thead><tbody>'
			. self::getUaRunsHtmlRows( $data['runs'], $data['userAgents'] )
			. '</tbody></table>';

		if ( $request->getSessionData( "auth" ) === "yes" && $data["jobInfo"]["ownerName"] == $request->getSessionData( "username" ) ) {
			$html .= $action_bar;
		}

		return $html;
	}

	public static function getUaHtmlHeader( $userAgents ) {
		$html = '<tr><th>&nbsp;</th>';

		foreach ( $userAgents as $userAgent ) {
			$html .= '<th><img src="' . swarmpath( 'img/' . $userAgent['displayicon'] )
				. '.sm.png" class="swarm-browsericon '
				. '" alt="' . htmlspecialchars( $userAgent['displaytitle'] )
				. '" title="' . htmlspecialchars( $userAgent['displaytitle'] )
				. '"><br>'
				. htmlspecialchars( preg_replace( '/\w+ /', '', $userAgent['displaytitle'] ) )
				. '</th>';
		}

		$html .= '</tr>';
		return $html;
	}

	public static function getUaRunsHtmlRows( $runs, $userAgents ) {
		$html = '';

		foreach ( $runs as $run ) {
			$html .= '<tr><th><a href="' . htmlspecialchars( $run['info']['url'] ) . '">'
				. $run['info']['name'] . '</a></th>';

			// Looping over $userAgents instead of $run["uaRuns"],
			// to avoid shifts in the table (github.com/jquery/testswarm/issues/13)
			foreach ( $userAgents as $uaID => $uaInfo ) {
				if ( isset( $run['uaRuns'][$uaID] ) ) {
					$uaRun = $run['uaRuns'][$uaID];
					$html .= html_tag_open( 'td', array(
						'class' => 'swarm-status swarm-status-' . $uaRun['runStatus'],
						'data-run-id' => $run['info']['id'],
						'data-run-status' => $uaRun['runStatus'],
						'data-useragent-id' => $uaID,
						// Un-ran tests don't have a client id
						'data-client-id' => isset( $uaRun['clientID'] ) ? $uaRun['clientID'] : '',
					));
					if ( isset( $uaRun['runResultsUrl'] ) && isset( $uaRun['runResultsLabel'] ) ) {
						$html .=
							html_tag_open( 'a', array(
								'rel' => 'nofollow',
								'href' => $uaRun['runResultsUrl'],
							) )
							. ( $uaRun['runResultsLabel']
								? $uaRun['runResultsLabel']
								: UserPage::getStatusIconHtml( $uaRun['runStatus'] )
							)
							. '<i class="icon-list-alt pull-right" title="' . htmlspecialchars(
								"Open run results for {$userAgents[$uaID]['displaytitle']}"
							) . '"></i>'
							. '</a>';
							$html .=
							html_tag_open( 'i', array('class' => 'hover-only swarm-job-reset-single icon-trash pull-right', 'title' => htmlspecialchars(
								"Delete results for {$userAgents[$uaID]['displaytitle']}"
							) ) ) . '</i>';
					} else {
						$html .= UserPage::getStatusIconHtml( $uaRun['runStatus'] );
					}
					$html .= '</td>';
				} else {
					// This run isn't schedules to be ran in this UA
					$html .= '<td class="swarm-status swarm-status-notscheduled"></td>';
				}
			}
		}

		return $html;
	}
}
