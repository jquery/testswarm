<?php
/**
 * Page interface for JobAction.
 *
 * @author John Resig
 * @author Jörn Zaefferer
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
		$auth = $this->getContext()->getAuth();

		$this->setTitle( "Job status" );
		$this->setRobots( 'noindex,nofollow' );
		$this->bodyScripts[] = swarmpath( "js/job.js" );

		$error = $this->getAction()->getError();
		$data = $this->getAction()->getData();
		$html = '';

		if ( $error ) {
			$html .= html_tag( 'div', array( 'class' => 'alert alert-error' ), $error['info'] );
		}

		if ( !isset( $data["info"] ) ) {
			return $html;
		}

		$this->setSubTitle( '#' . $data["info"]["id"] );

		$project = $data['info']['project'];
		$isOwner = $auth && $auth->project->id === $project['id'];

		$html .=
			'<h2>' . $data["info"]["nameHtml"] .'</h2>'
			. '<p><em>Submitted by '
			. html_tag( 'a', array( 'href' => $project['viewUrl'] ), $project['display_title'] )
			. ' '. self::getPrettyDateHtml( $data["info"], 'created' )
			. '</em>.</p>';

		if ( $isOwner ) {
			$html .= '<script>SWARM.jobInfo = ' . json_encode2( $data["info"] ) . ';</script>';
			$action_bar = '<div class="form-actions swarm-item-actions">'
				. ' <button class="swarm-reset-runs-failed btn btn-info">Reset failed runs</button>'
				. ' <button class="swarm-reset-runs btn btn-info">Reset all runs</button>'
				. ' <button class="swarm-delete-job btn btn-danger">Delete job</button>'
				. '</div>'
				. '<div class="swarm-wipejob-error alert alert-error" style="display: none;"></div>';
		} else {
			$action_bar = '';
		}

		$html .= '<ul class="pager">';
		if ( $data['pagination']['prev'] ) {
			$html .= '<li class="previous">' . html_tag_open( 'a', array(
				'href' => $data['pagination']['prev']['viewUrl'],
				'title' => $data['pagination']['prev']['nameText'],
			) ) . '&larr;&nbsp;Previous</a></li>';
		} else {
			$html .= '<li class="previous disabled" title="No previous job"><a href="#">&larr;&nbsp;Previous</a></span>';
		}
		if ( $data['pagination']['next'] ) {
			$html .= '<li class="next">' . html_tag_open( 'a', array(
				'href' => $data['pagination']['next']['viewUrl'],
				'title' => $data['pagination']['next']['nameText'],
				) ) . 'Next&nbsp;&rarr;</a></li>';
		} else {
			$html .= '<li class="next disabled" title="No next job"><a href="#">Next&nbsp;&rarr;</a></span>';
		}
		$html .= '</ul>';

		$html .= $action_bar;
		$html .= '<table class="table table-bordered swarm-results swarm-results-unbound-auth"><thead>'
			. self::getUaHtmlHeader( $data['userAgents'] )
			. '</thead><tbody>'
			. self::getUaRunsHtmlRows( $data['runs'], $data['userAgents'], $isOwner )
			. '</tbody></table>';

		$html .= $action_bar;

		return $html;
	}

	/**
	 * Create a table header for user agents.
	 */
	public static function getUaHtmlHeader( $userAgents ) {
		$html = '<tr><th>&nbsp;</th>';
		foreach ( $userAgents as $userAgent ) {
			$displayInfo = $userAgent['displayInfo'];
			$html .= '<th>'
				. html_tag( 'div', array(
					'class' => $displayInfo['class'] . ' swarm-icon-small',
					'title' => $displayInfo['title'],
				) )
				. '<br>'
				. html_tag_open( 'span', array(
					'class' => 'swarm-results-browsername swarm-browsername',
				) ) . $displayInfo['labelHtml'] . '</span>'
				. '</th>';
		}

		$html .= '</tr>';
		return $html;
	}

	/**
	 * Create table rows for a table of ua run results.
	 * This is used on the JobPage.
	 *
	 * @param array $runs List of runs, from JobAction.
	 * @param array $userAgents List of uaData objects.
	 * @param bool $showResetRun Whether to show the reset buttons for individual runs.
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
								: self::getStatusIconHtml( $uaRun['runStatus'] )
							). '</a>'
							. $runResultsTagOpen
							. html_tag( 'i', array(
								'class' => 'swarm-show-results icon-list-alt',
								'title' => $runResultsTooltip,
							) )
							. '</a>'
							. ( $showResetRun ?
								html_tag( 'i', array(
									'class' => 'swarm-reset-run-single icon-remove-circle',
									'title' => "Re-schedule run for $title",
								) )
								: ''
							);
					} else {
						$html .= self::getStatusIconHtml( $uaRun['runStatus'] );
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

	public static function getStatusIconHtml( $status ) {
		static $icons = array(
			"new" => '<i class="icon-time" title="Scheduled, awaiting run."></i>',
			"progress" => '<i class="icon-repeat swarm-status-progressicon" title="In progress.."></i>',
			"passed" => '<i class="icon-ok" title="Passed!"></i>',
			"failed" => '<i class="icon-remove" title="Completed with failures"></i>',
			"timedout" => '<i class="icon-flag" title="Maximum execution time exceeded"></i>',
			"error" => '<i class="icon-warning-sign" title="Aborted by an error"></i>',
			"lost" => '<i class="icon-question-sign" title="Client lost connection with the swarm"></i>',
		);
		return isset( $icons[$status] ) ? $icons[$status] : '';
	}

	/**
	 * Not used anywhere yet. The colors, icons and tooltips should be
	 * easy to understand. If not, this table is ready for use.
	 * @example:
	 *     '<div class="row"><div class="span6">' . getStatusLegend() . '</div></div>'
	 */
	public static function getStatusLegend() {
		return
			'<table class="table table-condensed table-bordered swarm-results">'
			. '<tbody>'
			. '<tr><td class="swarm-status swarm-status-new">'
				. self::getStatusIconHtml( "new" )
				. '</td><td>Scheduled</td>'
			. '</tr>'
			. '<tr><td class="swarm-status swarm-status-progress">'
				. self::getStatusIconHtml( "progress" )
				. '</td><td>In progress..</td>'
			. '</tr>'
			. '<tr><td class="swarm-status swarm-status-passed">'
				. self::getStatusIconHtml( "passed" )
				. '</td><td>Passed!</td>'
			. '</tr>'
			. '<tr><td class="swarm-status swarm-status-failed">'
				. self::getStatusIconHtml( "failed" )
				. '</td><td>Completed with failures</td>'
			. '</tr>'
			. '<tr><td class="swarm-status swarm-status-timedout">'
				. self::getStatusIconHtml( "timedout" )
				. '</td><td>Maximum execution time exceeded</td>'
			. '</tr>'
			. '<tr><td class="swarm-status swarm-status-error">'
				. self::getStatusIconHtml( "error" )
				. '</td><td>Aborted by an error</td>'
			. '</tr>'
			. '<tr><td class="swarm-status swarm-status-lost">'
				. self::getStatusIconHtml( "lost" )
				. '</td><td>Client lost connection with the swarm</td>'
			. '</tr>'
			. '<tr><td class="swarm-status swarm-status-notscheduled">'
				. ''
				. '</td><td>This browser was not part of the browserset for this job.</td>'
			. '</tr>'
			. '</tbody></table>';
	}

	/**
	 * Create a single row summarising the ua runs of a job. See also #getUaRunsHtmlRows.
	 * This is used on the ProjectPage.
	 * @param Array $job
	 * @param Array $userAgents List of uaData objects.
	 */
	public static function getJobHtmlRow( $job, $userAgents ) {
		$html = '<tr><th>'
			. '<a href="' . htmlspecialchars( $job['info']['viewUrl'] ) . '">' . htmlspecialchars( $job['info']['nameText'] ) . '</a>'
			. ' '
			. self::getPrettyDateHtml( $job['info'], 'created', array( 'class' => 'swarm-result-date' ) )
			. "</th>\n";

		foreach ( $userAgents as $uaID => $uaData ) {
			$html .= self::getJobStatusHtmlCell( isset( $job['summaries'][$uaID] ) ? $job['summaries'][$uaID] : false );
		}

		$html .= '</tr>';
		return $html;

	}

	/**
	 * Create a singe cell summarising the ua runs of a job. See also #getJobHtmlRow.
	 * This is used on the ProjectsPage.
	 * @param string|bool $status Status, or false to create a "skip" cell with
	 *  "notscheduled" status.
	 */
	public static function getJobStatusHtmlCell( $status = false ) {
		return $status
				? ( '<td class="swarm-status-cell"><div class="swarm-status swarm-status-' . $status . '">'
					. self::getStatusIconHtml( $status )
					. '</div></td>'
				)
				: '<td class="swarm-status swarm-status-notscheduled"></td>';
	}

}
