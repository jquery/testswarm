<?php
/**
 * "Login" page.
 *
 * @author John Resig, 2008-2011
 * @author Timo Tijhof, 2012
 * @since 0.1.0
 * @package TestSwarm
 */

class LoginPage extends Page {

	public function execute() {
		$action = LoginAction::newFromContext( $this->getContext() );
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

		$this->setTitle( "Login" );

		$html = '<form action="' . swarmpath( "login" ) . '" method="post" class="form-horizontal">'
			. '<fieldset>'
			. '<legend>Login</legend>';

		$error = $this->getAction()->getError();

		if ( $request->wasPosted() && $error ) {
			$html .= html_tag( 'div', array( 'class' => 'alert alert-error' ), $error['info'] );
		}

		$html .=
		'<div class="well">'
			. '<p>Login using your TestSwarm username and password.'
			. ' If you don\'t have one you may <a href="' . swarmpath( "signup" )
			. '">Signup Here</a>.</p>'
			. '<div class="control-group">'
				. '<label class="control-label" for="form-username">Username</label>'
				. '<div class="controls">'
					. '<input id="form-password" type="text" name="username" value="' . htmlspecialchars( $request->getVal( "username" ) ) . '">'
				. '</div>'
			. '</div><div class="control-group">'
				. '<label class="control-label" for="form-password">Password</label>'
				. '<div class="controls">'
					. '<input type="password" name="password">'
				. '</div>'
			. '</div>'
		. '</div><div class="form-actions">'
			. '<input id="form-password" type="submit" value="Login" class="btn btn-primary">'
		. '</div>';
		$html .= '</fieldset></form>';
		return $html;
	}
}
