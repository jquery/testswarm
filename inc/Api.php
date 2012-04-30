<?php
/**
 * The Api class manages the response for requests via api.php,
 *
 * @author Timo Tijhof, 2012
 * @since 1.0.0
 * @package TestSwarm
 */
class Api {
	/**
	 * @var $context TestSwarmContext
	 */
	protected $context;

	protected $format = "format";
	protected $response = array(
		"error" => array(
			"internal-error" => "No response data given.",
		),
	);

	protected static $formats = array(
		"json",
		"jsonp",
		"php",
		"debug",
	);

	// These formats will not be executed in a logged-in context
	protected static $greyFormats = array(
		"jsonp",
	);

	public static function isGreyFormat( $format ) {
		return in_array( $format, self::$greyFormats );
	}

	public function setFormat( $format ) {
		if ( in_array( $format, self::$formats ) ) {
			$this->format = $format;
		} else {
			throw new SwarmException( "Unsupported API format `$format`." );
		}
	}

	public function setResponse( Array $response ) {
		$this->response = $response;
	}

	public function output() {
		switch ( $this->format ) {
			case "json":
				header( "Content-Type: application/json; charset=utf-8" );
				echo json_encode( $this->response );
				break;

			// http://stackoverflow.com/a/8811412/319266
			case "jsonp":
				header( "Content-Type: text/javascript; charset=utf-8" );
				$callback = $this->context->getRequest()->getVal( "callback", "" );
				echo
					preg_replace( "/[^][.\\'\\\"_A-Za-z0-9]/", "", $callback )
					. "("
					. json_encode( $this->response )
					. ")";
				break;

			// https://svn.wikimedia.org/viewvc/mediawiki/trunk/phase3/includes/api/ApiFormatPhp.php?revision=103273&view=markup
			case "php":
				header( "Content-Type: application/vnd.php.serialized; charset=utf-8" );
				echo serialize( $this->response );
				break;

			// http://svn.wikimedia.org/viewvc/mediawiki/trunk/phase3/includes/api/ApiFormatDump.php?revision=70727&view=markup
			case "debug":
				$debugPage = ApiDebugPage::newFromContext( $this->context );
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
