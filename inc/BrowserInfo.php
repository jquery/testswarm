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
	 * @var $uaparserData stdClass object: Object returned by UA::parse.
	 */
	protected $uaparserData;

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

			// Convert from array with string values
			// to an object with boolean values
			$swarmUaIndex = new stdClass;
			$swarmInstallDir = dirname( __DIR__ );
			$defaultSettingsJSON = "$swarmInstallDir/config/defaultSettings.json";
			$defaultSettings = json_decode( file_get_contents( $defaultSettingsJSON ) );
			$browserSets = $defaultSettings->browserSets;
			foreach ( $browserSets as $browserSetName => $browserSet ) {
				foreach ( $browserSet as $browserSetIndex => $uaID ) {

					$swarmUaIndex->$uaID = new stdClass();
					$swarmUaIndex->$uaID->displaytitle = self::formatDisplayTitle( $uaID );

					list($browserName) = explode("|", $uaID);
					$swarmUaIndex->$uaID->displayicon = strtolower( str_replace( ' ', '_', $browserName ) );

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
		 *		[isMobileDevice] =>
		 *		[isMobile] =>
		 *		[isSpider] =>
		 *		[isTablet] =>
		 *		[isComputer] => 1
		 *		[major] => 14
		 *		[minor] => 0
		 *		[build] => 1
		 *		[patch] => 1
		 *		[browser] => Firefox
		 *		[family] => Firefox
		 *		[version] => 14.0.1
		 *		[browserFull] => Firefox 14.0.1
		 *		[isUIWebview] =>
		 *		[osMajor] => 10
		 *		[osMinor] => 8
		 *		[os] => Mac OS X
		 *		[osVersion] => 10.8
		 *		[osFull] => Mac OS X 10.8
		 *		[full] => Firefox 14.0.1/Mac OS X 10.8
		 *		[uaOriginal] => Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:14.0) Gecko/20100101 Firefox/14.0.1
		 *		)
		 */

		$UAParserInstance = new UA();

		$uaparserData = $UAParserInstance->parse( $userAgent );

		$this->rawUserAgent = $userAgent;
		$this->uaparserData = $uaparserData;

		return $this;
	}

	/** @return string */
	public function getRawUA() {
		return $this->rawUserAgent;
	}

	/** @return Selective array with UAParser results */
	public function getUAParser() {
		return array_intersect_key(
			(array)$this->uaparserData,
			array_flip(array( "os", "browser", "version", "major", "minor" ))
		);
	}

	/** @return string */
	public static function formatBrowserName( $name ) {
		return strtolower( str_replace( ' ', '_', $name ) );
	}

	/** @return string */
	public static function formatDisplayTitle( $name ) {
		$splitNameAndVersion = preg_replace( '/\|/', ' ', $name, 1);
		return str_replace( '|', '.', $splitNameAndVersion );
	}

	/** @return string */
	public static function formatUA( $displayicon, $displaytitle, $id ) {}
		$newUa->displayicon = self::formatBrowserName( $displayicon );
		$newUa->displaytitle = self::formatDisplayTitle( $displaytitle );
		$newUa->id = $id;
		return $newUa;
	}

	/** @return object|false */
	public function getSwarmUaItem() {

		// Lazy-init and cache
		if ( $this->swarmUaItem === null ) {
			$browserSets = $this->context->getConf()->browserSets;
			$uaParserData = $this->getUAParser();
			$found = false;
			foreach ( $browserSets as $browserSetName => $browserSet ) {
				foreach ( $browserSet as $browserSetIndex => $uaID ) {
					if ( $uaID === "{$uaParserData['browser']}|{$uaParserData['major']}|{$uaParserData['minor']}" ) {
						$found = self::formatUA( $uaParserData['browser'] , $uaID, $uaID);
						break;
					} elseif ( $uaID === "{$uaParserData['browser']}|{$uaParserData['major']}" ) {
						$found = self::formatUA( $uaParserData['browser'] , $uaID, $uaID);
						break;
					} elseif ( $uaID === $uaParserData['browser'] ) {
						$found = self::formatUA( $uaParserData['browser'] , $uaID, $uaID);
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
		return $this->getSwarmUaItem() ? $this->getSwarmUaItem()->id : null;
	}

	/** Don't allow direct instantiations of this class, use newFromContext instead */
	private function __construct() {}
}
