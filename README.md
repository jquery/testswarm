TestSwarm - Distributed Continuous Integration for JavaScript
=================

TestSwarm provides distributed continuous integration testing for JavaScript.

The main instance monitoring jQuery core and related projects runs at
[swarm.jquery.org](http://swarm.jquery.org/).



Quick start
----------

Clone the repo, `git clone git@github.com:jquery/testswarm.git`, or [download
the latest release](https://github.com/jquery/testswarm/zipball/master).



Versioning
----------

TestSwarm uses the Semantic Versioning guidelines as much as possible.

Releases will be numbered with the follow format:

`<major>.<minor>.<patch>`

The `-pre` suffix is used to indicate unreleased versions currently in
development.

For more information on SemVer, please visit http://semver.org/.



Bug tracker
-----------

Have a bug? Please report the issue in our issue tracker at GitHub!

https://github.com/jquery/testswarm/issues



Installation
-----------

To use TestSwarm you need a web server, database server and PHP. Right now
TestSwarm only supports Apache and MySQL as servers.

### Requirements

* Apache 2.0+
* PHP 5.2.3+
* MySQL 4.0+

### Install

1. Create a mysql database and a user who can connect and write to it.

2. Load the MySQL database.
   `mysql DBNAME -u USER -p < testswarm.sql`
   `mysql DBNAME -u USER -p < useragents.sql`

3. Copy the ./config/config-sample.ini to ./testswarm.ini and change the
   options to correspond to your MySQL database information.

4. Copy the ./config/.htaccess-sample to ./htaccess. If needed change the
   RewriteBase to match the contextpath configuration.

5. Create an entry to your crontab for action=cleanup. This performs various
   cleaning duties such as making timed-out runs available again for testing.
   `* * * * * curl -s http://path/to/testswarm/api.php?action=cleanup > /dev/null`

6. Currently the server must be run in Apache (it uses a .htaccess file).

   To run it from non-root, set `contextpath = "/testswarm/"` (or whatever
   path you use).

   And update the `.htaccess` file, use `RewriteBase /testswarm/`

   Test if `/testswarm/login` loads, if not, put some jibberish into the
   .htaccess file to see if it's getting loaded. If not, make sure
   `AllowOverride` is set to "`All`" (at least not to "`None`") in your main
   Apache config.



Get involved
---------------------

You're welcome to use the GitHub issue tracker to start discussions.

There is also a mailing list at Google Groups available:

* https://groups.google.com/group/testswarm
* testswarm@googlegroups.com

Most of us are also on IRC in the #jquery-dev channel at irc.freenode.net

Planning for TestSwarm and other projects related to testing of javascript
applications based around jQuery happens on the [jQuery Testing Team
wiki](http://jquerytesting.pbworks.com)



Copyright and license
---------------------

See [LICENSE](https://raw.github.com/jquery/testswarm/master/LICENSE) for more
information.



History
---------------------

TestSwarm was originally created by [John Resig](http://ejohn.org/) as a basic
tool to support unit testing of the [jQuery JavaScript
library](http://jquery.com). It was later moved to become an official [Mozilla
Labs](http://labs.mozilla.com/) and has since moved again to become a
[jQuery](http://jquery.org/) project.
