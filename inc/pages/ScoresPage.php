<?php
/**
 * "Scores" page.
 *
 * @author John Resig, 2008-2011
 * @author Jörn Zaefferer, 2012
 * @since 0.1.0
 * @package TestSwarm
 */

class ScoresPage extends Page {

	public function execute() {
		$action = ScoresAction::newFromContext( $this->getContext() );
		$action->doAction();

		$this->setAction( $action );
		$this->content = $this->initContent();
	}

	protected function initContent() {
		$this->setTitle( "Scores" );
		$scores = $this->getAction()->getData();

		$html = '<blockquote><p>All users with a score greater than zero.'
		 . ' The score is the number of tests run by that user\'s clients.</p></blockquote>'
		 . '<table class="table table-striped">'
		 . '<thead><tr><th class="span1">#</th><th>User</th><th class="span2">Score</th></tr></thead>'
		 . '<tbody>';

		foreach ( $scores as $item ) {
			$html .= '<tr><td class="num">' . htmlspecialchars( number_format( $item["position"] ) ) . '</td>'
				. '<td><a href="' . htmlspecialchars( swarmpath( "user/{$item["userName"]}" ) ) . '">' . htmlspecialchars( $item["userName"] ) . '</a></td>'
				. '<td class="num">' . htmlspecialchars( number_format( $item["score"] ) ) . '</td></tr>';
		}
		$html .= '</tbody></table>';

		return $html;
	}

}
