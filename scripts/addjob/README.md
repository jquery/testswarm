The example scripts in this directory serve as example however large parts of
them are very implementation specific. Below is documented which fields are required
from TestSwarms' perspective.

For more information about the fields, view the AddjobPage in your browser.

## Test suite runs

Before you can submit a job you has to have an actual test suite. Presumably you already have this but there are a few requirements that you need to check and implement as needed. Currently TestSwarm supports the following unit test frameworks:

* [QUnit](http://qunitjs.com/) (jQuery)
* [UnitTestJS](http://github.com/tobie/unittest_js) (Prototype, Scriptaculous)
* [JSSpec](https://code.google.com/p/jsspec/) (MooTools)
* [JSUnit](http://www.jsunit.net/)
* [Selenium Core](http://seleniumhq.org/projects/core/)
* [Dojo Objective Harness](http://docs.dojocampus.org/quickstart/doh)
* [Screw.Unit](https://github.com/nathansobo/screw-unit)
* [Jasmine](http://pivotal.github.com/jasmine/)

The test suite must run from a publicly accessible URL that serves an HTML document (may be generated from e.g. PHP or a static file, extension doesn't matter) and needs to support being run in an `<iframe>` (e.g. no `X-IFrame-Options` headers that disallow embedding from a different origin, or javascript that forces being in the `top` window context).

### inject.js

The document must include the TestSwarm `inject.js` file from the target TestSwarm installation. This file detects which unit test framework is present on the page and registers a callback for when that framework indicates it is done. The script then extracts the test results and submits them to TestSwarm, using the identification given from the TestSwarm context (run-id, client-id etc.).

You may unconditionally `inject.js`, it will only act if it detects that it is being run in a TestSwarm environment (so you don't need a seperate version of the test suite "for TestSwarm").

The `inject.js` file should be included in the `<head>` **after** the unit test framework of choice (as it needs to register a hook), but **before** the test suite starts.

You can also conditionally include the `inject.js` file. The snippet below will detect the required url params and add the `inject.js` script to the page.  

```javascript
(function() {
	var idx, injectURL, swarmURL, searchParts, part;

	searchParts = window.location.search.split("&");

	for (idx = 0; idx < searchParts.length; idx++ ) {
		part = searchParts[idx].split("=");
		
		if ( part[0] === "swarmURL"  ) {
			swarmURL = decodeURIComponent(part[1]);
		} else if ( part[0] === "swarmInjectURL" ) {
			injectURL = decodeURIComponent(part[1]);
		}
	}

	if ( !swarmURL || swarmURL.indexOf("http") !== 0 || !injectURL || injectURL.indexOf("http") !== 0) {
		return;
	}
	
	document.write("<scr" + "ipt src='"+injectURL+"?" + (new Date).getTime() + "'></scr" + "ipt>");
})();
```

### Example (QUnit)

Note that any QUnit specific details here may out of date. Pay attention to the `inject.js` inclusion.

```html
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<meta charset="UTF-8">
		<title>QUnit Test Suite</title>
		<!-- Third-pary libraries -->
		<link rel="stylesheet" href="libs/jquery.qunit/qunit.css">
		<script src="libs/jquery/jquery.js"></script>
		<script src="libs/jquery.qunit/jquery.qunit.js"></script>

		<!-- TestSwarm link -->
		<script src="http://example.org/testswarm/js/inject.js"></script>

		<!-- Your application scripts -->
		<script src="src/foo.js"></script>
		<script src="src/bar.js"></script>

		<!-- Your application test suites -->
		<script src="tests/foo.test.js"></script>
		<script src="tests/bar.test.js"></script>
	</head>
	<body>
		<div id="qunit"></div>
		<div id="qunit-fixture"></div>
	</body>
</html>
```

### Example (Jasmine)

Note that any Jasmine specific details here may out of date. Pay attention to the `inject.js` inclusion.

```html
<!DOCTYPE HTML>
<html>
<head>
  <title>Jasmine Spec Runner</title>

  <link rel="shortcut icon" type="image/png" href="lib/jasmine-1.2.0/jasmine_favicon.png">
  <link rel="stylesheet" type="text/css" href="lib/jasmine-1.2.0/jasmine.css">
  <script type="text/javascript" src="lib/jasmine-1.2.0/jasmine.js"></script>
  <script type="text/javascript" src="lib/jasmine-1.2.0/jasmine-html.js"></script>

  <!-- include source files here... -->
  <script type="text/javascript" src="spec/SpecHelper.js"></script>
  <script type="text/javascript" src="spec/PlayerSpec.js"></script>

  <!-- include spec files here... -->
  <script type="text/javascript" src="src/Player.js"></script>
  <script type="text/javascript" src="src/Song.js"></script>

  <!-- TestSwarm link --> 
  <script src="http://example.org/testswarm/js/inject.js">

  <script type="text/javascript">
    (function() {
      var jasmineEnv = jasmine.getEnv();
      jasmineEnv.updateInterval = 1000;

      var htmlReporter = new jasmine.HtmlReporter();

      jasmineEnv.addReporter(htmlReporter);

      jasmineEnv.specFilter = function(spec) {
        return htmlReporter.specFilter(spec);
      };

      var currentWindowOnload = window.onload;

      window.onload = function() {
        if (currentWindowOnload) {
          currentWindowOnload();
        }
        execJasmine();
      };

      function execJasmine() {
        jasmineEnv.execute();
      }

    })();
  </script>

</head>

<body>
</body>
</html>
```

## Request

* Must be a `POST` request
* To `http://swarm.example.org/api.php?action=addjob`

## Fields

### Authentication

Username with a user that will be the owner of this new job.

* `authUsername`: Matching entry from `users.name` field in the database 
* `authToken`: Matching entry from `users.auth` field in the database

### Job information

* `jobName`: Job name (may contain HTML) (e.g. `Foobar r123` or `Lorem ipsum <a href="..">#h0s4</a>`)
* `runMax`

### Browsers

* `browserSets[]=`: Key in the `browserSets` configuration (e.g. `currentDesktop` or a custom browserset defined in your configuration file).
* `browserSets[]=` ..
* `browserSets[]=` ..

### Runs

Run name/url pairs.

* `runNames[]`: Run name (e.g. "module foo")
* `runUrls[]`: Run URL (absolute url, including http:// or https://)
* `runNames[]`: ..
* `runUrls[]`: ..

## Submitting a job using NodeJS

You can also submit a job using NodeJS using [node-testswarm](https://github.com/jzaefferer/node-testswarm/).

```javascript
var testswarm = require( "testswarm" );

testswarm({
	url: "http://example.com/testswarm",
	pollInterval: 10000,
	timeout: 1000 * 60 * 30,
	done: function() {console.log('Tests Complete');}
}, {
	authUsername: "admin",
	authToken: "*********************",
	jobName: "MyTestJob",
	runMax: 3,
	"runNames[]": ['specs'],
	"runUrls[]": ["http://myDomain.local/Runner.html"],
	"browserSets[]": ["default"]
});
```
