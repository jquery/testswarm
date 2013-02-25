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
	 * @actionAuth: Required.
	 */
	public function doAction() {

		// Only go through authentication check if we're actually logged-in,
		// if we somehow end up here when not logged-in at all, just clear
		// whatever stale session there is and respond successful.
		if ( $this->getContext()->getAuth() ) {
			if ( !$this->doRequireAuth() ) {
				return;
			}
		}

		$this->getContext()->flushAuth();
	}
}