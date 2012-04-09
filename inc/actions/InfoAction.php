<?php
/**
 * "Info" action.
 *
 * @author Timo Tijhof, 2012
 * @since 0.3.0
 * @package TestSwarm
 */

class InfoAction extends Action {

	public function doAction() {
		$conf = $this->getContext()->getConf();

		$info = array(
			"version" => swarmGetVersion( $this->getContext() ),
			"conf" => array(
				"general" => $conf->general,
				"web" => $conf->web,
				"client" => $conf->client,
			),
			"session" => array(
				"username" => $this->getContext()->getRequest()->getSessionData( "username" ),
				"authenticated" => $this->getContext()->getRequest()->getSessionData( "auth" ) === "yes",
			),
		);

		$this->setData( $info );
	}
}
