<?php
/**
 * "Logout" action.
 *
 * @author John Resig, 2008-2011
 * @since 0.1.0
 * @package TestSwarm
 */
class LogoutAction extends Action {

	/**
	 * @actionMethod POST: Required.
	 * @actionAuth: Yes.
	 */
	public function doAction() {
		$request = $this->getContext()->getRequest();

		// Only go through authentication check if we're actually logged-in,
		// if we somehow end up here when not logged-in at all, just clear
		// whatever stale session there is and respond successful.
		if ( $request->getSessionData( 'auth' ) == 'yes' ) {
			if ( !$this->doRequireAuth() ) {
				return;
			}
		}

		$request->setSessionData( 'username', null );
		$request->setSessionData( 'auth', null );

		$this->setData( array(
			'status' => 'logged-out',
		) );
	}
}