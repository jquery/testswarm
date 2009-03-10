<?php
	$browser = "";
	$version = "";
	$ip = $_SERVER['REMOTE_ADDR'];
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	$useragent_id = 0;

	if ( ereg(".+(rv|it|ra|ie)[\/: ]([0-9a-z.]+)", $useragent, $match) ) {
		$version = $match[2];
	}

	$lowerUA = strtolower($useragent);

	if ( strpos($lowerUA, "chrome") > -1 ) {
		$browser = "chrome";
	} else if ( strpos($lowerUA, "webkit") > -1 ) {
		$browser = "webkit";
	} else if ( strpos($lowerUA, "opera") > -1 ) {
		$browser = "opera";
	} else if ( strpos($lowerUA, "gecko") > -1 ) {
		$browser = "gecko";
	} else if ( strpos($lowerUA, "msie") > -1 ) {
		$browser = "msie";
	}
?>
