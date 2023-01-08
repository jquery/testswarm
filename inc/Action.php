<?php
/**
 * Base class for querying data and perfoming write actions in TestSwarm.
 *
 * Used by the API and internally for the web pages.
 *
 * @author Timo Tijhof
 * @since 1.0.0
 * @package TestSwarm
 */

abstract class Action {
	/**
	 * @var $context TestSwarmContext: Needs to be protected instead of private
	 * to allow Action sub classes to access the context.
	 */
	protected $context;

	/**
	 * @var $error string|false: Boolean false if there are no errors,
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
	 * For "item"-based actions, the item value is to be retrieved from
	 * WebRequest::getVal( 'item' ); Form-based actions should use
	 * WebRequest::wasPosted() to check whether it is indeed POSTed, and may
	 * want to redirect after that (PRG <https://en.wikipedia.org/wiki/Post/Redirect/Get>).
	 *
	 * @return void
	 */
	abstract public function doAction();

	/**
	 * Can be called in 2 ways:
	 * - Code and message:
	 * @param string|array{code:int,info?:string|null} $errorCode
	 * @param string $errorMsg [optional]
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
	 * Enforce authentication requirement.
	 * Actions need to provide authentication with the request.
	 * By design this method does not succeed if there is a valid session but
	 * not tokens. The user session for the GUI must not be used here (to prevent CSRF).
	 *
	 * @param string $project [optional] If given, authentication is only
	 *  considered valid if the the user has authenticated for this project.
	 * @return false|string project ID.
	 */
	final protected function doRequireAuth( $project = null ) {
		$db = $this->getContext()->getDB();
		$request = $this->getContext()->getRequest();
		$auth = $this->getContext()->getAuth();

		if ( !$request->wasPosted() ) {
			$this->setError( 'requires-post' );
			return false;
		}

		$authID = $request->getVal( 'authID' );
		$authToken = $request->getVal( 'authToken' );

		if ( !$authID || !$authToken ) {
			$this->setError( 'missing-parameters', 'One or more required authentication parameters were not submitted.' );
			return false;
		}

		if ( is_string( $project ) && $project !== $authID ) {
			$this->setError( 'unauthorized' );
			return false;
		}

		// Authentication could be from session token in the GUI
		if ( $auth && $authID === $auth->project->id && $authToken === $auth->sessionToken ) {
			return $auth->project->id;
		}

		// Or through API with auth token
		$projectRow = $db->getRow(str_queryf(
			'SELECT
				id
			FROM projects
			WHERE id = %s
			AND   auth_token = %s;',
			$authID,
			sha1( $authToken )
		));

		if ( !$projectRow ) {
			$this->setError( 'unauthorized' );
			return false;
		}

		return $projectRow->id;
	}

	final public function getError() {
		return $this->error ? $this->error : false;
	}

	/**
	 * @param mixed $data
	 */
	final protected function setData( $data ) {
		// Recursively convert objects to arrays using json_decode/json_encode
		$this->data = json_decode( json_encode2( $data ), true );
	}

	/**
	 * @return mixed
	 */
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
	 * @param array &$target The array the keys should be added to, is passed by
	 * reference, so it will be modified!
	 * @param string $tsRawUTC
	 * @param string $prefix [optional] If given, this string will be prefixed to
	 * the added keys, and the rest of the name ucfirst'ed resulting in:
	 * "rawUTC" or "prefixRawUTC" respectively.
	 */
	final protected static function addTimestampsTo( &$target, $tsRawUTC, $prefix = null ) {
			// PHP's "c" claims to be ISO compatible but prettyDateJS disagrees
			// ("2004-02-12T15:19:21+00:00" vs. "2004-02-12T15:19:21Z").
			// Constructing format manually instead.
			$tsISO = gmdate( "Y-m-d\TH:i:s\Z", gmstrtotime( $tsRawUTC ) );

			if ( is_array( $target ) ) {
				$target[( $prefix ? "{$prefix}RawUTC" : 'rawUTC' )] = $tsRawUTC;
				$target[( $prefix ? "{$prefix}ISO" : 'ISO' )] = $tsISO;
				$target[( $prefix ? "{$prefix}LocalFormatted" : 'localFormatted' )] = date( 'r', gmstrtotime( $tsRawUTC ) );
				$target[( $prefix ? "{$prefix}LocalShort" : 'localShort' )] = date( 'j M Y', gmstrtotime( $tsRawUTC ) );
			} else {
				throw new SwarmException( 'Invalid arguments to ' . __METHOD__ );
			}
	}

	final public static function newFromContext( TestSwarmContext $context ) {
		// @phan-suppress-next-line PhanTypeInstantiateAbstractStatic
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
