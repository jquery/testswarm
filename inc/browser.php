<?php
/**
 * Parsed the current users user agent and puts
 * the result of it's detection into the following global variables:
 * - $browser
 * - $version
 * - $os
 * - $useragent
 * - $useragent_id
 * @todo FIXME: These shouldn't all be dangling into the global namespace!
 * Create a class instead (caching stuff in a static array keyed by user agent or something)
 *
 * @since 0.1.0
 * @package TestSwarm
 */

	$browser = "";
	$version = "";
	$os = "";
	$useragent = $_SERVER["HTTP_USER_AGENT"];
	$useragent_id = 0;

	$lowerUA = strtolower($useragent);

	if ( preg_match("/.+(rv|webos|applewebkit|presto|msie|konqueror)[\/: ]([0-9a-z.]+)/", $lowerUA, $match) ) {
		$version = $match[2];
	}

	if ( preg_match("/.*(webos|fennec|series60|blackberry[0-9]*[a-z]*)[\/: ]([0-9a-z.]+)/", $lowerUA, $match) ) {
		$version = $match[2];
	}

	if ( preg_match("/ms-rtc lm 8/", $lowerUA) ) {
		$version = "8.0as7.0";
	}

	if ( strpos($lowerUA, "msie") > -1 && strpos($lowerUA, "windows phone") > -1 ) {
		$browser = "winmo";
	} elseif ( strpos($lowerUA, "msie") > -1 ) {
		$browser = "msie";
	} elseif ( strpos($lowerUA, "konqueror") > -1 ) {
		$browser = "konqueror";
	} elseif ( strpos($lowerUA, "chrome") > -1 ) {
		$browser = "chrome";
	} elseif ( strpos($lowerUA, "webos") > -1 ) {
		$browser = "webos";
	} elseif ( strpos($lowerUA, "android") > -1 && strpos($lowerUA, "mobile safari") > -1 ) {
		$browser = "android";
	} elseif ( strpos($lowerUA, "series60") > -1 ) {
		$browser = "s60";
	} elseif ( strpos($lowerUA, "blackberry") > -1 ) {
		$browser = "blackberry";
	} elseif ( strpos($lowerUA, "opera mobi") > -1 ) {
		$browser = "operamobile";
	} elseif ( strpos($lowerUA, "fennec") > -1 ) {
		$browser = "fennec";
	} elseif ( strpos($lowerUA, "webkit") > -1 && strpos($lowerUA, "mobile") > -1 ) {
		$browser = "mobilewebkit";
	} elseif ( strpos($lowerUA, "webkit") > -1 ) {
		$browser = "webkit";
	} elseif ( strpos($lowerUA, "presto") > -1 ) {
		$browser = "presto";
	} elseif ( strpos($lowerUA, "gecko") > -1 ) {
		$browser = "gecko";
	}

	if ( strpos($lowerUA, "windows nt 6.1") > -1 ) {
		$os = "win7";
	} elseif ( strpos($lowerUA, "windows nt 6.0") > -1 ) {
		$os = "vista";
	} elseif ( strpos($lowerUA, "windows nt 5.2") > -1 ) {
		$os = "2003";
	} elseif ( strpos($lowerUA, "windows nt 5.1") > -1 ) {
		$os = "xp";
	} elseif ( strpos($lowerUA, "windows nt 5.0") > -1 ) {
		$os = "2000";
	} elseif ( strpos($lowerUA, "blackberry") > -1 ) {
		$os = "blackberry";
	} elseif ( strpos($lowerUA, "iphone") > -1 ) {
		$os = "iphone";
	} elseif ( strpos($lowerUA, "ipod") > -1 ) {
		$os = "ipod";
	} elseif ( strpos($lowerUA, "ipad") > -1 ) {
		$os = "ipad";
	} elseif ( strpos($lowerUA, "symbian") > -1 ) {
		$os = "symbian";
	} elseif ( strpos($lowerUA, "webos") > -1 ) {
		$os = "webos";
	} elseif ( strpos($lowerUA, "android") > -1 ) {
		$os = "android";
	} elseif ( strpos($lowerUA, "windows phone") > -1 ) {
		$os = "winmo";
	} elseif ( strpos($lowerUA, "os x 10.4") > -1 || strpos($lowerUA, "os x 10_4") > -1 ) {
		$os = "osx10.4";
	} elseif ( strpos($lowerUA, "os x 10.5") > -1 || strpos($lowerUA, "os x 10_5") > -1 ) {
		$os = "osx10.5";
	} elseif ( strpos($lowerUA, "os x 10.6") > -1 || strpos($lowerUA, "os x 10_6") > -1 ) {
		$os = "osx10.6";
	} elseif ( strpos($lowerUA, "os x") > -1 ) {
		$os = "osx";
	} elseif ( strpos($lowerUA, "linux") > -1 ) {
		$os = "linux";
	}
