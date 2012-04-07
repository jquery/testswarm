<?php
/**
 * The Action class is base for the TestSwarm actions.
 * Used in Pages and Apis.
 *
 * @author Timo Tijhof, 2012
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
	 * @var $error stroing|false: Boolean false if there are no errors,
	 * or one of the errorCodes.
	 */
	protected $error = false;

	protected static $errorCodes = array(
		// "internal-error" is exclusively for use by the exception handler
		"internal-error" => "An internal error occurred. Action could not be performed.",
		"invalid-input" => "One or more input fields were invalid.",
		"missing-parameters" => "One ore more required fields were not submitted.",
		"requires-post" => "This action requires a POST request.",
		"requires-auth" => "You are not authorized to perform this action.",
		"data-corrupt" => "Data was retreived but was found to be corrupt or incomplete.",
	);

	/**
	 * @var $data array: Data to give to the action handler (Page, Api).
	 */
	protected $data = array();

	/**
	 * Perform the actual action based on the current context.
	 * For "item"-based actions, the item value is to be retreived from
	 * WebRequest::getVal( "item" ); Form-based actions should use
	 * WebRequest::wasPosted() to check wether it is indeed POSTed, and may
	 * want to redirect after that (PRG <https://en.wikipedia.org/wiki/Post/Redirect/Get>).
	 */
	abstract public function doAction();

	/**
	 * Can be called in 2 ways:
	 * - Code and message:
	 * @param $errorCode string
	 * @param $errorMsg string [optional
	 * - Array with code and message:
	 * @param $param $error array: property "code" and optionally "info".
	 */
	final protected function setError( $errorCode, $errorMsg = null ) {
		if ( is_array( $errorCode ) && isset( $errorCode["code"] ) ) {
			$errorMsg = isset( $errorCode["info"] ) ? $errorCode["info"] : null;
			$errorCode = $errorCode["code"];
		}

		if ( !isset( $errorCode ) || !isset( self::$errorCodes[$errorCode] ) ) {
			throw new SwarmException( "Unrecognized error code used." );
		}

		$this->error = array(
			"code" => $errorCode,
			"info" => $errorMsg === null ? self::$errorCodes[$errorCode] : $errorMsg,
		);
	}

	final public function getError() {
		return $this->error ? $this->error : false;
	}

	/**
	 * @param $data mixed
	 */
	final protected function setData( $data ) {
		// Convert all objects to arrays with json_decode(json_encode
		$this->data = json_decode( json_encode( $data ), true );
	}

	final public function getData() {
		return $this->data;
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
