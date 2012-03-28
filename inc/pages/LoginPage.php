<?php
/**
 * "Login" page.
 *
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

		$html = '<form action="' . swarmpath( "login" ) . '" method="post">'
			. '<fieldset>'
			. '<legend>Login</legend>';

		$error = $this->getAction()->getError();

		if ( $request->wasPosted() && $error ) {
			$html .= html_tag( 'div', array( 'class' => 'errorbox' ), $error['info'] );
		}

		$html .= '<p>Login using your TestSwarm username and password.'
			. ' If you don\'t have one you may <a href="' . swarmpath( "signup" )
			. '">Signup Here</a>.</p>'
			. '<label>Username: <input type="text" name="username" value="' . htmlspecialchars( $request->getVal( "username" ) ) . '"></label><br>'
			. '<label>Password: <input type="password" name="password"></label><br>'
			. '<input type="submit" value="Login">'
			. '</fieldset></form>';
		return $html;
	}
}
