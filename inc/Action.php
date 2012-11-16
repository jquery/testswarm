<?php
/**
 * The Action class is base for the TestSwarm actions.
 * Used in Pages and Apis.
 *
 * @author Timo Tijhof, 2012
 * @since 1.0.0
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
		// internal-error is exclusively for use by the exception handler
		'internal-error' => 'An internal error occurred. Action could not be performed.',
		'invalid-input' => 'One or more input fields were invalid.',
		'missing-parameters' => 'One ore more required fields were not submitted.',
		'requires-post' => 'This action requires a POST request.',
		'unauthorized' => 'This action requires authorization. The token or username may be missing or invalid.',
		'data-corrupt' => 'Data was retreived but was found to be corrupt or incomplete.',
	);

	/**
	 * @var $data array: Data to give to the action handler (Page, Api).
	 */
	protected $data = array();

	/**
	 * Perform the actual action based on the current context.
	 * For "item"-based actions, the item value is to be retreived from
	 * WebRequest::getVal( 'item' ); Form-based actions should use
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
		if ( is_array( $errorCode ) && isset( $errorCode['code'] ) ) {
			$errorMsg = isset( $errorCode['info'] ) ? $errorCode['info'] : null;
			$errorCode = $errorCode['code'];
		}

		if ( !isset( $errorCode ) || !isset( self::$errorCodes[$errorCode] ) ) {
			throw new SwarmException( 'Unrecognized error code used.' );
		}

		$this->error = array(
			'code' => $errorCode,
			'info' => $errorMsg === null ? self::$errorCodes[$errorCode] : $errorMsg,
		);
	}

	/**
	 * Enforce user authentication. Centralized logic.
	 * @param string|int $user [optional] Additionally, verify that the
	 * user is of a certain ID or username.
	 * @return false|int: user id
	 */
	final protected function doRequireAuth( $user = null ) {
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();

		if ( !$request->wasPosted() ) {
			$this->setError( 'requires-post' );
			return false;
		}

		$authUsername = $request->getVal( 'authUsername' );
		$authToken = $request->getVal( 'authToken' );

		if ( !$authUsername || !$authToken ) {
			$this->setError( 'missing-parameters' );
			return false;
		}

		if ( is_string( $user ) && $user !== $authUsername ) {
			$this->setError( 'unauthorized' );
			return false;
		}

		// Check authentication
		$userRow = $db->getRow(str_queryf(
			'SELECT
				id
			FROM users
			WHERE name = %s
			AND   auth = %s;',
			$authUsername,
			$authToken
		));

		if ( !$userRow ) {
			$this->setError( 'unauthorized' );
			return false;
		}

		$userId = (int)$userRow->id;

		if ( is_int( $user ) && $user !== $userId ) {
			$this->setError( 'unauthorized' );
			return false;
		}

		return $userId;
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

	/**
	 * Central method to create keys in an Action response related to time for consistency.
	 * Adds three keys:
	 * - RawUTC (14-digit timestamp in UTC, as found in the database)
	 * - ISO (naturally in UTC aka Zulu)
	 * - Localized format according to the swarm configuration.
	 *
	 * @param &$target array: The array the keys should be added to, is passed by
	 * reference, so it will be modified!
	 * @param $tsRawUTC string:
	 * @param $prefix string: [optional] If given, this string will be prefixed to
	 * the added keys, and the rest of the name ucfirst'ed resulting in:
	 * "rawUTC" or "prefixRawUTC" respectively.
	 */
	final protected static function addTimestampsTo( &$target, $tsRawUTC, $prefix = null ) {
			$tsLocalFormatted = strftime( '%c', gmstrtotime( $tsRawUTC ) );

			// PHP's "c" claims to be ISO compatible but prettyDateJS disagrees
			// ("2004-02-12T15:19:21+00:00" vs. "2004-02-12T15:19:21Z").
			// Constructing format manually instead.
			$tsISO = gmdate( "Y-m-d\TH:i:s\Z", gmstrtotime( $tsRawUTC ) );

			if ( is_array( $target ) ) {
				$target[( $prefix ? "{$prefix}RawUTC" : 'rawUTC' )] = $tsRawUTC;
				$target[( $prefix ? "{$prefix}ISO" : 'ISO' )] = $tsISO;
				$target[( $prefix ? "{$prefix}LocalFormatted" : 'localFormatted' )] = $tsLocalFormatted;
			} else {
				throw SwarmException( 'Invalid arguments to ' . __METHOD__ );
			}
	}

	final public static function newFromContext( TestSwarmContext $context ) {
		$action = new static();
		$action->context = $context;
		return $action;
	}

	final protected function getContext() {
		return $this->context;
	}

	/** Don't allow direct instantiations of this class, use newFromContext instead. */
	final private function __construct() {}
}
