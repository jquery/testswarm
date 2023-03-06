<?php
/**
 * Keep the client session active.
 *
 * @author Timo Tijhof
 * @since 1.0.0
 * @package TestSwarm
 */
class PingAction extends Action {

	/**
	 * Update client 'alive' and refresh client config.
	 *
	 * @actionMethod POST Required.
	 * @actionParam string run_token
	 * @actionParam int client_id
	 */
	public function doAction() {
		$conf = $this->getContext()->getConf();
		$request = $this->getContext()->getRequest();

		if ( !$request->wasPosted() ) {
			$this->setError( 'requires-post' );
			return;
		}

		$runToken = $request->getVal( 'run_token' );
		if ( $conf->client->runTokenHash && !$runToken ) {
			$this->setError( 'missing-parameters', 'This TestSwarm does not allow unauthorized clients to join the swarm.' );
			return;
		}

		$clientID = $request->getInt( 'client_id' );

		if ( !$clientID ) {
			$this->setError( 'missing-parameters' );
			return;
		}

		// Create a Client object that verifies client id, user agent and run token.
		// Also updates the client 'alive' timestamp.
		// Throws exception (caught higher up) if stuff is invalid.
		$client = Client::newFromContext( $this->getContext(), $runToken, $clientID );

		$this->setData( array(
			'status' => 'ok',
			'confUpdate' => array(
				'client' => $conf->client
			),
		) );
	}
}
