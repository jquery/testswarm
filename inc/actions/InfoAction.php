<?php
/**
 * Get general information about this TestSwarm install.
 *
 * @author Timo Tijhof
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
		);

		$this->setData( $info );
	}
}
