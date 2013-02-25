<?php
/**
 * "Projects" page.
 *
 * @author Timo Tijhof, 2012
 * @since 1.0.0
 * @package TestSwarm
 */

class ProjectsPage extends Page {

	public function execute() {
		$action = ProjectsAction::newFromContext( $this->getContext() );
		$action->doAction();

		$this->setAction( $action );
		$this->content = $this->initContent();
	}

	protected function initContent() {
		$this->setTitle( 'Projects' );

		$projects = $this->getAction()->getData();

		$html = '<blockquote><p>Below is an overview of all registered projects,'
			. ' sorted alphabetically by name.</p></blockquote>'
			. '<table class="table table-striped">'
			. '<thead><tr>'
				. '<th>Project name</th>'
				. '<th class="span4">Latest job</th>'
			. '</tr></thead>'
			. '<tbody>';

		foreach ( $projects as $project ) {
			$job = $project['job'];
			$html .= '<tr>'
				. '<td><a href="'
				. htmlspecialchars( swarmpath( "project/{$project['id']}" ) ) . '">'
				. htmlspecialchars( $project['displayTitle'] ) . '</a></td>';
			if ( !$job ) {
				$html .= '<td>N/A</td>';
			} else {
				$html .= '<td class="swarm-status-cell swarm-jobstatus-cell"><div class="swarm-status swarm-status-' . $job['summary'] . '">'
					. JobPage::getStatusIconHtml( $job['summary'] )
					. html_tag( 'a', array(
						'href' => $job['info']['viewUrl'],
						'title' => $job['info']['nameText'],
						), 'Job #' . $job['info']['id']
					)
					. '</div></td>';
			}

			$html .= '</tr>';
		}
		$html .= '</tbody></table>';

		return $html;
	}

}
