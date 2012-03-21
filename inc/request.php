<?php
/**
 * Class for interacting with request and session data.
 *
 * Based on
 * - https://svn.toolserver.org/svnroot/krinkle/trunk/common/Request.php
 * - https://svn.wikimedia.org/viewvc/mediawiki/trunk/phase3/includes/WebRequest.php?view=markup&pathrev=114154
 *
 * @package TestSwarm
 */

class WebRequest {
	protected $raw;

	function __construct() {
		$this->checkMagicQuotes();

		// POST overrides GET data
		// We don't use $_REQUEST here to avoid interference from cookies...
		$this->raw = $_POST + $_GET;
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
	public function getBool( $key, $default = false ) {
		return (bool)$this->getVal( $key, $default );
	}

	/** @return int */
	public function getInt( $key, $default = 0 ) {
		return intval( $this->getVal( $key, $default ) );
	}

	/**
	 * Is the key is set, whatever the value. Useful when dealing with HTML checkboxes.
	 * @return bool
	 */
	public function hasKey( $key ) {
		return !array_key_exists( $key, $this->raw ) ? false : true;
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
		foreach( $arr as $key => $val ) {
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
}
