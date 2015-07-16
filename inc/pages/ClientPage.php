<?php
/**
 * Page interface for ClientAction.
 *
 * @author Timo Tijhof
 * @since 1.0.0
 * @package TestSwarm
 */
class ClientPage extends Page {

	public function execute() {
		$action = ClientAction::newFromContext( $this->getContext() );
		$action->doAction();

		$this->setAction( $action );
		$this->content = $this->initContent();
	}

	protected function initContent() {
		$this->setTitle( 'Client' );
		$this->setRobots( 'noindex,nofollow' );

		$error = $this->getAction()->getError();
		$data = $this->getAction()->getData();
		$html = '';

		if ( $error ) {
			$html .= html_tag( 'div', array( 'class' => 'alert alert-error' ), $error['info'] );
		}

		if ( !isset( $data['info'] ) ) {
			return $html;
		}

		$info = $data['info'];
		$displayInfo = $info['uaData']['displayInfo'];

		$this->setSubTitle( '#' . $info['id'] );

		$html .= '<h3>Information</h3>'
			. '<div class="row">'
			. '<div class="span2">'
				. BrowserInfo::buildIconHtml( $displayInfo )
			. '</div>'
			. '<div class="span10">'
			. '<table class="table table-striped">'
			. '<tbody>'
			. '<tr><th>Name</th><td>'
				. html_tag( 'a', array( 'href' => $info['viewUrl'] ), $info['name'] )
			. '</td></tr>'
			. '<tr><th>UA ID</th><td>'
				. '<code>' . htmlspecialchars( $info['uaID'] ) . '</code>'
			. '<tr><th>User-Agent</th><td>'
				. '<tt>' . htmlspecialchars( $info['uaRaw'] ) . '</tt>'
			. '</td></tr>'
			. '<tr><th>Session age</th><td>'
				. number_format( intval( $info['sessionAge'] ) ) . 's'
			. '</td></tr>'
			. '<tr><th>Connected</th><td>'
				. self::getPrettyDateHtml( $info, 'connected' )
			. '</td></tr>'
			. '<tr><th>Last ping</th><td>'
				. self::getPrettyDateHtml( $info, 'pinged' )
			. '</td></tr>'
			. '</tbody></table>'
			. '</div>'
			. '</div>';

		$html .= '<h3>Log</h3>';
		if ( !$data['results'] ) {
			$html .= '<div class="alert alert-info">Client has no run log.</div>';
		} else {
			$html .= '<table class="table table-striped">'
				. '<thead><tr><th>Result</th><th>Project</th></th><th>Run</th><th>Status</th>'
				. '<tbody>';

			foreach ( $data['results'] as $run ) {
				$html .= '<tr>'
					. '<td>' . html_tag( 'a', array( 'href' => $run['viewUrl'] ), '#' . $run['id'] ) . '</td>'
					. '<td>' . ( $run['project']
						? html_tag( 'a', array( 'href' => $run['project']['viewUrl'] ), $run['project']['display_title'] )
						: '-' ) . '</td>'
					. '<td>' . ( $run['job'] && $run['run']
						? html_tag( 'a', array( 'href' => $run['job']['viewUrl'] ), $run['job']['nameText'] . ' / ' . $run['run']['name'] )
						: '<em>Job has been deleted</em>' ) . '</td>'
					. JobPage::getJobStatusHtmlCell( $run['status'] )
					. '</tr>';
			}

			$html .= '</tbody></table>';
		}

		return $html;
	}

}
