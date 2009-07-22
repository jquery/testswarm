<?php
	$browser = "";
	$version = "";
	$os = "";
	$ip = $_SERVER['REMOTE_ADDR'];
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	$useragent_id = 0;

	$lowerUA = strtolower($useragent);

	if ( ereg(".+(rv|applewebkit|presto|msie|konqueror)[\/: ]([0-9a-z.]+)", $lowerUA, $match) ) {
		$version = $match[2];
	}

	if ( strpos($lowerUA, "chrome") > -1 ) {
		$browser = "chrome";
	} else if ( strpos($lowerUA, "konqueror") > -1 ) {
		$browser = "konqueror";
	} else if ( strpos($lowerUA, "webkit") > -1 ) {
		$browser = "webkit";
	} else if ( strpos($lowerUA, "presto") > -1 ) {
		$browser = "presto";
	} else if ( strpos($lowerUA, "gecko") > -1 ) {
		$browser = "gecko";
	} else if ( strpos($lowerUA, "msie") > -1 ) {
		$browser = "msie";
	}

	if ( strpos($lowerUA, "windows nt 6.0") > -1 ) {
		$os = "vista";
	} else if ( strpos($lowerUA, "windows nt 5.1") > -1 ) {
		$os = "xp";
	} else if ( strpos($lowerUA, "windows nt 5.0") > -1 ) {
		$os = "2000";
	} else if ( strpos($lowerUA, "os x 10.4") > -1 || strpos($lowerUA, "os x 10_4") > -1 ) {
		$os = "osx10.4";
	} else if ( strpos($lowerUA, "os x 10.5") > -1 || strpos($lowerUA, "os x 10_5") > -1 ) {
		$os = "osx10.5";
	} else if ( strpos($lowerUA, "os x") > -1 ) {
		$os = "osx";
	} else if ( strpos($lowerUA, "linux") > -1 ) {
		$os = "linux";
	}
?>
