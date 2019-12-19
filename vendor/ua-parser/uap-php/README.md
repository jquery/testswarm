# ua-parser PHP Library #

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/ua-parser/uap-php?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge) [![Build Status](https://travis-ci.org/ua-parser/uap-php.svg?branch=master)](https://travis-ci.org/ua-parser/uap-php) [![Average time to resolve an issue](http://isitmaintained.com/badge/resolution/ua-parser/uap-php.svg)](http://isitmaintained.com/project/ua-parser/uap-php "Average time to resolve an issue") [![Percentage of issues still open](http://isitmaintained.com/badge/open/ua-parser/uap-php.svg)](http://isitmaintained.com/project/ua-parser/uap-php "Percentage of issues still open")
[![Latest Stable Version](https://poser.pugx.org/ua-parser/uap-php/v/stable)](https://packagist.org/packages/ua-parser/uap-php)
[![Total Downloads](https://poser.pugx.org/ua-parser/uap-php/downloads)](https://packagist.org/packages/ua-parser/uap-php)
[![License](https://poser.pugx.org/ua-parser/uap-php/license)](https://packagist.org/packages/ua-parser/uap-php)

This is the PHP library for the [uap-core](https://github.com/ua-parser/uap-core) project.

## Installation ##
Add `ua-parser/uap-php` to the require section of your `composer.json` file and run `composer update`.  

## Demo ##

You can [test the PHP library](http://uaparser.dmolsen.com/) with your browser.

## Usage ##

Straightforward:

```php
require_once 'vendor/autoload.php';
use UAParser\Parser;

$ua = "Mozilla/5.0 (Macintosh; Intel Ma...";

$parser = Parser::create();
$result = $parser->parse($ua);

print $result->ua->family;            // Safari
print $result->ua->major;             // 6
print $result->ua->minor;             // 0
print $result->ua->patch;             // 2
print $result->ua->toString();        // Safari 6.0.2
print $result->ua->toVersion();       // 6.0.2

print $result->os->family;            // Mac OS X
print $result->os->major;             // 10
print $result->os->minor;             // 7
print $result->os->patch;             // 5
print $result->os->patchMinor;        // [null]
print $result->os->toString();        // Mac OS X 10.7.5
print $result->os->toVersion();       // 10.7.5

print $result->device->family;        // Other

print $result->toString();            // Safari 6.0.2/Mac OS X 10.7.5
print $result->originalUserAgent;     // Mozilla/5.0 (Macintosh; Intel Ma...
```

## Using Your Own Custom regexes.php File ##

You can use your own `regexes.php` file if you've customized the official file. I *strongly* encourage you to push back any changes you may have so others can benefit. That said, to use your own do the following:

```php
require_once 'vendor/autoload.php';
use UAParser\Parser;

$parser = Parser::create("path/to/custom/regexes.php");
```

## Using ua-parser PHP Library from the Command Line ##

A command line utility is now included with the PHP library. The following commands are supported:

### Get Usage Info

Provides simple usage information:

    php bin/uaparser

### Update the regexes.php File

Fetches an updated YAML file for `ua-parser` and overwrites the current JSON file. You can use the following as part of a cron job that runs nightly.

    php bin/uaparser ua-parser:update [--no-backup]

By default creates a backup file. Use `--no-backup` to turn that feature off.

### Convert an Existing regexes.yaml File to regexes.php

With the change to v2.0 you may have an existing and customized YAML file for `ua-parser`. Use the following to convert it to JSON.

    php bin/uaparser ua-parser:convert [file]

### Grab Just the Latest regexes.yaml File From the Repository

If you need to add a new UA it's easier to edit the original YAML and then convert it to JSON.

    php bin/uaparser ua-parser:fetch [file]

Fetches an updated YAML file. *Warning:* This method overwrites any existing `regexes.yaml` file.

### Parse a Single User Agent String

Parses a user agent string and dumps the results as a list.

    php bin/uaparser ua-parser:parse "your user agent string"

### Parse a Webserver Log File

Parses the supplied log file or log directory to test ua-parser. Saves the UA to a file when the UA or OS family aren't recognized or when the UA is listed as a generic smartphone or as a generic feature phone.

    php bin/uaparser ua-parser:logfile [-f /path/to/logfile] [-d /path/to/logdir] [--include "*.gz"] [--exclude "*.gz"] errors.log

Multiple `--include` and `--exclude` parameters are allowed.

## Credits ##

Thanks to the [original ua-parser team](http://code.google.com/p/ua-parser/people/list) for making the YAML file available for others to build upon.

Also, many thanks to the following major contributors to the PHP library:

* Bryan Shelton
* Michael Bond
* @rjd22
* Timo Tijhof
* Marcus Bointon
* Ryan Parman
* Pravin Dahal

## Licensing ##
* The library is licensed under the MIT license
* The user agents data from the ua-parser project is licensed under the Apache license
* The initial list of generic feature phones & smartphones came from Mobile Web OSP under the MIT license
* The initial list of spiders was taken from Yiibu's profile project under the MIT license.

