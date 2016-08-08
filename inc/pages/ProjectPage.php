<?php
/**
 * Page interface for ProjectAction.
 *
 * @author John Resig
 * @author Jörn Zaefferer
 * @author Timo Tijhof
 * @since 1.0.0
 * @package TestSwarm
 */
class ProjectPage extends Page {
	public function execute() {
		$action = ProjectAction::newFromContext( $this->getContext() );
		$action->doAction();

		$this->setAction( $action );
		$this->content = $this->initContent();
	}

	protected function initContent() {

		$this->setTitle( "Project" );

		$html = "";

		$error = $this->getAction()->getError();
		$data = $this->getAction()->getData();
		if ( $error ) {
			$html .= html_tag( "div", array( "class" => "alert alert-error" ), $error["info"] );
			return $html;
		}

		$this->setSubTitle( $data['info']['display_title'] );

		$info = array();
		if ( $data['info']['site_url'] ) {
			$info[] = 'Homepage: ' . html_tag( 'a', array( 'href' => $data['info']['site_url'] ), parse_url( $data['info']['site_url'], PHP_URL_HOST ) ?: $data['info']['site_url'] );
		}
		$info[] = 'Created: ' . self::getPrettyDateHtml( $data['info'], 'created' );

		$html .= '<div class="well well-small">' . implode( ' <span class="muted">|</span> ', $info ) . '</div>';

		if ( !count( $data['jobs'] ) ) {

			$html .= '<div class="alert alert-info">No jobs found.</div>';

		} else {

			$html .= '<h2>Jobs</h2><ul class="pager">';
			if ( $data['pagination']['prev'] ) {
				$html .= '<li class="previous">' . html_tag_open( 'a', array(
					'href' => $data['pagination']['prev']['viewUrl'],
				) ) . '&larr;&nbsp;Previous</a></li>';
			} else {
				$html .= '<li class="previous disabled" title="No previous page"><a href="#">&larr;&nbsp;Previous</a></span>';
			}
			if ( $data['pagination']['next'] ) {
				$html .= '<li class="next">' . html_tag_open( 'a', array(
					'href' => $data['pagination']['next']['viewUrl'],
				) ) . 'Next&nbsp;&rarr;</a></li>';
			} else {
				$html .= '<li class="next disabled" title="No next page"><a href="#">Next&nbsp;&rarr;</a></span>';
			}
			$html .= '</ul>';

			$html .= '<table class="table table-bordered swarm-results">';
			$html .= '<thead>';
			$html .= JobPage::getUaHtmlHeader( $data['userAgents'] );
			$html .= '</thead><tbody>';

			foreach ( $data['jobs'] as $job ) {
				$html .= JobPage::getJobHtmlRow( $job, $data['userAgents'] );
			}

			$html .= '</tbody></table>';
		}

		return $html;
	}
}
