<?php
/**
 * Page interface for InfoAction.
 *
 * @author Timo Tijhof
 * @since 1.0.0
 * @package TestSwarm
 */
class InfoPage extends Page {

	public function execute() {
		$action = InfoAction::newFromContext( $this->getContext() );
		$action->doAction();

		$this->setAction( $action );
		$this->content = $this->initContent();
	}

	protected function initContent() {
		$this->setTitle( 'Software information' );

		$html = '';

		$data = $this->getAction()->getData();
		$error = $this->getAction()->getError();

		if ( $error ) {
			$html .= html_tag( 'div', array( 'class' => 'alert alert-error' ), $error['info'] );
		}

		$html .= '<blockquote><p>This swarm is running ' . html_tag( 'a', array(
			'href' => $data['software']['website'],
			), 'TestSwarm'
		) . ' version '. $data['software']['versionInfo']['TestSwarm'] . '.</p></blockquote>';

		$devInfo = $data['software']['versionInfo']['devInfo'];
		if ( $devInfo ) {
			$html .= '<p>This install of TestSwarm is managed in a Git repository. '
				. ' The current branch is ' . html_tag( 'a', array(
					'href' => 'https://github.com/jquery/testswarm/tree/' . $devInfo['branch'],
				), $devInfo['branch'] )
				. '. HEAD is at <code>' . html_tag( 'a', array(
					'href' => 'https://github.com/jquery/testswarm/commit/' . $devInfo['SHA1'],
				), $devInfo['SHA1'] )
				. '</code>.</p>';
		}

		return $html;
	}
}
