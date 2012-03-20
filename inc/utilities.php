<?php
	/**
	 * TestSwarm exception
	 * Just a placeholder for now, can be expanded further in the future.
	 */
	class SwarmException extends Exception {

	}

	/**
	 * Utility function to overwrite keys and support multiple levels.
	 * (array_merge overwrites keys, but isn't recursive. array_merge_recursive
	 * is recurive but doesn't overwrite deper level's keys..)
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
	 * MySQL query utility function
	 * @source php.net/mysql-query#86447
	 */
	function mysql_queryf($string) {
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

		$result = mysql_query( $sql_query );
		if (!$result) {
			echo "Invalid query: " . mysql_error();
			exit;
		}

		return $result;
	}

	/**
	 * Get item out of array, falling back on a default if need be.
	 * Complains loudly on failing.
	 */
	function getItem( $key, Array $array, $default = null ) {
		if ( array_key_exists( $key, $array ) ) {
			return $array[$key];
		} else {
			if ( func_num_args() === 3 ) {
				return $default;
			} else {
				throw new SwarmException(
					"Unable to find key `" . $key . "` in the array:\n" . print_r( $array, true )
				);
			}
		}
	}

	/*
	 * Central function to get paths to files and directories
	 * @param $rel string Relative path from the testswarm root, without leading slash
	 * @return string Relative path from the domain root to the specified file or directory
	 */
	function swarmpath( $rel ) {
		global $swarmConfig;

		// Only normalize the contextpath once
		static $contextpath;

		if ( is_null( $contextpath ) ) {
			// Strip trailing slash if there is one
			$path = $swarmConfig["web"]["contextpath"];
			if ( substr( $path, -1 ) === '/' ) {
				$path = substr( $path, 0, -1 );
			}

			// Make sure path starts absolute.
			// Either with protocol https?://domain, relative-protocol //domain
			// or starting at domain root with a slash.
			if ( substr( $path, 0, 6 ) !== 'http:/' && substr( $path, 0, 6 ) !== 'https:' && $path[0] !== '/' ) {
				$path = "/$path";
			}
		}

		// Just in case, strip the leading slash
		// from the requested path
		if ( $rel[0] === '/' ) {
			$rel = substr($rel, 1);
		}

		return "$path/$rel";
	}
