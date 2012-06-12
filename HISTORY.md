## 1.0.0-pre

***NOT A RELEASE YET***

This major release features a complete rewrite of the front-end and back-end code base, and
adds several major features such as a brand new API, automatically synchronized run-client
settings and a new face using Twitter Bootstrap.

TestSwarm has also incorporated several optimizations for a continous integration workflow
where TestSwarm is used with Jenkins and BrowserStack (or similar services) by providing
detailed information about the swarm's state through "api.php?action=swarmstate". This new API
action lists all supported browsers including how many clients are online for each user agent
and if and how many pending runs there are. This makes it easy to then use this information to
automatically start or terminate browsers as needed in, for example, using the [BrowserStack
API](https://github.com/browserstack/api) and
[node-browserstack](https://github.com/scottgonzalez/node-browserstack). 

The database has remained the same mostly, although there are a few breaking change in some of
the relationships and column types. You are recommended to start clean as there is no practical
upgrade path. If needed, you could take note of the authentication token of your main account
in the "users.auth" column and after re-creating the the accounts on a new 1.0 TestSwarm,
manipulate the "users.auth" field in the database to match the old value. Other than that, no
data is to be imported from a pre-1.0 database.

Complete list of issues solved in the 1.0.0 milestone:

* <https://github.com/jquery/testswarm/issues?sort=updated&direction=asc&state=closed&milestone=1>

### New features

* (#82) Refactor front-end skin, implemented Twitter Bootstrap (#145).
* (#107) Implement browser client settings framework.
* (#127) Refactor backend with no globals into OOP with Context.
* (#132) Refactor state/content/logic scripts into Page/Action classes.
* (#115) Implement API, including various new actions (#105, #116).
* (#139) Create action for getting current status of the swarm.
* (#143) Refactor useragent storage and detection.
* (#150) Expose current TestSwarm version in InfoAction API and in `<meta>` on Pages.
* (#146) Add noindex rules.
* (#149) Create action "projects" for listing all accounts that submit jobs.
* Implement basic caching for expansive operations in flat files in `./cache/`.
* Implement "debug" mode for the API (format=debug)
* Implement "debug" mode for Database queries. Disabled by default for performance and
  security reasons, enable by setting [debug][db_log_queries] = 1 in testswarm.ini.
* (#142) Implement database update script.
* (#158) Implement database install script.
* Implement browserset query script.
* (#172) Use JSON for configuration files.
* Create "Info" page. Showing current version info with links to GitHub.

### User agents

* Added Chrome 18.
* Added Chrome 19.
* Added Firefox 11.
* Added Firefox 12.
* (#168) Added Firefox 13.
* Added Opera 12 beta.
* Added Android 1.6.
* Added Android 2.3.
* Added Fennec 5.
* Added Fennec 6.
* Added Fennec 7.
* Added Fennec 10.
* Added iPad 3.2.
* Added iPad 4.3.
* Added iPad 5.
* Added iPhone 3.2.
* (#41) Added Opera Mini.
* Added Opera Mini 2.
* Added Palm Web 2.

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

### Other changes

* (#121) Update jQuery from 1.3.2 to 1.7.1.
* (#70) Link to ScoresPage in the site menu.
* Drop redundant HTML5 attributes
* Use protocol-relative urls where possible for urls to third-party domains that support
  both HTTP and HTTPS.
* Add `lang="en" dir="ltr"` to `<html>`.
* Add debug mode configuration to testswarm.ini.
* (#141) Old Perl example files in ./scripts/ that were no longer used or maintained have
  been removed from the repository.
* (#143) Using [phpbrowscap](https://github.com/garetjax/phpbrowscap) as user agent parser
  (data from <http://browsers.garykeith.com/>).
* The default `cooldown_sleep` has been lowered from 15 seconds to 2 seconds. The update rest
  period between points where there are no new tests is still 30 seconds, however.
* The HomePage now includes all information from the SwarmstateAction regarding the number of
  pending jobs for each user agent.
* The main site navigation now includes the projects list in a dropdown menu. This together
  with the new ProjectsPage has made individual project pages accessible from the HomePage
  for the first time.
* Better error handling if DB is unavailable.
* PHP version requirement raised to 5.3.0+.
* (#165) Expose runs/max in JobAction.
* Default settings are now stored in `./config/testswarm-defaults.json` (instead of in
  `init.php`). Local settings are read from `./config/testswarm.json`.
* (#78) Replace generic "Chrome" user agent ID with regular versioned ones. Google Chrome
  versions are now testable like any other browser.


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
