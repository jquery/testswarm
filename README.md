[![Tested with QUnit](https://qunitjs.com/testedwith.svg)](https://qunitjs.com/)

TestSwarm
=================

TestSwarm provides distributed continuous integration testing for JavaScript.

> **⚠️ Project status**
> TestSwarm remains used by jQuery Foundation projects such as jQuery and jQuery UI, but is no longer under active development. Critical issues may be patched, but new issues will not be addressed.
>
> We recommend reviewing these alternatives: [QTap](https://github.com/qunitjs/qtap), [Karma](https://karma-runner.github.io/), [Testem](https://github.com/testem/testem), [grunt-contrib-qunit](https://github.com/gruntjs/grunt-contrib-qunit), [browserstack-runner](https://github.com/browserstack/browserstack-runner/), [Airtap](https://github.com/airtap/airtap), [Intern](https://theintern.io/), [Web Test Runner](https://github.com/brandonaaron/web-test-runner-qunit).

## Documentation

* [About TestSwarm](./docs/About.md) (Philosophy, Architecture, How is it different?)
* [API Guide](./docs/API.md)
* [Automation Guide](./docs/Automation.md)
* [How to: Submit jobs](./scripts/addjob/README.md)
* [Project history](./docs/History.md) (Screenshots)

**Further reading**:

* [JavaScript Testing Does Not Scale](http://ejohn.org/blog/javascript-testing-does-not-scale/), John Resig, 2009.
* [TestSwarm Alpha Open!](https://johnresig.com/blog/test-swarm-alpha-open/), John Resig, 2009.
* [JSConf talk: TestSwarm](http://ejohn.org/blog/jsconf-talk-games-performance-testswarm/), John Resig, 2009.
* [Video: TestSwarm Walkthrough](http://www.vimeo.com/6281121), John Resig, 2009.

## Quick start

Clone the repo

```sh
git clone https://github.com/jquery/testswarm.git
```

## Installation

### Browser compatibility

* Chrome 58+ (2017)
* Edge 15+ (2017, both legacy MSEdge and Chromium-based)
* Firefox 45+ (2016)
* Internet Explorer 9+
* Opera 36+ (2016)
* Safari 9+ (2015)
* Android 4.3+ (2013)
* iOS Mobile Safari 7+ (2013)

### Environmental compatibility

To run TestSwarm you will need a web server, a database server and PHP.
At the moment TestSwarm supports the following, but other configurations
may work as well.

* Apache 2.0+, NGINX 1.10+
* PHP 5.4+ (or PHP-FPM for NGINX)
* MySQL 5.6+
* cURL (for the cleanup action; see step 8)

### Steps

1. Create an empty MySQL database and create a user with read and write access to it.

1. Copy `config/sample-localSettings.php` to `config/localSettings.php`<br/>
   Copy `config/sample-localSettings.json` to `config/localSettings.json`.<br/>
   Edit `localSettings.json` and replace the sample settings with your own.<br/>
   Refer to the [Settings page](./docs/Settings.md) for more information.

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
   `composer install --no-dev`

1. Install the TestSwarm database by running:
   `php scripts/install.php`

1. Create an entry in your crontab for action=cleanup. This performs various
   cleaning duties such as making timed-out runs available again.<br/>
   `* * * * * curl -s https://swarm.example.org/api.php?action=cleanup > /dev/null`

1. [Create a project](./scripts/README.md#create-projects) and [submit jobs](./scripts/addjob/README.md).

## Get involved

You're welcome to use the GitHub [issue tracker](https://github.com/jquery/testswarm/issues)
 to start discussions.

Some of us are also on Gitter at [jquery/dev](https://gitter.im/jquery/dev).

## Copyright and license

See [LICENSE.txt](./LICENSE.txt).

## Versioning

TestSwarm uses the Semantic Versioning guidelines as much as possible.

Releases will be numbered in the following format:

`<major>.<minor>.<patch>`

The `-alpha` suffix is used to indicate unreleased versions in development.

For more information on SemVer, please visit <https://semver.org/>.
