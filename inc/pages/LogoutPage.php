<?php
/**
 * "Logout" page.
 *
 * @author John Resig, 2008-2011
 * @since 0.1.0
 * @package TestSwarm
 */
class LogoutPage extends Page {

	public function execute() {
		$action = LogoutAction::newFromContext( $this->getContext() );
		$action->doAction();

		$this->setAction( $action );
		$this->content = $this->initContent();
	}

	protected function initContent() {
		$request = $this->getContext()->getRequest();

		$this->setTitle( 'Log out' );
		$html = '';

		$data = $this->getAction()->getData();
		$error = $this->getAction()->getError();

		if ( !$error && $data['status'] == 'logged-out' ) {
			$this->setTitle( 'Logged out!' );
			$html .= html_tag( 'div', array( 'class' => 'alert alert-success' ),
				'Thanks for running TestSwarm. You are now signed out.'
			);
			// Don't show form in case of success.
			return $html;
		}

		if ( $request->wasPosted() && $error ) {
			$html .= html_tag( 'div', array( 'class' => 'alert alert-error' ), $error['info'] );
		}


		$html .= '<form action="' . swarmpath( 'logout' ) . '" method="post" class="form-horizontal">'
			. '<fieldset>'
			. '<legend>Log out</legend>'
			. '<div class="form-actions">'
			. '<p>Please submit the following protected form to proceed to log out.</p>'
				. '<input type="submit" value="Logout" class="btn btn-primary">'
				. self::getLogoutFormFieldsHtml( $this->getContext() )
			. '</div>'
			. '</fieldset>'
			. '</form>';

		return $html;
	}

	public static function getLogoutFormFieldsHtml( TestSwarmContext $context ) {
		$db = $context->getDB();
		$request = $context->getRequest();

		$userName = $request->getSessionData( 'username' );
		$userAuthToken = $db->getOne(str_queryf(
			'SELECT auth
			FROM users
			WHERE name = %s',
			$userName
		));

		return
			'<input type="hidden" name="authUsername" value="' . htmlspecialchars( $userName ) . '">'
			. '<input type="hidden" name="authToken" value="' . htmlspecialchars( $userAuthToken ) . '">';

	}
}
