# TestSwarm Reference Documentation

## Classes

TestSwarm (as of v1.0.0) is programmed in an [object-oriented](https://en.wikipedia.org/wiki/Object-oriented_programming) fashion and based around the principle of "context". A short overview of the main classes follows.

### BrowserInfo

Given a user agent string this class parses into an object and extracts information about the client. Such as browser name, version and operating system. It also tries to find the "TestSwarm user agent ID", the key in a browserSet in `localSettings.json`. We use the [ua-parser](https://github.com/tobie/ua-parser) library for parsing.

### Database

Although right now the only supported database backend is MySQL, the Database class abstracts most MySQL specific methods so that it is fairly easy to change this later on. It also offers various convenience methods  (such as `getOne`, `getRow` and `getRows`), and uses proper context based calls to the underlying php functions (e.g. methods like `mysql_error` and `mysql_insert_id` are always called with the connection object in case multiple connections are active).

### WebRequest

Abstraction for accessing request data (`$_GET`, `$_POST`, `$_SESSION` and IP-address). Also provides several convenience methods such as `wasPosted()`, `getBool, `getInt`, `getArray` and `getVal( key, defaultValue )`.

### TestSwarmContext

The above and other classes are lazy loaded when they are needed. Both the class files themselves are lazy-loaded using the PHP Autoloader and the initialization of the classes is done on-the-fly when they are first needed and the instance is cached from there for re-use.

### Action

The base class for all actions. Actions are the logic independent from the front-end. Pages and the API both use these to get the information in a structured format.

### Page

Formats the skin for `index.php` requests and Page-extending classes implement page specific output, gathered from one or more instances of Action classes.

## Globals

As of TestSwarm 1.0.0 TestSwarm practically uses zero globals. There are however 2 global variables used in some legacy functions that will be phased out later.

* `$swarmInstallDir` (string)
This is the absolute path of the directory where the TestSwarm install is located in the file system.

* `$swarmContext` (TestSwarmContext)
Primary instance of the `TestSwarmContext` class. Should be used as little as possible. In general only within an entry point file (`index.php`, `api.php`). In any other environment the current context should be used from within the enclosing class.

* `swarmAutoLoadClasses` (array)

## Settings
See [Settings](./Settings.md) page.

## Database schema

* **projects** - Identification and authentication for projects that submit jobs.
* **clients** - Client sessions. Each session stores a freeform name and User-Agent string.
* **jobs** - Jobs created by a project. Each job has a freeform name. When jobs are created, they also describe a list of runs (name and url), and list of browsers in which each test suite should be run. (linked: `projects.id`)
* **runs** - The run names and urls submitted as part of the job. (linked: `jobs.id`)
* **run_useragent** - State for each of the runs for each of the requested browsers. Has a nullable field pointing to the stored result in the `runresults` table. If a run is requested to re-try in case of test failure (or manually reset from the web interface), then fields in this table will be reset to their initial value. (linked: `runs.id`, `runresults.id`)
* **runresults** - Each result from a client that tested a run. There may be multiple results for the same run as jobs may specify a number of retries. Jobs may also be manually reset from the web interface. Old results are kept and permalinks to it will continue to work. (linked: `runs.id`, `clients.id`)