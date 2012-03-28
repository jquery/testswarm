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
	 * @var $error stroing|false: Boolean false if there are no errors,
	 * or one of the errorCodes.
	 */
	protected $error = false;

	protected static $errorCodes = array(
		"internal-error" => "An internal error occurred. Action could not be performed",
		"invalid-input" => "One or more input fields were invalid.",
		"missing-parameters" => "One ore more required fields were not submitted.",
		"requires-post" => "This action requires a POST action.",
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

	final protected function setError( $error ) {
		if ( !isset( self::$errorCodes[$error] ) ) {
			throw new SwarmException( "Unrecognized error code used." );
		}
		$this->error = $error;
	}

	final public function getError() {
		return $this->error
			? array(
				"code" => $this->error,
				"info" => self::$errorCodes[$this->error],
			) : false;
	}

	final protected function setData( Array $data ) {
		$this->data = $data;
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
