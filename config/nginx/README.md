Setting up TestSwarm to work with php-fpm and nginx
===================================================

First you must have a working php-fpm + nginx installation running.

Lets go over that a bit first.

Installation
------------

For the purposes of this explanation I am assuming:
 
* You are running Linux
* It is a clean install
* You have cloned/extracted testswarm into /var/www/testswarm
* You know what you are doing in most cases

###Requirements:

* NGINX
* PHP-FPM 5.3.0+
* MySQL 4.0+
* Curl (for a cron job that cleans out the system)

If your on Ubuntu natty and up you can just grab php5-fpm off apt

If your running Debian you need to add the dotdeb.org repository to your /etc/apt/sources.list [Directions on the site](http://www.dotdeb.org/instructions/). If you have an existing php/mysql install remove them before adding this as it will cause problems. Install all the things from this repo.

If your running an Centos or another rpm based distro go here [howtoforge article](http://www.howtoforge.com/installing-nginx-with-php5-and-php-fpm-and-mysql-support-on-centos-6.2)

###Debian/Ubuntu install:

`apt-get install php5-mysql php5-fpm php5-cli nginx mysql-server`

set your mysql password (use a good one, don't be lazy).

copy the testswarm-sample-nginx.conf to your /etc/nginx/sites-available/ directory and change the name to your url.com.conf

`cp testswarm-sample-nginx.conf /etc/nginx/sites-available/testswarm.iamawesome.com.conf`

open the nginx .conf file up in vi or whatever you use

you need to change a couple things, there are directions in the file itself, follow those, come back here.

You may have noticed I specified that the directory is /var/www/testswarm. The files are in /var/www/testswarm not /var/www/testswarm/testswarm make sure this is the case.

you will now need to set up your mysql database, I named my database testswarm but you can call yours whatever you want, create a user and give it permissions.

`mysql -u root -p`

`mysql> grant usage on testswarm.* to cheese@localhost identified by 'lwn89a#87sa9%a8b987s@!';
Query OK, 0 rows affected (0.00 sec)

mysql> grant all privileges on testswarm.* to cheese@localhost;
Query OK, 0 rows affected (0.00 sec)

mysql> flush privileges;
Query OK, 0 rows affected (0.00 sec)

mysql> quit`

Note: please don\'t use this password :) If you do, don\'t get mad if you have to ride the p0wny all the way to 0wn3d land.

You now need to go down one directory to /config and set up your testswarm.json file

`cp testswarm-defaults.json testswarm.json`

Now open that up in your editor, change the user and password in the database settings at the top.

You will see on line 16 the contextpath setting is "/", keep it that way, dont change it or the nginx config won't work.

Ok now set up the database, to the the root directory /var/www/testswarm and run

`php scripts/dbInstall.php`

this will add all the goodies to MySQL and you\'re that much closer to being done.

Now make sure that php-fpm is running

`/etc/init.d/php-fpm status`

if not start it, and now go to your testswarm site, it should be rawkin socks.

###Other Notes

In testswarm.json set<br/> 
`"storage": {
		"cacheDir": "$1/cache"
	}` 
to a writable directory that is not readable from the
web. Either set it to a custom path outside the web document root, or use the
default `cache` directory.
<br/>Chmod it:
`chmod 777 ./cache`.
<br/>
Side note:<br/>
I kept this the default /var/www/testswarm/cache, nginx hides it for you

Copy `./config/robots.txt` to `./robots.txt`

Create an entry in your crontab for action=cleanup. This performs various
cleaning duties such as making timed-out runs available again. This runs every minute just be aware. You might want to do a `*/10 * * * *` instead, which is every 10 mins.<br/>
`* * * * * curl -s http://swarm.example.org/api.php?action=cleanup > /dev/null`

