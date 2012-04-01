<?php
/**
 * Error "404" handler.
 *
 * @author Timo Tijhof, 2012
 * @since 0.3.0
 * @package TestSwarm
 */

class Error404Page extends Page {

	protected function initContent() {
		self::httpStatusHeader( 404 );

		$this->setTitle( "Error 404: " . Page::getHttpStatusMsg( 404 ) );

		return 'The page you requested could not be found.';
	}
}

