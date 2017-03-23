<?php
/**
 * Help action for the API, indicating which actions and formats are available.
 *
 * @author Timo Tijhof
 * @since 1.0.0
 * @package TestSwarm
 */
class ApihelpAction extends Action {

	/**
	 * @actionNote This action takes no parameters.
	 */
	public function doAction() {
		global $swarmAutoLoadClasses;
		$context = $this->getContext();
		$conf = $context->getConf();
		$request = $context->getRequest();

		$actions = array();
		foreach ( $swarmAutoLoadClasses as $class => $file ) {
			if ( strlen( $class ) > 6 && substr( $class, -6 ) === 'Action' ) {
				$actions[] = strtolower( substr( $class, 0, -6 ) );
			}
		}

		$this->setData( array(
			'action' => $actions,
			'format' => Api::getFormats(),
		) );
	}
}
