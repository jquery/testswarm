Browser Capabilities PHP Project
================================

_Hacking around with PHP to have a better solution than `get_browser()`_


Introduction
------------

The [browscap.ini](http://browsers.garykeith.com/downloads.asp) file is a
database maintained by [Gary Keith](http://browsers.garykeith.com/) which
provides a lot of details about browsers and their capabilities, such as name,
versions, Javascript support and so on.

PHP's native [get_browser()](http://php.net/get_browser) function parses this
file and provides you with a complete set of information about every browser's
details, But it requires the path to the browscap.ini file to be specified in
the php.ini [browscap](http://ch2.php.net/manual/en/ref.misc.php#ini.browscap)
directive which is flagged as `PHP_INI_SYSTEM`.

Since in most shared hosting environments you have not access to the php.ini
file, the browscap directive cannot be modified and you are stuck with either
and outdated database or without browscap support at all.

Browscap is a standalone class for PHP5 that gets around the limitations of
`get_browser()` and manages the whole thing.
It offers methods to update, cache, adapt and get details about every supplied
user agent on a standalone basis.


Quick start
-----------

A quick start guide is available on the GitHub wiki, at the following address:
https://github.com/GaretJax/phpbrowscap/wiki/QuickStart


Features
--------

Here is a non-exaustive feature list of the Browscap class:

 * Fast
 * Standalone
 * Even faster parsing many user agents
 * Fully get_browser() compatible
 * Often faster and more accurate than get_browser()
 * Fully PHP configuration independent
 * User agent auto-detection
 * Returns object or array
 * Parsed .ini file cached directly into PHP arrays
 * Accepts any .ini file (even ASP and lite versions)
 * Auto updated browscap.ini file and cache from remote server with version checking
 * Configurable remote update server
 * Fully configurable (since 0.2)
 * <del>PHP4 and</del> PHP5 compatible (PHP4 version deprecated)
 * Released under the MIT License


Issues and feature requests
---------------------------

Please report your issues and ask for new features on the GitHub Issue Tracker
at https://github.com/GaretJax/phpbrowscap/issues

Please note that the browscap class only parses and queries the browscap.ini
database provided by Gary Keith. If a browser if wrongly identified or a results
presents erroneous properties, please refer directly to the browscap project
homepage at: http://browsers.garykeith.com/
