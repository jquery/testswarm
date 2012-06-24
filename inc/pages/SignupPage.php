<?php
/**
 * "Signup" page.
 *
 * @author John Resig, 2008-2011
 * @author Jörn Zaefferer, 2012
 * @since 0.1.0
 * @package TestSwarm
 */

class SignupPage extends Page {

	public function execute() {
		$action = SignupAction::newFromContext( $this->getContext() );
		$action->doAction();
		$error = $action->getError();
		if ( !$error ) {
			$data = $action->getData();
			if ( $data["status"] === "logged-in" ) {
				$this->redirect( swarmpath( "user/" . $data["username"] ) );
			}
		}

		$this->setAction( $action );
		$this->content = $this->initContent();
	}

	protected function initContent() {
		$request = $this->getContext()->getRequest();

		$this->setTitle( "Signup" );

		$html = '<form action="' . swarmpath( "signup" ) . '" method="post" class="form-horizontal">'
			. '<fieldset>'
			. '<legend>Signup</legend>';

		$error = $this->getAction()->getError();

		if ( $request->wasPosted() && $error ) {
			$html .= html_tag( 'div', array( 'class' => 'alert alert-error' ), $error['info'] );
		}

		$html .=
			'<div class="well">'
				. '<p>Create an account. If you already have an account you may <a href="' . swarmpath( "login" )
				. '">login here</a>.</p>'
				. '<div class="control-group">'
					. '<label class="control-label" for="form-username">Username</label>'
					. '<div class="controls">'
						. '<input id="form-username" type="text" name="username" maxlength="255">'
					. '</div>'
				. '</div><div class="control-group">'
					. '<label class="control-label" for="form-password">Password</label>'
					. '<div class="controls">'
						. '<input id="form-password" type="password" name="password">'
					. '</div>'
				. '</div>'
			. '</div><div class="form-actions">'
				. '<input type="submit" value="Signup" class="btn btn-primary">'
			. '</div>';

		$html .= '</fieldset></form>';

		return $html;
	}
}
