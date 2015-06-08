TestSwarm - Distributed Continuous Integration for JavaScript
=================

TestSwarm provides distributed continuous integration testing for
JavaScript.

The main instance monitoring jQuery core and related projects runs at
[swarm.jquery.org](http://swarm.jquery.org/).

Project Status
--------------

TestSwarm is still in use in projects of the jQuery Foundation, but it isn't under active development anymore. Although critical issues may be patched in the future, most open issues will remain unaddressed.

Within the jQuery Foundation, we're experimenting with alternative projects, to eventually shut down our own instance of TestSwarm:

- [Karma](http://karma-runner.github.io/)
- [browserstack-runner](https://github.com/browserstack/browserstack-runner/)
- [Intern](http://theintern.io/)

We recommend reviewing those and other alternatives.

Quick start
----------

Clone the repo, `git clone --recursive git://github.com/jquery/testswarm.git`.


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

### Environmental compatibility

To run TestSwarm you will need a web server, a database server and PHP.
At the moment TestSwarm supports the following, but other configurations
may work as well.

* Apache 2.0+, NGINX 1.2+
* PHP 5.4+ (or PHP-FPM for NGINX)
* MySQL 4.0+
* cURL (for the cleanup action; see step 8)

### Steps

1. Create an empty MySQL database and create a user with read and write access to it.

1. Copy `config/sample-localSettings.php` to `config/localSettings.php`<br/>
   Copy `config/sample-localSettings.json` to `config/localSettings.json`.<br/>
   Edit `localSettings.json` and replace the sample settings with your own.<br/>
   Refer to the [Settings page](https://github.com/jquery/testswarm/wiki/Settings) for more information.

1. *For Apache:*<br/>
   Copy `config/sample-.htaccess` to `.htaccess`.<br/>
   To run TestSwarm from a non-root directory, set `web.contextpath` in `localSettings.json` to the
   correct path from the web root and update RewriteBase in `.htaccess`.
   Verify that `.htaccess` is working properly by opening a page other than the HomePage (e.g.
   `/testswarm/projects`) in your browser.<br/>Required Apache configuration:<br/>
   * `AllowOverride` is set to `All` (or ensure `FileInfo` is included).
   * `mod_rewrite` installed and loaded.

   *For NGINX:*<br/>
   Copy `config/sample-nginx.conf` to `/etc/nginx/sites-available`.
   <br/>The file name should match your domain e.g. for swarm.example.org:<br/>
   `cp config/sample-nginx.conf /etc/nginx/sites-available/swarm.example.org.conf`
   <br/>Open this conf file in your editor and replace the "example" values with the correct values.
   <br/>Make sure your install is located at `/var/www/testswarm`
   (otherwise update the file to match the correct location).<br/>
   Now you need to link the `sites-available` config to the `sites-enabled` config:<br/>
   (replace the "swarm.example.org" with your own file name):<br/>
   `ln -s /etc/nginx/sites-available/swarm.example.org.conf /etc/nginx/sites-enabled/swarm.example.org.conf`<br/>
   Now make sure that php-fpm is running: `/etc/init.d/php-fpm status`<br/>
   if is not running start it: `/etc/init.d/php-fpm start`

1. Copy `config/sample-robots.txt` to `robots.txt`<br/>
   Or, if TestSwarm is not in the root directory, add similar rules to your root `robots.txt`.

1. Set `storage.cacheDir` to a writable directory that is not readable from the
   web. Either set it to a custom path outside the document root, or use the
   default `cache` directory (protected with .htaccess).<br/>Chmod it:
   `chmod 777 cache`.

1. Install dependencies
   `composer install`

1. Install the TestSwarm database by running:
   `php scripts/install.php`

1. Create an entry in your crontab for action=cleanup. This performs various
   cleaning duties such as making timed-out runs available again.<br/>
   `* * * * * curl -s http://swarm.example.org/api.php?action=cleanup > /dev/null`

1. [Create a project](./scripts/README.md#create-projects) and [submit jobs](./scripts/addjob/README.md).


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
* [more wiki pages](https://github.com/jquery/testswarm/wiki/_pages)


Copyright and license
---------------------

See [LICENSE.txt](https://raw.github.com/jquery/testswarm/master/LICENSE.txt).


History
---------------------

TestSwarm was originally created by [John Resig](http://ejohn.org/) as a
basic tool to support unit testing of the [jQuery JavaScript
library](http://jquery.com). It was later moved to become an official
[Mozilla Labs](http://labs.mozilla.com/) and has since moved again to become
a [jQuery](http://jquery.org/) project.
