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

		if ( !$error ) {
			$this->setTitle( 'Logged out!' );
			$html .= html_tag( 'div', array( 'class' => 'alert alert-success' ),
				'You are now logged out.'
			);
			// Return early, we don't need to show the <form> in case of success.
			return $html;
		}

		// If we weren't logged in in the first place, there is no error.
		// If there is an error now it means we're on a POST request to Logout
		// and the tokens were invalid. Likely a case of a malice or stale state (someone having
		// a tab open for a month with absolute no other activity and the token no longer being
		// valid when they finally click "Logout").
		// Show the user the error and allow them user to manually confirm the log out.
		$auth = $this->getContext()->getAuth();
		$html .=
			html_tag( 'div', array( 'class' => 'alert alert-error' ), $error['info'] )
			. '<form action="' . swarmpath( 'logout' ) . '" method="post" class="form-horizontal">'
			. '<fieldset>'
			. '<legend>Log out</legend>'
			. '<div class="form-actions">'
			. '<p>Please submit the following protected form to proceed to log out.</p>'
			. '<input type="submit" value="Logout" class="btn btn-primary">'
			. '<input type="hidden" name="authID" value="' . htmlspecialchars( $auth->project->id ) . '">'
			. '<input type="hidden" name="authToken" value="' . htmlspecialchars( $auth->sessionToken ) . '">'
			. '</div>'
			. '</fieldset>'
			. '</form>';

		return $html;
	}
}
