## 1.0.0-alpha

***NOT A RELEASE YET***

This major release features a complete rewrite of the frontend and backend code base, and
adds several major features such as a brand new API, automatically synchronized runclient
settings and a new interface powered by Twitter Bootstrap.

TestSwarm has also incorporated several optimizations for a continous integration workflow
where TestSwarm is used with Jenkins and BrowserStack (or similar services) by providing
detailed information about the swarm's state through `api.php?action=swarmstate`. This new API
action lists all supported browsers including how many clients are online for each user agent
and if and how many pending runs there are. This makes it easy to then use this information to
automatically start or terminate browsers as needed with, for example, the [BrowserStack
API](https://github.com/browserstack/api),
[node-browserstack](https://github.com/scottgonzalez/node-browserstack) and [testswarm-browserstack](https://github.com/clarkbox/testswarm-browserstack).

The database has remained mostly the same, although there are too many breaking changes for
a clean upgrade. You are recommended to start clean as there is no upgrade path. If you want, it
is possible to take note of the authentication and password hash of your main account (in the
`users.auth` column) and re-insert it directly into the database after installing TestSwarm 1.0.
Other than that, no data is importable from a pre-1.0 installation.

Complete list of issues solved in the 1.0.0 milestone:

* <https://github.com/jquery/testswarm/issues?sort=updated&direction=asc&state=closed&milestone=1>

### Configuration changes
* PHP version requirement raised to 5.3.0+.
* Settings are now loaded from `/config/localSettings.json` (instead of `/testswarm.ini`).
* The default `cooldownSleep` setting has changed from 15 seconds to 1 second.
* The default `nonewrunsSleep` setting has changed from 30 seconds to 15 seconds.
* The database schema has been re-constructed from the ground up. The schema is
  non-upgradable and will have to be re-created. Use the dbUpdate.php or dbInstall.php script
  to quickly re-create your database. All data will be lost when upgrading from < 1.0.0!

### New features

* (#82) Refactor front-end skin, powered by Twitter Bootstrap (#145).
* (#107) Implement browser client settings framework.
* (#127) Refactor backend with no globals into OOP with Context.
* (#132) Refactor state/content/logic scripts into Page/Action classes.
* (#115) Implement API, including various new actions (#105, #116).
* (#139) Create action for getting current status of the swarm.
* (#143) Refactor useragent storage and detection.
* (#150) Expose current TestSwarm version in InfoAction API and in `<meta>` on Pages.
* (#146) Add noindex rules.
* (#149) Create action "projects" for listing all accounts that submit jobs.
* Implement basic caching for expensive operations in flat files in `/cache/`.
* Implement "debug" mode for the API (`format=debug`).
* Implement "debug" mode for Database queries.
  Disabled by default for performance and security reasons,
  enable by setting `debug.dbLogQueries` to `true` in the settings file.
* (#158) New shell script to install the database.
* (#142) New shell script to update from an old version of the database.
* New shell script to query the browserSet configuration.
* (#172) Use JSON for configuration files.
* Create "Info" page. Showing current version info with links to GitHub.
* New "Result" page. Linked to from Job pages, showing the run results inluding
  navigation links for the run and meta data about the client and the run.
  Replaces the old Runresults page.
* Implemented Ping system. Used to keep track of which clients are online, and
  which may have lost network connection, crashed or else. Also used to keep the
  client-side configuration up to date to allow long-live runner clients that
  don't run with old configurations.
* (#207) Security: Protect against clickjacking attacks. Pages now send proper
  X-Frame-Options headers.
* (#209) Security: Fix CRSR vulnerabilities. The API and GUI no longer perform
  actions on behalf of a user based on cookies/sessions. All actions now require
  tokens. This is handled transparently between GUI and API. Third party users
  of the API may have to update their code to send the authToken for requests
  that previously didn't require it.
* (#232) Security: Allow settings file to be stored outside docroot.
  Though settings are still loaded from `config/localSettings.json` by default,
  there is now a small PHP-file serving as intermediary which can be modified
  to point to somewhere else instead. As being a PHP file, its source won't be
  shown, even if the webserver's Deny rules for /config are misconfigured.
* (#199) Add a confirmation in interface for Reset job/Delete job.
* (#222) Add "Reset failed jobs" button.
* (#216) Add support for Mocha.

### User agents

* Added Chrome 18 - 22
* Added Firefox 11 - 16
* Added Opera 12
* Added Safari 6.0
* Added Android 1.6
* Added Android 2.3
* Added Fennec 5 - 7
* Added Fennec 10
* Added iPad 3.2
* Added iPad 4.3
* Added iPad 5
* Added iPhone 3.2
* (#41) Added Opera Mini
* Added Opera Mini 2
* Added Palm Web 2

### Bugs fixed

* (#99) state=getrun should protect against broken/incomplete `runs` database entries.
* (#122) state=job inaccessible as GET view for HTML form.
* (#1) Server/client timezone difference should be accounted for.
* (#92) Tinder should show current browsers (regression in 0.2.0).
* (#83) Missing icon for hpwos.
* (#95) Browsers are wrongly ordered (i.e. IE10 before IE6 instead of after IE9).
* (#118) Clean up username mess.
* (#18) Detect database failure early and abort with friendly notice.
* (#110) User agent matching should prefer start instead of end.
* (#19) User signup doesnt check for erroneous return result, users not created.
* AddjobPage has been fixed and can now be used again from the browser.
* Fixed bug in Signup where it would initiate a session for a username, even if the INSERT
  query for the users table failed and the username remains unregistered.
* (#162) JobPage with no "new" runs but some "in progress" should still ajax refresh.
* (#166) natsort user agents in UserAction.
* (#182) JobPage AJAX needs error handler to fix infinite "Updating..".
* (#189) Shouldn't distribute runs that are being run already.
* job.js should keep refreshing even when everything is complete and "reset" happens.
* (#191) Preserve other window.onerror handlers (if there are any).
* (#210) When not logged in, dblclick for Wiperun on job pages should not make an
  API request, as it would just respond with "Not authorized".
* (#78) Replace generic "Chrome" user agent ID with regular versioned ones. Google Chrome
  versions are now testable like any other browser.

### Other changes

* (#121) Update jQuery from 1.3.2 to 1.7.2.
* (#70) Link to ScoresPage in the site menu.
* Drop redundant HTML5 attributes.
* Use protocol-relative urls where possible for urls to third-party domains that support
  both HTTP and HTTPS.
* Add `lang="en" dir="ltr"` to `<html>`.
* (#141) Old Perl example files in `/scripts/ that were no longer used or maintained have
  been removed from the repository.
* (#143) Using [phpbrowscap](https://github.com/garetjax/phpbrowscap) as user agent
  parser (data from <http://browsers.garykeith.com/>).
* Default settings are now stored in `/config/settings-default.json` (instead of
  hardcoded in `init.php`).
* The HomePage now includes all information from the SwarmstateAction (including the number
  of pending jobs for each user agent).
* Individual project pages are now accessible from main navigation on the home page (in
  the Project dropdown menu).
* Improve error handling for when database connetion can't be established.
* (#165) Expose runs/max in JobAction.


## 0.2.0 / 2012-03-07

### User agents

* (#97) Added Firefox 8
* (#97) Added Firefox 9
* (#101) Added Firefox 10 Beta
* (#104) Update Firefox 10.
* (#97) Added Preso 2.10
* (#102) Added iOS 5 / Mobile Safari.


## 0.1.0 / 2011-11-22

* (#94) First tagged version.
