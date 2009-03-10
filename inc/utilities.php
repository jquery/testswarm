<?php
	# Handy function from:
	# http://us2.php.net/manual/en/function.stripslashes.php#79976
	if (get_magic_quotes_gpc()) {
		function stripslashes_deep($value) {
			$value = is_array($value) ?
				array_map('stripslashes_deep', $value) :
				stripslashes($value);

			return $value;
		}

		$_POST = array_map('stripslashes_deep', $_POST);
		$_GET = array_map('stripslashes_deep', $_GET);
		$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
		$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
	}

	# Handy function from:
	# http://us.php.net/manual/en/function.mysql-query.php#86447
	function mysql_queryf($string) {
		global $DEBUG_ON;

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
		if ( $DEBUG_ON ) {
			echo "$sql_query<br>\n";
		}
		return mysql_query($sql_query);
	}
?>
