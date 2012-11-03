<?php
/**
 * Class to extract information from a user agent string.
 *
 * @author Timo Tijhof, 2012
 * @since 1.0.0
 * @package TestSwarm
 */
class BrowserInfo {

	/**
	 * @var $context TestSwarmContext
	 */
	private $context;

	/**
	 * @var $rawUserAgent string
	 */
	protected $rawUserAgent = "";

	/**
	 * @var $uaData stdClass object: Object returned by UA::parse.
	 */
	protected $uaData;

	/**
	 * @var $swarmUaItem stdClass object
	 */
	protected $swarmUaItem;

	/**
	 * @var $swarmUaIndex stdClass object: Cached object of parsed defaultSettings.json browserSets
	 */
	protected static $swarmUaIndex;

	/** @return object */
	public static function getSwarmUAIndex() {
		// Lazy-init and cache
		if ( self::$swarmUaIndex === null ) {
			global $swarmInstallDir;
      global $swarmContext;

			// Convert from array with string values
			// to an object with boolean values
        $swarmUaIndex = new stdClass();
        $browserSets = $swarmContext->getConf()->browserSets;
			foreach ( $browserSets as $browserSetName => $browserSet ) {
				foreach ( $browserSet as $browserSetIndex => $uaID ) {
					$swarmUaIndex->$uaID = self::fakeUaData( $uaID );
				}
			}
			self::$swarmUaIndex = $swarmUaIndex;
		}
		return self::$swarmUaIndex;
	}

	/**
	 * @param $context TestSwarmContext
	 * @param $userAgent string
	 * @return BrowserInfo
	 */
	public static function newFromContext( TestSwarmContext $context, $userAgent ) {
		$bi = new self();
		$bi->context = $context;
		$bi->parseUserAgent( $userAgent );
		return $bi;
	}

	/**
	 * Create a new BrowserInfo object for the given user agent string.
	 *
	 * Instances may not be created directly, use the static newFromUA method instead
	 * @param $userAgent string
	 */
	protected function parseUserAgent( $userAgent ) {

		/**
		 * A ua-parser object looks like this (simplified version of the actual object)
		 * @source https://github.com/tobie/ua-parser
		 *
		 * stdClass Object (
		 *		[browser] => Firefox
		 *		[major] => 14
		 *		[minor] => 0
		 *		[patch] => 1
		 *		[version] => 14.0.1
		 *		[browserFull] => Firefox 14.0.1
		 *		[os] => Mac OS X
		 *		[osMajor] => 10
		 *		[osMinor] => 8
		 *		[osVersion] => 10.8
		 *		[osFull] => Mac OS X 10.8
		 *		[full] => Firefox 14.0.1/Mac OS X 10.8
		 *		[device] =>
		 *		[uaOriginal] => Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:14.0) Gecko/20100101 Firefox/14.0.1
		 * )
		 */

		$UAParserInstance = new UA();

		$uaparserData = $UAParserInstance->parse( $userAgent );

		$uaData = new stdClass();
		$uaData->osFull = $uaparserData->osFull;
		$uaData->browserName = $uaparserData->browser;
		$uaData->browserVersion = $uaparserData->version;
		$uaData->browserFull = $uaparserData->browserFull;

		$uaData->swarmClass = strtolower( str_replace( ' ', '_', $uaData->browserName ) );

		$this->rawUserAgent = $userAgent;
		$this->uaData = $uaData;

		return $this;
	}

	/** @return string */
	public function getRawUA() {
		return $this->rawUserAgent;
	}

	/** @return object */
	public function getUaData() {
		return $this->uaData;
	}

	/**
	 * This method makes sure that the properties we need
	 * on display pages are present. The reason we have to calculate these manually
	 * is because on the HomePage, JobPage, UserPage etc. we only have the uaID,
	 * not the user agent, so we need to engineer the rest.
	 * @return object{ }
	 */
	protected static function fakeUaData( $uaID ) {
		$parts = explode( '|', $uaID , 2 );

		$uaData = new stdClass();
		$uaData->osFull = '';
		$uaData->browserName = $parts[0];
		$uaData->browserVersion = $parts[1];
		$uaData->browserFull = "$parts[0] $parts[1]";

		$uaData->swarmClass = strtolower( str_replace( ' ', '_', $uaData->browserName ) );
		$uaData->id = $uaID;

		return $uaData;
	}

	/**
	 * Find the uaID as configured in browserSets that best matches the
	 * current user-agent.
	 * A browserSet is an array of uaIDs. Format:
	 * @var {object} uaID: '<browserName>|<browserVersion>'.
	 * The version numbers can be as many digets as desired (e.g. Foo|9, Foo|9.5, Foo 9.52 etc.)
	 * @return object|false
	 */
	public function getSwarmUaItem() {

		// Lazy-init and cache
		if ( $this->swarmUaItem === null ) {
			$browserSets = $this->context->getConf()->browserSets;
			$uaData = $this->getUaData();
			$found = false;
			$precision = 0;
			foreach ( $browserSets as $browserSetName => $browserSet ) {
				foreach ( $browserSet as $browserSetIndex => $uaID ) {
					if ( strpos( "{$uaData->browserName}|{$uaData->browserVersion}", $uaID ) === 0 && strlen( $uaID ) > $precision ) {
						$found = $uaData;
						$found->id = $uaID;
						$precision = strlen( $uaID );
						break;
					}
				}
			}

			$this->swarmUaItem = $found;
		}
		return $this->swarmUaItem;
	}

	/** @return bool */
	public function isInSwarmUaIndex() {
		return (bool)$this->getSwarmUaItem();
	}

	/** @return string|null */
	public function getSwarmUaID() {
		$uaData = $this->getSwarmUaItem();
		return $uaData ? $uaData->id : null;
	}

	/** Don't allow direct instantiations of this class, use newFromContext instead */
	private function __construct() {}
}
