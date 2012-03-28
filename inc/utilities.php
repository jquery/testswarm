<?php
/**
 * Various utility classes and global functions.
 *
 * @since 0.1.0
 * @package TestSwarm
 */

	/**
	 * TestSwarm exception
	 * Just a placeholder for now, can be expanded further in the future.
	 *
	 * @since 0.3.0
	 */
	class SwarmException extends Exception {

	}

	/**
	 * Utility function for formatting HTML.
	 * @since 0.3.0
	 *
	 * @param $tagName string: HTML tag name
	 * @param #attribs array: Key/value pairs, unescaped
	 * @param $content string|null: [optional] Text content, to be escaped.
	 */
	function html_tag( $tagName, Array $attribs = array(), $content = "" ) {
		static $voidElements = array(
			'area',
			'base',
			'br',
			'col',
			'command',
			'embed',
			'hr',
			'img',
			'input',
			'keygen',
			'link',
			'meta',
			'param',
			'source',
		);

		$html = "<$tagName";

		foreach ( $attribs as $key => $value ) {
			$html .= ' ' . strtolower( $key ) . '="' . strtr( $value, array(
				'&' => '&amp;',
				'"' => '&quot;',
				"\n" => '&#10;',
				"\r" => '&#13;',
				"\t" => '&#9;',
			) ) . '"';
		}

		if ( in_array( $tagName, $voidElements ) ) {
			$html .= ">";

		} else {
			$content = strtr( $content, array(
				'&' => '&amp;',
				'<' => '&lt;',
			) );
			$html .= ">$content</$tagName>";
		}

		return $html;
	}

	/**
	 * Utility function to overwrite keys and support multiple levels.
	 * (array_merge overwrites keys, but isn't recursive. array_merge_recursive
	 * is recurive but doesn't overwrite deper level's keys..)
	 *
	 * @since 0.1.0
	 */
	function array_extend( $arr1, $arr2 ) {
		foreach ( $arr2 as $key => $val ) {
			if ( array_key_exists( $key, $arr1 ) && is_array( $val ) ) {
				$arr1[$key] = array_extend( $arr1[$key], $arr2[$key] );
			} else {
				$arr1[$key] = $val;
			}
		}
		return $arr1;
	}

	/**
	 * SQL query utility function
	 * @since 0.1.0
	 * @source php.net/mysql-query#86447
	 */
	function mysql_queryf(/* $string, $arg, .. */) {
		global $swarmContext;

		$args = func_get_args();
		$sql_query = call_user_func_array( 'str_queryf', $args );

		$result = $swarmContext->getDB()->query( $sql_query );
		if (!$result) {
			echo "Invalid query: " . mysql_error();
			exit;
		}

		return $result;
	}
	function str_queryf($string) {
		$args = func_get_args();
		array_shift($args);
		$len = strlen($string);
		$sql_query = "";
		$args_i = 0;

		for ( $i = 0; $i < $len; $i++ ) {
			if ( $string[$i] === "%" ) {
				$char = $string[$i + 1];
				$i++;
				switch ( $char ) {
					case "%":
						$sql_query .= $char;
						break;
					case "u":
						$sql_query .= "'" . intval( $args[$args_i] ) . "'";
						break;
					case "s":
						$sql_query .= "'" . mysql_real_escape_string( $args[$args_i] ) . "'";
						break;
					case "x":
						$sql_query .= "'" . dechex( $args[$args_i] ) . "'";
						break;
				}
				if ($char != "x") {
					$args_i++;
				}
			} else {
				$sql_query .= $string[$i];
			}
		}

		return $sql_query;
	}

	/**
	 * Convert a date string into a Unix timestamp.
	 * Interpreteting the date string in GMT context (instead of the time zone currently 
	 * set with date_default_timezone_set in ./inc/init.php)
	 *
	 * Be careful not to use this function when working with non-dates
	 * such as "1 minute ago". Those must be passed to strtotime() directly, otherwise offset
	 * will be incorrectly offset applied. gmstrototime() is only to be used on actual dates
	 * such as "2012-01-01 15:45:01".
	 *
	 * @since 0.3.0
	 * @source php.net/strtotime#107773
	 *
	 * @param $time string
	 * @param $now int
	 * @return int Timestamp
	 */
	function gmstrtotime( $time, $now = null ) {
		static $utc_offset = null;
		if ( $utc_offset === null ) {
			$utc_offset = date_offset_get( new DateTime );
		}
		if ( $now === null ) {
			$loctime = strtotime( $time );
		} else {
			$loctime = strtotime( $time, $now );
		}
		return $loctime + $utc_offset;
	}

	/**
	 * Convert Unix timestamp into a 14-digit timestamp (YYYYMMDDHHIISS).
	 * For usage in the TestSwarm database.
	 * @since 0.3.0
	 *
	 * @param $timestamp int Unix timestamp, if 0 is given, "now" will be assumed.
	 */
	function swarmdb_dateformat( $timestamp = 0 ) {
		$timestamp = $timestamp === 0 ? time() : $timestamp;
		return gmdate( "YmdHis", $timestamp );
	}

	/*
	 * Central function to get paths to files and directories
	 * @since 0.1.0
	 *
	 * @param $rel string Relative path from the testswarm root, without leading slash
	 * @return string Relative path from the domain root to the specified file or directory
	 */
	function swarmpath( $rel ) {
		global $swarmContext;

		// Only normalize the contextpath once
		static $contextpath;

		if ( is_null( $contextpath ) ) {
			// Add trailing slash if it's missing
			$path = $swarmContext->getConf()->web->contextpath;
			if ( substr( $path, -1 ) !== '/' ) {
				$path = "$path/";
			}

			// Make sure path starts absolute.
			// Either with protocol https?://domain, relative-protocol //domain
			// or starting at domain root with a slash.
			if ( substr( $path, 0, 6 ) !== 'http:/' && substr( $path, 0, 6 ) !== 'https:' && $path[0] !== '/' ) {
				$path = "/$path";
			}

			// Update it (just in case it's used elsewhere)
			$swarmContext->getConf()->web->contextpath = $path;
		}

		// Just in case, strip the leading slash
		// from the requested path (check length, becuase may be an empty string,
		// avoid PHP E_NOTICE for undefined [0], which could JSON output to be interrupted)
		if ( strlen( $rel ) > 0 && $rel[0] === '/' ) {
			$rel = substr($rel, 1);
		}

		return $path . $rel;
	}
