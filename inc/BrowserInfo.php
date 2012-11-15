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
	 * @var TestSwarmContext
	 */
	private $context;

	/**
	 * @var string
	 */
	protected $rawUserAgent = '';

	/**
	 * @var stdClass: Object returned by UA::parse.
	 */
	protected $uaData;

	/**
	 * @var stdClass
	 */
	protected $swarmUaItem;

	/**
	 * @var stdClass: Cache for getBrowserIndex()
	 */
	protected static $browserIndex;

	/** @return object */
	public static function getBrowserIndex() {
		// Lazy-init and cache
		if ( self::$browserIndex === null ) {
			global $swarmInstallDir, $swarmContext;

			// Convert from array with string values
			// to an object with boolean values
			$browserIndex = new stdClass();
			$browserSets = $swarmContext->getConf()->browserSets;
			foreach ( $browserSets as $browserSet => $browsers ) {
				foreach ( $browsers as $uaID => $uaData ) {
					$keys = array_keys(get_object_vars(
						BrowserInfo::newFromContext( $swarmContext, '-' )->getUaData()
					));
					$data = new stdClass();
					// Filter out unwanted properties, and set missing properties.
					// (browserSets can be very precise or very generic).
					foreach ( $keys as $key ) {
						$data->$key = isset( $uaData->$key ) ? $uaData->$key : '';
					}
					$data->displayInfo = self::getDisplayInfo( $data );

					$browserIndex->$uaID = $data;
				}
			}
			self::$browserIndex = $browserIndex;
		}
		return self::$browserIndex;
	}

	/**
	 * @param TestSwarmContext $context
	 * @param string $userAgent
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
	 * @param string $userAgent
	 */
	protected function parseUserAgent( $userAgent ) {

		/**
		 * A ua-parser object looks like this (simplified version of the actual object)
		 * @source https://github.com/tobie/ua-parser
		 *
		 * stdClass Object (
		 *		[family] => Firefox
		 *		[major] => 14
		 *		[minor] => 0
		 *		[patch] => 1
		 *		[version] => 14.0.1
		 *		[browserFull] => Firefox 14.0.1
		 *		[os] => Mac OS X
		 *		[osMajor] => 10
		 *		[osMinor] => 8
		 *		[osPatch] => 2
		 *		[osVersion] => 10.8.2
		 *		[osFull] => Mac OS X 10.8.2
		 *		[full] => Firefox 14.0.1/Mac OS X 10.8.2
		 *		[device] =>
		 *		[deviceMajor] =>
		 *		[deviceMinor] =>
		 *		[uaOriginal] => Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8.2; rv:14.0) Gecko/20100101 Firefox/14.0.1
		 * )
		 */

		$UAParserInstance = new UA();

		$uaparserData = $UAParserInstance->parse( $userAgent );

		$uaData = new stdClass();

		$uaData->browserFamily = $uaparserData->family;
		$uaData->browserMajor = $uaparserData->major;
		$uaData->browserMinor = $uaparserData->minor;
		$uaData->browserPatch = $uaparserData->patch;

		$uaData->osFamily = $uaparserData->os;
		$uaData->osMajor = $uaparserData->osMajor;
		$uaData->osMinor = $uaparserData->osMinor;
		$uaData->osPatch = $uaparserData->osPatch;

		$uaData->deviceFamily = $uaparserData->device;
		$uaData->deviceMajor = $uaparserData->deviceMajor;
		$uaData->deviceMinor = $uaparserData->deviceMinor;

		$uaData->displayInfo = self::getDisplayInfo( $uaData );

		$this->rawUserAgent = $userAgent;
		$this->uaData = $uaData;

		return $this;
	}

	/**
	 * @param array|object $uaData
	 * @param string $prefix: Prefix for CSS classes.
	 * @return array
	 */
	protected static function getDisplayInfo( $uaData, $prefix = 'swarm-' ) {
		$uaData = (object) $uaData;
		$classes = array();
		$classes[] = $prefix . 'browser';
		if ( $uaData->browserFamily ) {
			$browserFamily = strtolower( str_replace( ' ', '_', $uaData->browserFamily ) );
			$classes[] = $prefix . 'browser-' . $browserFamily;
			if ( $uaData->browserMajor ) {
				$classes[] = $prefix . 'browser-' . $browserFamily . '-' . intval( $uaData->browserMajor );
			}
		}
		if ( $uaData->osFamily ) {
			$classes[] = $prefix . 'os';
			$osFamily = strtolower( str_replace( ' ', '_', $uaData->osFamily ) );
			$classes[] = $prefix . 'os-' . $osFamily;
			if ( $uaData->osMajor ) {
				$classes[] = $prefix . 'os-' . $osFamily . '-' . intval( $uaData->osMajor );
			}
		}
		if ( $uaData->deviceFamily ) {
			$classes[] = $prefix . 'device';
			$deviceFamily = strtolower( str_replace( ' ', '_', $uaData->deviceFamily ) );
			$classes[] = $prefix . 'device-' . $deviceFamily;
			if ( $uaData->deviceMajor ) {
				$classes[] = $prefix . 'device-' . $deviceFamily . '-' . intval( $uaData->deviceMajor );
			}
		}
		$title = array();
		if ( $uaData->browserFamily ) {
			$title[] = rtrim("$uaData->browserFamily $uaData->browserMajor.$uaData->browserMinor.$uaData->browserPatch", '. ');
		}
		if ( $uaData->osFamily ) {
			$title[] = rtrim("$uaData->osFamily $uaData->osMajor.$uaData->osMinor.$uaData->osPatch", '. ');
		}
		if ( $uaData->deviceFamily ) {
			$title[] = rtrim("$uaData->deviceFamily $uaData->deviceMajor.$uaData->deviceMinor", '. ');
		}
		return array(
			'class' => implode( ' ', $classes ),
			'title' => implode( '/', $title ),
		);
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
	 * Find the uaID as configured in browserSets that best matches the
	 * current user-agent and return the uaData from the browser index.
	 * @return object: Object from browserindex (with additional 'id' property).
	 */
	public function getSwarmUaItem() {

		// Lazy-init and cache
		if ( $this->swarmUaItem === null ) {
			$browserIndex = self::getBrowserIndex();
			$myUaData = $this->getUaData();
			$foundPrecision = 0;
			$found = false;
			foreach ( $browserIndex as $uaID => $uaData ) {
				$diff = array_diff_assoc( (array)$uaData, (array)$myUaData );
				unset( $diff['displayInfo'] );
				$precision = count( (array)$uaData ) - count( array_values( $diff ) );
				if ( implode( '', array_values( $diff ) ) === '' && $precision > $foundPrecision ) {
					$found = $uaData;
					$found->id = $uaID;
					$foundPrecision = $precision;
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
