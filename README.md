TestSwarm - Distributed Continuous Integration for JavaScript
=================

TestSwarm provides distributed continuous integration testing for
JavaScript.

The main instance monitoring jQuery core and related projects runs at
[swarm.jquery.org](http://swarm.jquery.org/).



Quick start
----------

Clone the repo, `git clone git://github.com/jquery/testswarm.git`, or
[download the latest
release](https://github.com/jquery/testswarm/zipball/master).



Versioning
----------

TestSwarm uses the Semantic Versioning guidelines as much as possible.

Releases will be numbered in the following format:

`<major>.<minor>.<patch>`

The `-alpha` suffix is used to indicate unreleased versions in development.

For more information on SemVer, please visit http://semver.org/.



Bug tracker
-----------

Found a bug? Please report it using our [issue
tracker](https://github.com/jquery/testswarm/issues)!



Installation
-----------

To run TestSwarm you will need a web server, a database server and PHP.
At the moment TestSwarm only supports Apache and MySQL.

### Requirements

* Apache 2.0+
* PHP 5.3.0+
* MySQL 4.0+
* Curl (for the cleanup action; see below)

### Install

1. Set up a MySQL database and create a user with read and write access.

1. Copy `./config/testswarm-sample.json` to `./config/testswarm.json` and
   update the database settings. For other settings,
   [check the wiki](https://github.com/jquery/testswarm/wiki/Settings).

1. Copy `./config/.htaccess-sample` to `./.htaccess`.<br/>
   Currently the only supported webserver is Apache (which uses a `.htaccess`
   file).<br/>
   To run TestSwarm from a non-root directory, set `web.contextpath` to the
   correct path from the web root and update RewriteBase in `.htaccess`.
   Verify that `.htaccess` is working properly by opening a page (e.g.
   `/testswarm/projects`) in your browser. If it doesn't work, make sure your
   `.htaccess` is actually being read (e.g. by putting some jibberish into the
   `.htaccess` file, which should result in a HTTP 500 Error). If it doesn't
   get loaded, verify that `AllowOverride` is set to "`All`" (at least not to
   "`None`") in your Apache configuration.

1. Set `storage.cacheDir` to a writable directory that is not readable from the
   web. Either set it to a custom path outside the web document root, or use the
   default `cache` directory (protected with .htaccess).<br/>Chmod it:
   `chmod 777 ./cache`.

1. Install the TestSwarm database by running:
   `php ./scripts/dbInstall.php`

1. Copy `./config/robots.txt` to `./robots.txt` (or add similar rules to your
   main `robots.txt` file if TestSwarm is not in the root directory).

1. Create an entry in your crontab for action=cleanup. This performs various
   cleaning duties such as making timed-out runs available again.<br/>
   `* * * * * curl -s http://swarm.example.org/api.php?action=cleanup > /dev/null`



Get involved
---------------------

You're welcome to use the GitHub [issue tracker](https://github.com/jquery/testswarm/issues)
 to start discussions.

Or post to the [QUnit and Testing forum](https://forum.jquery.com/qunit-and-testing).

Most of us are also on IRC in the
[#jquery-dev](http://webchat.freenode.net/?channels=jquery-dev) channel at
irc.freenode.net

Planning for TestSwarm and other projects related to testing of javascript
applications based around jQuery happens on the [jQuery Testing Team
wiki](http://jquerytesting.pbworks.com)


Documentation
---------------------

* [TestSwarm wiki](https://github.com/jquery/testswarm/wiki)
* [Submit jobs README](https://github.com/jquery/testswarm/blob/master/scripts/addjob/README.md)
* _[more wiki pages](https://github.com/jquery/testswarm/wiki/_pages)_



Copyright and license
---------------------

See
[MIT-LICENSE](https://raw.github.com/jquery/testswarm/master/MIT-LICENSE).



History
---------------------

TestSwarm was originally created by [John Resig](http://ejohn.org/) as a
basic tool to support unit testing of the [jQuery JavaScript
library](http://jquery.com). It was later moved to become an official
[Mozilla Labs](http://labs.mozilla.com/) and has since moved again to become
a [jQuery](http://jquery.org/) project.
