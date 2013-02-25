<?php
/**
 * "Login" page.
 *
 * @author John Resig, 2008-2011
 * @author Timo Tijhof, 2012-2013
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
			if ( $data ) {
				$this->redirect( swarmpath( 'project/' . $data['id'] ) );
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
			. '<p>Login for projects. Projects can only be created by swarm operators.</p>'
			. '<div class="control-group">'
				. '<label class="control-label" for="form-projectID">Project ID</label>'
				. '<div class="controls">'
					. '<input type="text" name="projectID" required id="form-projectID" value="' . htmlspecialchars( $request->getVal( "projectID" ) ) . '">'
				. '</div>'
			. '</div><div class="control-group">'
				. '<label class="control-label" for="form-projectPassword">Project password</label>'
				. '<div class="controls">'
					. '<input type="password" name="projectPassword" required id="form-projectPassword">'
				. '</div>'
			. '</div>'
		. '</div><div class="form-actions">'
			. '<input type="submit" value="Login" class="btn btn-primary">'
		. '</div>';
		$html .= '</fieldset></form>';
		return $html;
	}
}
