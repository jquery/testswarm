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

	public static function getStatusIconHtml( $status ) {
		static $icons = array(
			"new" => '<i class="icon-time" title="Scheduled, awaiting run."></i>',
			"progress" => '<i class="icon-repeat swarm-status-progressicon" title="In progress.."></i>',
			"passed" => '<i class="icon-ok" title="Passed!"></i>',
			"failed" => '<i class="icon-remove" title="Completed with failures"></i>',
			"timedout" => '<i class="icon-flag" title="Maximum execution time exceeded"></i>',
			"error" => '<i class="icon-warning-sign" title="Aborted by an error"></i>',
		);
		return isset( $icons[$status] ) ? $icons[$status] : '';
	}

	/**
	 * Not used anywhere yet, the colors, icons and title attributes are probably
	 * good enough, if not this table is ready for use.
	 * @example:
	 * '<div class="row"><div class="span6">' . getStatusLegend() . '</div></div>'
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
			. '<tr><td class="swarm-status swarm-status-notscheduled">'
				. ''
				. '</td><td>This browser was not part of the browserset for this job.</td>'
			. '</tr>'
			. '</tbody></table>';
	}

	protected function initContent() {

		$this->setTitle( "User" );

		$html = "";

		$error = $this->getAction()->getError();
		$data = $this->getAction()->getData();
		if ( $error ) {
			$html .= html_tag( "div", array( "class" => "alert alert-error" ), $error["info"] );
			return $html;
		}

		$this->setSubTitle( $data['userName'] );

		if ( count( $data['activeClients'] ) ) {

			$html .= '<h2>Active clients</h2><div class="row">';

			foreach ( $data['activeClients'] as $activeClient ) {
				$displayInfo = $activeClient['uaData']['displayInfo'];
				$html .=
					'<div class="span4"><div class="well clearfix">'
					. html_tag( 'div', array(
						'class' => $displayInfo['class'] . ' pull-right',
						'title' => $displayInfo['title'],
					) )
					. '<strong class="label">' . htmlspecialchars( $displayInfo['title'] ) . '</strong>'
					. '<p>'
					. '<small>Connected ' . self::getPrettyDateHtml( $activeClient, 'connected' ) . '</small>'
					. '<br>'
					. '<small>Last ping ' . self::getPrettyDateHtml( $activeClient, 'pinged' ) . '</small>'
					. '</p>'
					. '</div></div>';
			}

			$html .= '</div>';

		}

		if ( count( $data['recentJobs'] ) ) {

			$html .= '<h2>Recent jobs</h2><table class="table table-bordered swarm-results">';

			// Build table header
			$html .= '<thead>';
			$html .= JobPage::getUaHtmlHeader( $data['uasInJobs'] );
			$html .= '</thead><tbody>';

			foreach ( $data['recentJobs'] as $job ) {

				$html .= '<tr><th><a href="' . htmlspecialchars( $job['url'] ) . '">' . htmlspecialchars( strip_tags( $job['name'] ) ) . "</a></th>\n";

				foreach ( $data['uasInJobs'] as $uaID => $uaData ) {
					$html .= isset( $job['uaSummary'][$uaID] )
						? ( '<td class="swarm-status swarm-status-' . $job['uaSummary'][$uaID] . '">'
							. self::getStatusIconHtml( $job['uaSummary'][$uaID] )
							. '</td>'
						)
						: '<td class="swarm-status swarm-status-notscheduled"></td>';
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
