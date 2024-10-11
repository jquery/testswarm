# TestSwarm API

As of v1.0.0, TestSwarm provides a Web API.

It is reachable at `{swarmroot}/api.php`

## Formats

* [JSON](https://en.wikipedia.org/wiki/JSON) (**recommended**)
* [JSON-P](https://en.wikipedia.org/wiki/JSONP) (using `callback` parameter)
* Debug (see below)

### JSON

See also [http://json.org/](http://json.org/) for a list of decoders in various programming languages.

### JSON-P

Passes the JSON to a callback function (named in the `callback` url parameter). This enables use of the TestSwarm API cross-domain in the web browser.

**NB:** For security reasons and to avoid abuse, the API does not allow logged-in sessions via JSONP.

### Debug

The debug format shows an HTML page instead with information about the request and then response is shown in a "pretty" format for human reading using PHPs `var_dump` function. Together with the configuration options `php_error_reporting` and `db_log_queries` in the `[debug]` section of `testswarm.ini`, this is a very wide range debugging interface for API queries.

## Actions

A full list of actions can be found in the `./inc/actions` directory of your TestSwarm installation. See the `@actionXXX` comments above the `doAction` methods in each class for more information about how the action should be used and what the required parameters and request methods are. 