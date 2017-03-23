<?php
/**
 * Respond to requests for the API.
 *
 * @author Timo Tijhof
 * @since 1.0.0
 * @package TestSwarm
 */
class Api {
	/**
	 * @var $context TestSwarmContext
	 */
	protected $context;

	protected $action;

	protected $format = 'json';

	protected $response = array(
		'error' => array(
			'internal-error' => 'No response data given.',
		),
	);

	protected static $formats = array(
		'json',
		'jsonp',
		'debug',
	);

	// These formats will not be executed in a logged-in context
	protected static $greyFormats = array(
		'jsonp',
	);

	public static function isGreyFormat( $format ) {
		return in_array( $format, self::$greyFormats );
	}

	public static function getFormats() {
		return self::$formats;
	}

	public function setAction( $action ) {
		$this->action = $action;
	}

	public function setFormat( $format ) {
		if ( !in_array( $format, self::$formats ) ) {
			throw new SwarmException( "Unsupported API format `$format`." );
		}
		$this->format = $format;
	}

	public function setResponse( Array $response ) {
		$this->response = $response;
	}

	public function output() {
		switch ( $this->format ) {
			case 'json':
				header( 'Content-Type: application/json; charset=utf-8' );
				echo json_encode2( $this->response );
				break;

			// http://stackoverflow.com/a/8811412/319266
			case 'jsonp':
				header( 'Content-Type: text/javascript; charset=utf-8' );
				$callback = $this->context->getRequest()->getVal( 'callback', '' );
				echo
					preg_replace( "/[^][.\\'\\\"_A-Za-z0-9]/", '', $callback )
					. '('
					. json_encode2( $this->response )
					. ')';
				break;

			case 'debug':
				$debugPage = ApiDebugPage::newFromContext( $this->context );
				$debugPage->setActionName( $this->action );
				$debugPage->setApiResponse( $this->response );
				$debugPage->output();
				break;
		}
	}

	final public static function newFromContext( TestSwarmContext $context ) {
		$api = new self();
		$api->context = $context;
		return $api;
	}

	/** Don't allow direct instantiations of this class, use newFromContext instead. */
	final private function __construct() {}
}
