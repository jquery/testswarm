<?php
	# Handy function from:
	# http://us2.php.net/manual/en/function.stripslashes.php#79976
	if (get_magic_quotes_gpc()) {
		function stripslashes_deep($value) {
			$value = is_array($value) ?
				array_map("stripslashes_deep", $value) :
				stripslashes($value);

			return $value;
		}

		$_POST = array_map("stripslashes_deep", $_POST);
		$_GET = array_map("stripslashes_deep", $_GET);
		$_COOKIE = array_map("stripslashes_deep", $_COOKIE);
		$_REQUEST = array_map("stripslashes_deep", $_REQUEST);
	}

	# Utility function to overwrite keys and support multiple levels.
	# (array_merge overwrites keys, but isn't recursive. array_merge_recursive
	# is recurive but doesn't overwrite deper level's keys..)
	function array_extend($arr1, $arr2) {
		foreach($arr2 as $key => $val) {
			if(array_key_exists($key, $arr1) && is_array($val)) {
				$arr1[$key] = array_extend($arr1[$key], $arr2[$key]);
			} else {
				$arr1[$key] = $val;
			}
		}
		return $arr1;
	}

	# Handy function from:
	# http://us.php.net/manual/en/function.mysql-query.php#86447
	function mysql_queryf($string) {
		global $swarmDebug;

		$args = func_get_args();
		array_shift($args);
		$len = strlen($string);
		$sql_query = "";
		$args_i = 0;
		for($i = 0; $i < $len; $i++) {
			if($string[$i] == "%") {
				$char = $string[$i + 1];
				$i++;
				switch($char) {
					case "%":
						$sql_query .= $char;
						break;
					case "u":
						$sql_query .= "'" . intval($args[$args_i]) . "'";
						break;
					case "s":
						$sql_query .= "'" . mysql_real_escape_string($args[$args_i]) . "'";
						break;
					case "x":
						$sql_query .= "'" . dechex($args[$args_i]) . "'";
						break;
				}
				if($char != "x") {
					$args_i++;
				}
			} else {
				$sql_query .= $string[$i];
			}
		}
		if ( $swarmDebug ) {
			echo "$sql_query<br>\n";
		}
		$result = mysql_query($sql_query);
		if (!$result) {
		    die("Invalid query: " . mysql_error());
		}
		return $result;
	}

	/**
	 * Get item out of array, falling back on a default if need be.
	 * Complains loudly on failing.
	 */
	function getItem($key, $array, $default=null) {
		if (array_key_exists($key, $array)) {
			return $array[$key];
		} else {
			if (func_num_args() === 3) {
				return $default;
			} else {
				die('<b>getItem Error:</b> Unable to find key <b>' . $key . '</b> in the array ' . print_r($array, true));
			}
		}
	}

	/*
	 * Central function to get paths to files and directories
	 * config "contextpath" should have trailing slash!
	 * @param $rel string Relative path from the root, without prefixed slash
	 * @return string Relative path from the domain root to the specified file or directory
	 */
	function swarmpath( $rel ) {
		global $swarmConfig;
		if ( $rel[0] == "/" ) {
			$rel = substr($rel, 1);
		}
		return $swarmConfig["web"]["contextpath"] . $rel;
	}
