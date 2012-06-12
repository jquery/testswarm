<?php
/**
 * Class for interacting with request and session data.
 *
 * Based on
 * - https://svn.toolserver.org/svnroot/krinkle/trunk/common/Request.php
 * - https://svn.wikimedia.org/viewvc/mediawiki/trunk/phase3/includes/WebRequest.php?view=markup&pathrev=114154
 *
 * @author Timo Tijhof, 2012
 * @since 1.0.0
 * @package TestSwarm
 */

class WebRequest {
	private $context;

	protected $raw;
	private $ip;

	/**
	 * @param $context TestSwarmContext
	 * @return WebRequest
	 */
	public static function newFromContext( TestSwarmContext $context ) {
		$req = new self();
		$req->context = $context;
		$req->checkMagicQuotes();

		// POST overrides GET data
		// We don't use $_REQUEST here to avoid interference from cookies...
		$req->raw = $_POST + $_GET;
		return $req;
	}

	public function getRawVal( $arr, $key, $default ) {
		return isset( $arr[$key] ) ? $arr[$key] : $default;
	}

	/**
	 * Get a value from the array as string. Array values are discarded,
	 * use getArray() instead.
	 * @return string|null
	 */
	public function getVal( $key, $default = null ) {
		$val = $this->getRawVal( $this->raw, $key, $default );
		if ( is_array( $val ) ) {
			$val = $default;
		}
		if ( is_null( $val ) ) {
			return null;
		} else {
			return (string)$val;
		}
	}

	/** @return array|null */
	public function getArray( $name, $default = null ) {
		$val = $this->getRawVal( $this->raw, $name, $default );
		if ( is_null( $val ) ) {
			return null;
		} else {
			return (array)$val;
		}
	}

	/** @return bool */
	public function getBool( $key ) {
		return !!array_key_exists( $key, $this->raw );
	}

	/** @return int */
	public function getInt( $key, $default = 0 ) {
		return intval( $this->getVal( $key, $default ) );
	}

	/**
	 * @example:
	 * $request->hasKeys( 'foo', 'bar' );
	 * @example:
	 * $request->hasKeys( array( 'foo', 'bar' ) );
	 *
	 * @return bool
	 */
	public function hasKeys( $keys/* , .. */ ) {
		$keys = is_array( $keys ) ? $keys : func_get_args();
		foreach ( $keys as $key ) {
			if ( !array_key_exists( $key, $this->raw ) ) {
				return false;
			}
		}
		return true;
	}

	/** @return bool */
	public function wasPosted() {
		return isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] == 'POST';
	}

	public function getSessionData( $key, $default = null ) {
		if ( !isset( $_SESSION[$key] ) ) {
			return $default;
		}

		return $_SESSION[$key];
	}

	public function setSessionData( $key, $data ) {
		$_SESSION[$key] = $data;
	}

	/**
	 * Strip slashes from global arrays if magic_quotes_gpc is on.
	 * WARNING: Must only be done once! Running a second time may damage the values.
	 */
	private function checkMagicQuotes() {
		$fixQuotes = function_exists( 'get_magic_quotes_gpc' ) && get_magic_quotes_gpc();
		if ( $fixQuotes ) {
			$this->fix_magic_quotes( $_COOKIE );
			$this->fix_magic_quotes( $_ENV );
			$this->fix_magic_quotes( $_GET );
			$this->fix_magic_quotes( $_POST );
			$this->fix_magic_quotes( $_REQUEST );
			$this->fix_magic_quotes( $_SERVER );
		}
	}

	/**
	 * Recursively strip slashes from the given array (for undoing magic_quotes_gpc).
	 * @see php.net/get-magic-quotes-gpc#49612
	 *
	 * @param $arr array
	 * @param $topLevel bool
	 * @return array Original unchanged array
	 */
	private function &fix_magic_quotes( &$arr, $topLevel = true ) {
		$clean = array();
		foreach ( $arr as $key => $val ) {
			if ( is_array( $val ) ) {
				$cleanKey = $topLevel ? stripslashes( $key ) : $key;
				$clean[$cleanKey] = $this->fix_magic_quotes( $arr[$key], false );
			} else {
				$cleanKey = stripslashes( $key );
				$clean[$cleanKey] = stripslashes( $val );
			}
		}
		$arr = $clean;
		return $arr;
	}

	/**
	 * @source http://roshanbh.com.np/2007/12/getting-real-ip-address-in-php.html
	 * @return string IP
	 */
	public function getIP() {
		// Cached?
		if ( $this->ip !== null ) {
			return $this->ip;
		}

		$ip = false;
		if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		if ( !$ip ) {
			throw new SwarmException( 'Could not determine client IP-address.' );
		}

		$this->ip = $ip;
		return $ip;
	}

	/** @return Page|null */
	public function getPageInstance() {
		$pageAction = $this->getVal( 'action', 'home' );
		// getVal will only fallback to "home" if "action" isn't set,
		// if it is falsy, also use home (we don't want to instantiate Page
		// directly if it is an empty string
		if ( !$pageAction ) {
			$pageAction = 'home';
		}
		$pageClass = Page::getPageClassByName( $pageAction );
		return $pageClass ? $pageClass::newFromContext( $this->context ) : null;
	}

	/** Don't allow direct instantiations of this class, use newFromContext instead */
	private function __construct() {}
}

class DerivativeWebRequest extends WebRequest {
	protected $derivPosted = false;

	public static function newFromContext( TestSwarmContext $context ) {
		$req = new self();
		return $req;
	}

	public function setRawQuery( Array $query = array() ) {
		$this->raw = $query;
	}

	public function setWasPosted( $posted ) {
		$this->derivPosted = (bool)$posted;
	}

	/** @return bool */
	public function wasPosted() {
		return $this->derivPosted;
	}

	/** Don't allow direct instantiations of this class, use newFromContext instead */
	private function __construct() {}
}
