The example scripts in this directory serve as example however large parts of
them are very implementation specific. Below is documented which fields are required
from TestSwarms' perspective.

For more information about the fields, view the AddjobPage in your browser.

Be sure to obtain an authentication token first by [creating a project](../#create-projects).

## Test suite runs

Before you can submit a job you has to have an actual test suite. Presumably you already have this but there are a few requirements that you need to check and implement as needed. Currently TestSwarm supports the following unit test frameworks:

* [QUnit](http://qunitjs.com/) (jQuery)
* [UnitTestJS](http://github.com/tobie/unittest_js) (Prototype, Scriptaculous)
* [JSSpec](https://code.google.com/p/jsspec/) (MooTools)
* [JSUnit](http://www.jsunit.net/)
* [Selenium Core](http://seleniumhq.org/projects/core/)
* [Dojo Objective Harness](http://docs.dojocampus.org/quickstart/doh)
* [Screw.Unit](https://github.com/nathansobo/screw-unit)
* [Mocha](https://github.com/visionmedia/mocha)

The test suite must run from a publicly accessible URL that serves an HTML document (may be generated from e.g. PHP or a static file, extension doesn't matter) and needs to support being run in an `<iframe>` (e.g. no `X-IFrame-Options` headers that disallow embedding from a different origin, or javascript that forces being in the `top` window context).

### inject.js

The document must include the TestSwarm `inject.js` file from the target TestSwarm installation. This file detects which unit test framework is present on the page and registers a callback for when that framework indicates it is done. The script then extracts the test results and submits them to TestSwarm, using the identification given from the TestSwarm context (run-id, client-id etc.).

You may unconditionally `inject.js`, it will only act if it detects that it is being run in a TestSwarm environment (so you don't need a seperate version of the test suite "for TestSwarm").

The `inject.js` file should be included in the `<head>` **after** the unit test framework of choice (as it needs to register a hook), but **before** the test suite starts.

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

## Request

* Must be a `POST` request
* To `http://swarm.example.org/api.php?action=addjob`

## Fields

### Authentication

Project that will be the owner of this new job.

* `authID`: Matching entry from `projects.id` field in the database
* `authToken`: Matching entry from `projects.auth_token` field in the database

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
