<?php
/**
 * "Info" action.
 *
 * @author Timo Tijhof, 2012
 * @since 1.0.0
 * @package TestSwarm
 */

class InfoAction extends Action {

	/**
	 * @actionNote This action takes no parameters.
	 */
	public function doAction() {
		$context = $this->getContext();
		$conf = $context->getConf();
		$request = $context->getRequest();

		$info = array(
			"software" => array(
				"website" => "https://github.com/jquery/testswarm",
				"versionInfo" => $context->getVersionInfo(),
			),
			"conf" => array(
				"general" => $conf->general,
				"web" => $conf->web,
				"client" => $conf->client,
			),
			"session" => array(
				"username" => $request->getSessionData( "username" ),
				"authenticated" => $request->getSessionData( "auth" ) === "yes",
			),
		);

		$this->setData( $info );
	}
}
