TestSwarm v0.3.0pre - Distributed continuous integration for JavaScript.

http://swarm.jquery.org/

## DISCUSSION

The Google Group for general usage and development discussion:
http://groups.google.com/group/testswarm

## INSTALLATION

1. Create a mysql database and a user who can connect and write to it.

2. Load the MySQL database.
   `mysql DBNAME -u USER -p < testswarm.sql`
   `mysql DBNAME -u USER -p < useragents.sql`

3. Copy the ./config/config-sample.ini to ./config.ini and change the options
   to correspond to your MySQL database information.

4. Copy the ./config/.htaccess-sample to ./htaccess. If needed change the
   RewriteBase to match the contextpath configuration.

5. Load the cronjob (changing the URL to point to your site).
   `crontab << config/cronjob.txt`

6. Currently the server must be run in Apache (it uses a .htaccess file).

   To run it from non-root, set contextpath = "/testswarm" (or whatever path
   you use).

   And update the `.htaccess` file, use `RewriteBase /testswarm/`

   Test if `/testswarm/login` loads, if not, put some jibberish into the .htaccess
   file to see if it's getting loaded. If not, make sure `AllowOverride` is set to
   "`All`" (at least not to "`None`") in your main Apache config.

## LICENSE

See LICENSE for more information.

Planning for TestSwarm and other testing tools related work now happens on the jQuery Testing Team planning wiki: http://jquerytesting.pbworks.com/w/page/41556026/FrontPage
