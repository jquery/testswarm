## Settings

Refer to [`./config/defaultSettings.json`](../config/defaultSettings.json) for the default values of these settings.

<span id="conf.general"></span>
## General settings
***NOTE***: Settings under section "general" are publicly retrievable through `api.php?action=info`.

* `general.timezone` (string): Timezone that things should be displayed in. See also http://php.net/date_default_timezone_set.

<span id="conf.database"></span>
## Database settings
A MySQL connection will be established to specified host. The database has to be set up before the application can be used. Refer to the README for more information.

* `database.host`: Hostname of MySQL server to connect to.
* `database.database`: Name of the database to use.
* `database.username`: Username to connect with to the above host must have permission on the above database to: `SELECT`, `INSERT`, `UPDATE` and `DELETE`.
* `database.password`: Password for the above username.

<span id="conf.customMsg"></span>
## Customizable interface messages
The `_html` or `_text` suffix indicates whether it is outputted as-is or html-escaped.

* `customMsg.homeIntro_html`: Message outputted on the HomePage, wrapped in `<blockquote><p>..</p></blockquote>`. `$1` is replaced with an html-escaped value of `web.title`.

<span id="conf.web"></span>
## Web Settings
***NOTE***: Settings under section "web" are publicly retrievable through `api.php?action=info`.

Settings for the web front-end (index.php)

* `web.server`: The server address including protocol and (if needed) the port. No trailing slash. When set to `null` (default), `init.php` will do a basic attempt to guess the right value. If urls are broken in your install, make sure to set this correctly.
* `web.contextpath`: The context path is the prefix (starting at the domain name root) used to access resources from the browser.  Make sure to also update `.htaccess` with the correct RewriteBase. Should be the absolute path from the root (e.g. `"/"`, or `"/testswarm/"`).
* `web.title`: Name for this TestSwarm instance. Will be used in the header of the site.
* `web.ajaxUpdateInterval`: Number of seconds to wait between AJAX refreshes in web interface (such as the run results table on the Job page).

<span id="conf.client"></span>
## Client settings
***NOTE***: Settings under section "client" are publicly retrievable through `api.php?action=info`.

***NOTE***: These are publicly exported on the Run page. When clients request new tests from API they also refresh the client settings. So updating these values affects all connected clients as well.

* `client.cooldownSleep` (number): After a run has been completed, wait this number of seconds before requesting the next run from the server.
* `client.nonewrunsSleep` (number): If server request for next run gave no results "No new tests to run", wait this number of seconds before trying again.
* `client.runTimeout` (number): Number of seconds a run may take to run. After this time the run-iframe will be cancelled and a timeout-report is submitted to `action=saverun instead.
* `client.saveReqTimeout` (number): Number of seconds the AJAX request to action=saverun on RunPage may take
;  until it is aborted.
* `client.saveRetryMax` (number): If the AJAX request to action=saverun on RunPage fails entirely (e.g. times out, 404 Not Found, 500 Internal Server Error, ..) it will retry up to this number of times before reaching out to the last resort (restarting the browser window). When in doubt, keep a higher rather than a lower value. Because it's better to have a client in a still-alive page retrying a few times until the server is back on, then refreshing too soon when the RunPage doesn't even render (which effectively permanently disconnects the client).
* `client.saveRetrySleep` (number): Number of seconds to wait between retries for `action=saverun`.
* `client.requireRunToken` (boolean):  Whether joining the swarm to run tests requires a token or not. By default anyone can join the swarm without needing a token or a registered account. When enabling this, run the refreshRunToken.php script to generate a token that will then have to be passed as "`run_token`" for GetrunAction, SaverunAction and on RunPage.
* `client.refreshControl` (number): Increasing this number will force all connected clients to refresh the window. Use this sparingly as a client will not be able to automatically reconnect if the refresh fails for whatever reason. When using AJAX to request new runs, there is a fallback, but for a complete refresh there is no catch.

<span id="conf.storage"></span>
## Storage
* `storage.cacheDir`: Which directory to use for caching. Should be readable/writeable and not executable from the web. Either outside the web root or protected by a restriction from `.htaccess`. As convenience "`$1`" will be replaced with the value of `$swarmInstallDir`.

<span id="conf.browserSets"></span>
## BrowserSets
***NOTE***: Define your own browserSets in the `localSettings` file. 

[Example](../config/sample-localSettings.json#L11)

<span id="conf.debug"></span>
## Debugging
* `debug.showExceptionDetails` (boolean): Wether to show exceptions in HTML output. By default Uncaught exceptions will be handled by PHP and cause a PHP Fatal error. Depending on your server settings these usually result in the request being aborted and the browser is given a HTTP 500 Internal Server Error (blank page unless [`display_errors`](http://www.php.net/manual/en/errorfunc.configuration.php#ini.display-errors) is on). TestSwarm catches these on a high level, and setting this to `true` will output the exception details.
* `debug.phpErrorReporting` (boolean): If enabled, php errors of all levels will be shown. Note that will allow PHP to output E_NOTICE and E_WARN errors in unexpected places and cause invalid HTML or distorted pages.
* `debug.dbLogQueries` (boolean): If enabled, the Database class will log all queries and the Page class will output them at the bottom of the HTML. In order to use this for [Api](API) responses, use `api?format=debug` which uses a Page as container. Be careful not to use this on a production site as this will show raw queries in HTML and could potentially contain information that users should not be able to see.