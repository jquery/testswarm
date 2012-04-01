<?php
/**
 * "Logout" action.
 *
 * @author John Resig, 2008-2011
 * @since 0.1.0
 * @package TestSwarm
 */
class LogoutAction extends Action {

	public function doAction() {
		$request = $this->getContext()->getRequest();
		$request->setSessionData( "username", null );
		$request->setSessionData( "auth", null );

		$this->setData( array(
			"status" => "logged-out",
		) );
	}
}