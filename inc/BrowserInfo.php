<?php
/**
 * Class to extract information from a user agent string.
 *
 * @since 0.3.0
 * @package TestSwarm
 */
class BrowserInfo {

	protected static $cache = array();

	protected $userAgent = "";
	protected $browserCodename;
	protected $browserVersion;
	protected $osCodename;

	protected $swarmUserAgentID;
	protected $swarmUserAgentName;

	/**
	 * @param $userAgent string
	 * @return BrowserInfo
	 */
	public static function newFromUA( $userAgent ) {
		// Cached already?
		if ( isset( self::$cache[$userAgent] ) ) {
			return self::$cache[$userAgent];
		}

		$bi = new self( $userAgent );
		return self::$cache[$userAgent] = $bi;
	}

	/**
	 * Get information from the TestSwarm useragents table.
	 * @param $bi BrowserInfo
	 * @return array|null Database row or null if no matches.
	 */
	public static function findSwarmUAFromBI( BrowserInfo $bi ) {
		$result = mysql_queryf(
			"SELECT
				id,
				name
			FROM
				useragents
			WHERE	engine = %s
			AND	%s REGEXP version;",
			$bi->getBrowserCodename(),
			$bi->getBrowserVersion()
		);

		if ( $row = mysql_fetch_array( $result ) ) {
			return $row;
		}

		return null;
	}

	/** @return string */
	public function getRawUA() {
		return $this->userAgent;
	}

	/** @return string|null */
	public function getBrowserCodename() {
		return $this->browserCodename;
	}

	/** @return string|null */
	public function getBrowserVersion() {
		return $this->browserVersion;
	}

	/** @return string|null */
	public function getOsCodename() {
		return $this->osCodename;
	}

	/**
	 * ID of the matching entry in the TestSwarm useragent database table.
	 * @return int|null
	 */
	public function getSwarmUserAgentID() {
		return $this->swarmUserAgentID;
	}

	/**
	 * ID of the matching entry in the TestSwarm useragent database table.
	 * @return int|null
	 */
	public function getSwarmUserAgentName() {
		return $this->swarmUserAgentName;
	}

	public function isKnownInTestSwarm() {
		return !is_null( $this->swarmUserAgentID ) && !is_null( $this->swarmUserAgentName );
	}

	public function loadSwarmUserAgentData() {
		$uaRow = self::findSwarmUAFromBI( $this );
		if ( $uaRow ) {
			$this->swarmUserAgentID = $uaRow["id"] ? intval( $uaRow["id"] ) : null;
			$this->swarmUserAgentName = $uaRow["name"] ? (string)$uaRow["name"] : null;
		}
	}

	/**
	 * Create a new BrowserInfo object for the given user agent string.
	 *
	 * Instances may not be created directly, use the static newFromUA method instead
	 * @param $userAgent string
	 */
	private function __construct( $userAgent ) {
		$lcUA = strtolower( $userAgent );

		// Version
		$version = null;
		if ( preg_match( "/.+(rv|webos|applewebkit|presto|msie|konqueror)[\/: ]([0-9a-z.]+)/", $lcUA, $m ) ) {
			$version = $m[2];
		}
		if ( preg_match( "/.*(webos|fennec|series60|blackberry[0-9]*[a-z]*)[\/: ]([0-9a-z.]+)/", $lcUA, $m ) ) {
			$version = $m[2];
		}
		if ( preg_match("/ms-rtc lm 8/", $lcUA) ) {
			$version = "8.0as7.0";
		}
		$this->browserVersion = $version;

		// Browser/Engine code
		$browser = null;
		if ( strpos($lcUA, "msie") > -1 && strpos($lcUA, "windows phone") > -1 ) {
			$browser = "winmo";
		} elseif ( strpos($lcUA, "msie") > -1 ) {
			$browser = "msie";
		} elseif ( strpos($lcUA, "konqueror") > -1 ) {
			$browser = "konqueror";
		} elseif ( strpos($lcUA, "chrome") > -1 ) {
			$browser = "chrome";
		} elseif ( strpos($lcUA, "webos") > -1 ) {
			$browser = "webos";
		} elseif ( strpos($lcUA, "android") > -1 && strpos($lcUA, "mobile safari") > -1 ) {
			$browser = "android";
		} elseif ( strpos($lcUA, "series60") > -1 ) {
			$browser = "s60";
		} elseif ( strpos($lcUA, "blackberry") > -1 ) {
			$browser = "blackberry";
		} elseif ( strpos($lcUA, "opera mobi") > -1 ) {
			$browser = "operamobile";
		} elseif ( strpos($lcUA, "fennec") > -1 ) {
			$browser = "fennec";
		} elseif ( strpos($lcUA, "webkit") > -1 && strpos($lcUA, "mobile") > -1 ) {
			$browser = "mobilewebkit";
		} elseif ( strpos($lcUA, "webkit") > -1 ) {
			$browser = "webkit";
		} elseif ( strpos($lcUA, "presto") > -1 ) {
			$browser = "presto";
		} elseif ( strpos($lcUA, "gecko") > -1 ) {
			$browser = "gecko";
		}
		$this->browserCodename = $browser;

		// Operating system
		$os = null;
		if ( strpos($lcUA, "windows nt 6.1") > -1 ) {
			$os = "win7";
		} elseif ( strpos($lcUA, "windows nt 6.0") > -1 ) {
			$os = "vista";
		} elseif ( strpos($lcUA, "windows nt 5.2") > -1 ) {
			$os = "2003";
		} elseif ( strpos($lcUA, "windows nt 5.1") > -1 ) {
			$os = "xp";
		} elseif ( strpos($lcUA, "windows nt 5.0") > -1 ) {
			$os = "2000";
		} elseif ( strpos($lcUA, "blackberry") > -1 ) {
			$os = "blackberry";
		} elseif ( strpos($lcUA, "iphone") > -1 ) {
			$os = "iphone";
		} elseif ( strpos($lcUA, "ipod") > -1 ) {
			$os = "ipod";
		} elseif ( strpos($lcUA, "ipad") > -1 ) {
			$os = "ipad";
		} elseif ( strpos($lcUA, "symbian") > -1 ) {
			$os = "symbian";
		} elseif ( strpos($lcUA, "webos") > -1 ) {
			$os = "webos";
		} elseif ( strpos($lcUA, "android") > -1 ) {
			$os = "android";
		} elseif ( strpos($lcUA, "windows phone") > -1 ) {
			$os = "winmo";
		} elseif ( strpos($lcUA, "os x 10.4") > -1 || strpos($lcUA, "os x 10_4") > -1 ) {
			$os = "osx10.4";
		} elseif ( strpos($lcUA, "os x 10.5") > -1 || strpos($lcUA, "os x 10_5") > -1 ) {
			$os = "osx10.5";
		} elseif ( strpos($lcUA, "os x 10.6") > -1 || strpos($lcUA, "os x 10_6") > -1 ) {
			$os = "osx10.6";
		} elseif ( strpos($lcUA, "os x") > -1 ) {
			$os = "osx";
		} elseif ( strpos($lcUA, "linux") > -1 ) {
			$os = "linux";
		}
		$this->os = $os;

		// Try to load information about this user agent from the TestSwarm database
		$this->loadSwarmUserAgentData();

		return $this;
	}
}
