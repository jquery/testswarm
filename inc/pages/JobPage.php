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

		$isAuth = $request->getSessionData( "auth" ) === "yes" && $data["jobInfo"]["ownerName"] == $request->getSessionData( "username" );

		$html .=
			'<h2>' . $data["jobInfo"]["name"] .'</h2>'
			. '<p><em>Submitted by '
			. html_tag( "a", array( "href" => swarmpath( "user/{$data["jobInfo"]["ownerName"]}" ) ), $data["jobInfo"]["ownerName"] )
			. ' on ' . htmlspecialchars( date( "Y-m-d H:i:s", gmstrtotime( $data["jobInfo"]["creationTimestamp"] ) ) )
			. ' (UTC)' . '</em>.</p>';

		if ( $isAuth ) {
			$html .= '<script>SWARM.jobInfo = ' . json_encode( $data["jobInfo"] ) . ';</script>';
			$action_bar = '<div class="form-actions swarm-item-actions">'
				. ' <button class="swarm-reset-runs-failed btn btn-info">Reset failed runs</button>'
				. ' <button class="swarm-reset-runs btn btn-info">Reset all runs</button>'
				. ' <button class="swarm-delete-job btn btn-danger">Delete job</button>'
				. '</div>'
				. '<div class="alert alert-error" id="swarm-wipejob-error" style="display: none;"></div>';
		} else {
			$action_bar = '';
		}

		$html .= $action_bar;
		$html .= '<table class="table table-bordered swarm-results"><thead>'
			. self::getUaHtmlHeader( $data['userAgents'] )
			. '</thead><tbody>'
			. self::getUaRunsHtmlRows( $data['runs'], $data['userAgents'], $isAuth )
			. '</tbody></table>';

		$html .= $action_bar;

		return $html;
	}

	public static function getUaHtmlHeader( $userAgents ) {
		$html = '<tr><th>&nbsp;</th>';
		foreach ( $userAgents as $userAgent ) {
			$displayInfo = $userAgent['displayInfo'];
			$html .= '<th>'
				. html_tag( 'div', array(
					'class' => $displayInfo['class'],
					'title' => $displayInfo['title'],
				) )
				. '<br>'
				. html_tag( 'span', array(
					'class' => 'label swarm-browsername',
				), $displayInfo['title'] )
				. '</th>';
		}

		$html .= '</tr>';
		return $html;
	}

	/**
	 * @param Array $runs
	 * @param Array $userAgents
	 * @param bool $showResetRun: Whether to show the reset buttons for individual runs.
	 *  This does not check authororisation or load related javascript for the buttons.
	 */
	public static function getUaRunsHtmlRows( $runs, $userAgents, $showResetRun = false ) {
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
						$title = $userAgents[$uaID]['displayInfo']['title'];
						$runResultsTooltip = "Open run results for $title";
						$runResultsTagOpen = html_tag_open( 'a', array(
							'rel' => 'nofollow',
							'href' => $uaRun['runResultsUrl'],
							'title' => $runResultsTooltip,
						) );
						$html .=
							$runResultsTagOpen
							. ( $uaRun['runResultsLabel']
								? $uaRun['runResultsLabel']
								: UserPage::getStatusIconHtml( $uaRun['runStatus'] )
							). '</a>'
							. $runResultsTagOpen
							. html_tag( 'i', array(
								'class' => 'swarm-show-results icon-list-alt pull-right',
								'title' => $runResultsTooltip,
							) )
							. '</a>'
							. ( $showResetRun ?
								html_tag( 'i', array(
									'class' => 'swarm-reset-run-single icon-remove-circle pull-right',
									'title' => "Re-schedule run for $title",
								) )
								: ''
							);
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
