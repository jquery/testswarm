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
At the moment TestSwarm supports Apache/MySQL/PHP and Nginx/MySQL/PHP-FPM

### Install for Apache/MySQL/PHP

### Requirements

* Apache 2.0+
* PHP 5.3.0+
* MySQL 4.0+
* Curl (for the cleanup action; see below)


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

### Install for Nginx/MySQL/PHP-FPM

###Requirements:

* NGINX
* PHP-FPM 5.3.0+
* MySQL 4.0+
* Curl (for a cron job that cleans out the system)

If your on Ubuntu natty and up you can just grab php5-fpm off apt

If your running Debian you need to add the dotdeb.org repository to your /etc/apt/sources.list [Directions on the site](http://www.dotdeb.org/instructions/).<br/>
If you have an existing php/mysql install remove them before adding this as it will cause problems. Install all the things from this repo.

If your running an Centos or another rpm based distro go here [howtoforge article](http://www.howtoforge.com/installing-nginx-with-php5-and-php-fpm-and-mysql-support-on-centos-6.2)

For the purposes of these directions I am assuming you extracted/cloned testswarm to /var/www/testswarm

###Debian/Ubuntu install:

`apt-get install php5-mysql php5-fpm php5-cli nginx mysql-server`

You will be prompted for the MySQL root password (use a good one, don't be lazy) remember this as we'll need it in a bit.

####Set up Nginx

cd into your /var/www/testswarm/config directory

copy the testswarm-sample-nginx.conf to your /etc/nginx/sites-available/ directory and change the name to your url.com.conf<br/>
For example:<br/>
`cp testswarm-sample-nginx.conf /etc/nginx/sites-available/testswarm.iamawesome.com.conf`

cd into the /etc/nginx/sites-available/ directory

open the .conf file with vi or whatever you use as a command line editor.

you need to change a couple things, there are directions in the file itself, follow those directions, save the file and exit.

You may have noticed that in the .conf file I specified the document root directories as /var/www/testswarm.<br/> 
The files are in /var/www/testswarm not /var/www/testswarm/testswarm make sure this is the case or you will need to change the document root.<br/>

Now you need to link the sites-available config to the sites-enabled config

Example (replace the .conf file name with your own):

`ln -s /etc/nginx/sites-available/testswarm.iamawesome.com.conf /etc/nginx/sites-enabled/testswarm.iamawesome.com.conf`

####Set up MySQL

You will now need to set up your mysql database, I named my database testswarm but you can call yours whatever you want, create a user and give it permissions. In this example I create the database testswarm, I set the username to cheese and set the password to lwn89a87sa9%a8b987s@! and then give cheese permissions to testswarm. (please note the use of a strong password for your database user) 

````
mysql -u root -p

mysql> create database testswarm;
Query OK, 1 row affected (0.00 sec)

mysql> grant usage on testswarm.* to cheese@localhost identified by 'lwn89a87sa9%a8b987s@!';
Query OK, 0 rows affected (0.00 sec)

mysql> grant all privileges on testswarm.* to cheese@localhost;
Query OK, 0 rows affected (0.00 sec)

mysql> flush privileges;
Query OK, 0 rows affected (0.00 sec)

mysql> quit
````

####Configure Testswarm

Now we set up your testswarm.json file, you need to create a copy of the testswarm-sample.json file and name it testswarm.json:

`cp testswarm-sample.json testswarm.json`

Now open testswarm.json up in your editor, under the database settings change the user and password to the user and password you created for your database.

You will see on line 9 the contextpath setting is "/testswarm", change it to "/" or testswarm won't work properly with Nginx.

Ok now load up the database with the right data.<br/>
cd to the the root directory `cd /var/www/testswarm` and run

`php scripts/dbInstall.php`

Now your database contains the proper data and we can finish up.

####Finish up

Testswarm comes preset with a cache directory
It is set in testswarm.json

````
"storage": {
	"cacheDir": "$1/cache"
}
````

If you don't want this to be your cache directory for any reason, this is where you change it. Make sure it is a directory that is not in your document root /var/www/testswarm, or if it is, add it to the deny list in the Nginx config file. Chances are you won't ever need to do this, but for those special cases, this is where it is done.
Before your cache directory can be used you need to set it to readable/writable by everyone using chmod:

`chmod 777 /var/www/testswarm/cache`


Setup the robots.txt file, its already made, you just need to copy it to /var/www/testswarm

Copy `/var/www/testswarm/config/robots.txt` to `/var/www/testswarm/robots.txt`


Now make sure that php-fpm is running

`/etc/init.d/php-fpm status`

if is not running start it `/etc/init.d/php-fpm start`


Now open your browser and go to your testswarm url.


###Other Notes

Create an entry in your crontab for action=cleanup. This performs various cleaning duties such as making timed-out runs available again. The below cron job runs every minute, just so you're aware. You might want to do a `*/10 * * * *` instead, which is every 10 mins.<br/>
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
