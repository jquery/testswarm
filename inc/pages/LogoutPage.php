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
		$this->setTitle( "Logged out" );

		return '<div class="alert alert-info">Thanks for running TestSwarm. You are now signed out.</div>';
	}
}
