<?php
/**
 * The Action class is base for the TestSwarm actions.
 * Used in Pages and Apis.
 *
 * @author Timo Tijhof
 * @since 0.3.0
 * @package TestSwarm
 */

abstract class Action {
	/**
	 * @var $context TestSwarmContext: Needs to be protected instead of private
	 * in order for extending Api classes to access the context. 
	 */
	protected $context;

	/**
	 * @var $error array|false: Boolean false if there are no errors,
	 * or an array with a 'code' and 'info' property. The 'info' property
	 * is a humanreadable string explaining the error briefly. The 'code'
	 * property should be one of the following that should be machine
	 * readable to give respond in a certain way. Keep the unique codes
	 * limited.
	 * Possible codes: 'internal-error', 'invalid-input'
	 */
	protected $error = false;

	/**
	 * Perform the actual action based on the current context.
	 * For "item"-based actions, the item value is to be retreived from
	 * WebRequest::getVal( "item" ); Form-based actions should use
	 * WebRequest::wasPosted() to check wether it is indeed POSTed, and may
	 * want to redirect after that (PRG <https://en.wikipedia.org/wiki/Post/Redirect/Get>).
	 */
	abstract public function doAction();

	/**
	 * Useful utility function to send a redirect as reponse and close the request.
	 * @param $target string: Url
	 * @param $code int: 30x 
	 */
	protected function redirect( $target = '', $code = 302 ) {
		static $httpCodes = array(
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			307 => 'Temporary Redirect',
		);
		$httpCode = $httpCodes[$code];
		if ( !$httpCodes[$code] ) {
			throw new SwarmError( "Invalid redirect http code." );
		}

		session_write_close();
		header( $_SERVER["SERVER_PROTOCOL"] . " $code $httpCode", true, $code );
		header( "Content-Type: text/html; charset=utf-8" );
		header( 'Location: ' . $target );

		exit;
	}

	final public function getError() {
		return $this->error;
	}

	final public static function newFromContext( TestSwarmContext $context ) {
		$page = new static();
		$page->context = $context;
		return $page;
	}

	final protected function getContext() {
		return $this->context;
	}

	/** Don't allow direct instantiations of this class, use newFromContext instead. */
	final private function __construct() {}
}
