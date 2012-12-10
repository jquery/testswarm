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
			$userAgents = $swarmContext->getConf()->userAgents;
			foreach ( $userAgents as $uaID => $uaData ) {
				$keys = array_keys(get_object_vars(
					$swarmContext->getBrowserInfo()->getUaData()
				));
				$data = new stdClass();
				// Filter out unwanted properties, and set missing properties.
				foreach ( $keys as $key ) {
					$data->$key = isset( $uaData->$key ) ? $uaData->$key : '';
				}
				$data->displayInfo = self::getDisplayInfo( $data );

				$browserIndex->$uaID = $data;
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
	 * Utility to build a blank uaData object. Used when dealing with outdated
	 * or malformed uaIDs.
	 */
	public static function makeGenericUaData( $id = '-' ) {
		$uaData = new stdClass();

		$uaData->browserFamily =
		$uaData->browserMajor =
		$uaData->browserMinor =
		$uaData->browserPatch =
		$uaData->osFamily =
		$uaData->osMajor =
		$uaData->osMinor =
		$uaData->osPatch =
		$uaData->deviceFamily =
		$uaData->deviceMajor =
		$uaData->deviceMinor = '';

		$uaData->displayInfo = self::getDisplayInfo( $uaData );

		$uaData->displayInfo['title'] = "[ $id ]";

		return $uaData;
	}

	public static function sortUaData( $a, $b ) {
		$a = is_array( $a ) ? (object)$a : $a;
		$b = is_array( $b ) ? (object)$b : $b;
		return strnatcasecmp( $a->displayInfo['title'], $b->displayInfo['title'] );
	}

	public function sortUaId( $a, $b ) {
		$browserIndex = $this->getBrowserIndex();
		return self::sortUaData(
			isset( $browserIndex->$a ) ? $browserIndex->$a : self::makeGenericUaData( $a ),
			isset( $browserIndex->$b ) ? $browserIndex->$b : self::makeGenericUaData( $a )
		);
	}

	/**
	 * Create a new BrowserInfo object for the given user agent string.
	 * Instances may not be created directly, use the static newFromContext method instead.
	 *
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
		 *		[deviceVersion] =>
		 *		[deviceFull] =>
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
		$classes[] = $prefix . 'icon';

		if ( $uaData->browserFamily ) {
			$classes[] = $prefix . 'browser';
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
		// "Smart" way of concatenating the parts, and trimming off empty parts
		// (Trim trailing dots or spaces indicate two adjacent empty parts).
		// Also remove the wildcard from the interface label (only relevant to the backend)
		if ( $uaData->browserFamily ) {
			$title[] = rtrim("$uaData->browserFamily $uaData->browserMajor.$uaData->browserMinor.$uaData->browserPatch", '. *');
		}
		if ( $uaData->osFamily ) {
			$title[] = rtrim("$uaData->osFamily $uaData->osMajor.$uaData->osMinor.$uaData->osPatch", '. *');
		}
		if ( $uaData->deviceFamily ) {
			$title[] = rtrim("$uaData->deviceFamily $uaData->deviceMajor.$uaData->deviceMinor", '. *');
		}
		return array(
			'class' => implode( ' ', $classes ),
			'title' => implode( '/', $title ),
			'labelText' => implode( "\n", $title ),
			'labelHtml' => implode( '<br/>', array_map( 'htmlspecialchars', $title ) ),
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
	 * Process the wildcard syntax allowed at the end
	 * of uaData property values.
	 * This was originally created to handle the different
	 * pseudo-patch releases from Opera. Opera 11.62 for instance
	 * some people want to treat it like 11.6.2 because BrowserStack
	 * has 11.60 and 11.62 mixed up under the id "11.6". So we can
	 * use "browserMinor: 6*" in the userAgents configuration,
	 * which will tolerate anything. Use carfully though,
	 * theotically this means it will match X.6, X.60 and X.600,
	 * X.6foo, X.61-alpha etc.
	 * NB: Wildcards are only allowed at the end of values. And because
	 * it doesn't make sense to have more than one in that case, it
	 * only looks for one.
	 * NB: Pass the objects as copied arrays to this function, they will
	 * be mutated otherwise.
	 *
	 * @param Array $uaData: browserSet configuration item
	 * @param Array $myUaData: parsed ua-browser object
	 * @return number|bool: If they match, how precise it is (higher is better),
	 * or boolean false.
	 */
	private function compareUaData( Array $uaData, Array $myUaData ) {
		unset( $uaData['displayInfo'], $myUaData['displayInfo'] );

		foreach ( $uaData as $key => $value ) {
			if ( preg_match( '/(Major|Minor|Patch)$/', $key ) && substr( $value, -1 ) === '*' ) {
				$uaData[$key] = substr( $value, 0, -1 );
				// Shorten myUaData's value to just before the
				// position of the wildcard in uaData's value.
				$myUaData[$key] = substr( $myUaData[$key], 0, strlen( $uaData[$key] ) );
			}
		}
		$diff = array_diff_assoc( $uaData, $myUaData );
		$precision = count( $uaData ) - count( array_values( $diff ) );
		if ( implode( '', array_values( $diff ) ) === '' ) {
			return $precision;
		}
		return false;
	}

	/**
	 * Find the uaID in browserIndex that best matches the current
	 * user-agent and return the uaData from the browser index.
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
				$precision = $this->compareUaData( (array)$uaData, (array)$myUaData );
				if ( $precision !== false && $precision > $foundPrecision ) {
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
