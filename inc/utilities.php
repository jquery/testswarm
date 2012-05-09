<?php
/**
 * Various utility classes and global functions.
 *
 * @author John Resig, 2008-2011
 * @author Timo Tijhof, 2012
 * @since 0.1.0
 * @package TestSwarm
 */

	// Protect against web entry
	if ( !defined( 'SWARM_ENTRY' ) ) {
		exit;
	}

	/**
	 * TestSwarm exception
	 * Just a placeholder for now, can be expanded further in the future.
	 *
	 * @since 1.0.0
	 */
	class SwarmException extends Exception {

	}

	/**
	 * Utility function for formatting HTML.
	 * @since 1.0.0
	 *
	 * @param $tagName string: HTML tag name
	 * @param #attribs array: Key/value pairs, unescaped
	 * @param $content string|null: [optional] Text content, to be escaped.
	 */
	function html_tag_open( $tagName, Array $attribs = array() ) {
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
		$html .= ">";
		return $html;
	}
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

		$html = html_tag_open( $tagName, $attribs );

		if ( !in_array( $tagName, $voidElements ) ) {
			$content = strtr( $content, array(
				'&' => '&amp;',
				'<' => '&lt;',
			) );
			$html .= "$content</$tagName>";
		}

		return $html;
	}

	/**
	 * Utility function to overwrite object keys and support multiple levels.
	 * array_merge() overwrites keys, but isn't recursive.
	 * array_merge_recursive() is recursive but doesn't overwrite deeper levels,
	 * and merges by creating arrays, instead of overwriting them.
	 *
	 * @since 1.0.0
	 * @param $obj1 object: Starting point
	 * @param $obj2 object: Values from this object are added
	 * @param $options array: one or more of 'add', 'overwrite'.
	 * Defaults to array( 'add', 'overwrite' ); If neither option is given, the function
	 * will effectively be a no-op.
	 */
	function object_merge( $obj1, $obj2, $options = null ) {
		$options = is_array( $options ) ? $options : array( 'add', 'overwrite' );

		foreach ( $obj2 as $key => $val ) {
			if ( property_exists( $obj1, $key ) && in_array( 'overwrite', $options ) ) {
				if ( is_object( $val ) ) {
					$obj1->$key = object_merge( $obj1->$key, $obj2->$key, $options );
				} else {
					$obj1->$key = $val;
				}
			} elseif ( !property_exists( $obj1, $key ) && in_array( 'add', $options ) ) {
					$obj1->$key = $val;
			}
		}
		return $obj1;
	}

	/**
	 * SQL query utility function
	 * @since 0.1.0
	 * @source php.net/mysql-query#86447
	 */
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
					case "l":
						$rawList = is_array( $args[$args_i] ) ? $args[$args_i] : array( $args[$args_i] );
						$escapedList = array_map( "mysql_real_escape_string", $rawList );
						$sql_query .= "('" . implode( "', '", $escapedList ) . "')";
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

	if ( !function_exists( 'natksort' ) ) {
		/**
		 * PHP has natsort() but no natksort().
		 *
		 * @source http://stackoverflow.com/a/1186347/319266
		 * @seealso php.net/uksort, php.net/natsort, php.net/strnatcmp
		 */
		 function natksort( &$array ) {
			uksort( $array, 'strnatcmp' );
		}
	}
	if ( !function_exists( 'natcaseksort' ) ) {
		/**
		 * PHP has natcasesort() but no natcaseksort().
		 *
		 * @source http://stackoverflow.com/a/1186347/319266
		 * @seealso php.net/uksort, php.net/natcasesort, php.net/strnatcasecmp
		 */
		 function natcaseksort( &$array ) {
			uksort( $array, 'strnatcasecmp' );
		}
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
	 * @since 1.0.0
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
	 * @since 1.0.0
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
	 * @param $rel string: Relative path from the testswarm root, without leading slash
	 * @param $options array|string: A string or an array of string. Options: 'fullurl'.
	 * Any urls outputted through the API should use the 'fullpath' option, or otherwise make
	 * sure that the url is including protocol and hostname.
	 * @return string Relative path from the domain root to the specified file or directory
	 */
	function swarmpath( $rel, $options = array() ) {
		global $swarmContext;

		// Only normalize the contextpath once
		static $path;

		if ( is_null( $path ) ) {
			// Add trailing slash if it's missing
			$path = $swarmContext->getConf()->web->contextpath;
			if ( substr( $path, -1 ) !== "/" ) {
				$path = "$path/";
			}

			// Make sure path starts absolute.
			// Either with protocol https?://domain, relative-protocol //domain
			// or starting at domain root with a slash.
			if ( substr( $path, 0, 6 ) !== "http:/" && substr( $path, 0, 6 ) !== "https:" && $path[0] !== "/" ) {
				$path = "/$path";
			}

			// Update it (just in case it's used elsewhere)
			$swarmContext->getConf()->web->contextpath = $path;
		}

		// Options
		$options = (array)$options;
		if ( in_array( 'fullurl', $options ) ) {
			$prefix = $swarmContext->getConf()->web->server . $path;
		} else {
			$prefix = $path;
		}

		// Just in case, strip the leading slash
		// from the requested path (check length, becuase may be an empty string,
		// avoid PHP E_NOTICE for undefined [0], which could JSON output to be interrupted)
		if ( strlen( $rel ) > 0 && $rel[0] === "/" ) {
			$rel = substr($rel, 1);
		}

		return $prefix . $rel;
	}
