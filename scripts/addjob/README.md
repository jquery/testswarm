These example scripts in this directory serve as example however large parts of
them are very implementation specific. Below are the fields actually required
from TestSwarms' perspective.

For more information about the fields, view the AddjobPage in your browser.

## Test suite runs

Before you can submit a job you has to have an actual test suite. Presumably you already already have this but there are a few requirements that you need to check and implement as needed. Currently TestSwarm supports the following unit test frameworks:

* [QUnit](http://docs.jquery.com/QUnit) (jQuery)
* [UnitTestJS](http://github.com/tobie/unittest_js) (Prototype, Scriptaculous)
* [JSSpec](https://code.google.com/p/jsspec/) (MooTools)
* [JSUnit](http://www.jsunit.net/)
* [Selenium Core](http://seleniumhq.org/projects/core/)
* [Dojo Objective Harness](http://docs.dojocampus.org/quickstart/doh)
* [Screw.Unit](https://github.com/nathansobo/screw-unit)

The test suite must run from a publicly accessible URL that serves an HTML document (may be generated from e.g. PHP or a static file, extension doesn't matter) and needs to support being run in an `<iframe>` (e.g. no `X-IFrame-Options` headers that disallow embedding from a different origin, or javascript that forces being in the `top` window context).

### inject.js

The document must include the TestSwarm `inject.js` file from the target TestSwarm installation. This file detects whcih unit test framework is present on the page and registers a callback for when that framework indicates it is done. The script then extracts the test results and submits them to TestSwarm, using the identification given from the TestSwarm context (run-id, client-id etc.).

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
		<script src="libs/jquery/jquery.js">
		<script src="libs/jquery.qunit/jquery.qunit.js">

		<!-- TestSwarm link -->
		<script src="http://example.org/testswarm/js/inject.js">

		<!-- Your application scripts -->
		<script src="src/foo.js">
		<script src="src/bar.js">

		<!-- Your application test suites -->
		<script src="tests/foo.test.js">
		<script src="tests/bar.test.js">
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

Username with a user that will be the owner of this new job.

* `authUsername`: Matching entry from `users.name` field in the database 
* `authToken`: Matching entry from `users.auth` field in the database

### Job information

* `jobName`: Job name (may contain HTML) (e.g. `Foobar r123` or `Lorem ipsum <a href="..">#h0s4</a>`)
* `runMax`

### Browsers

* `browserSets[]=`: One of `current`, `popular`, `gbs`, `beta`, `mobile`.
   Correspond to the `useragents` table
* `browserSets[]=` ..
* `browserSets[]=` ..

### Runs

Run name/url pairs.

* `runNames[]`: Run name (e.g. "module foo")
* `runUrls[]`: Run URL (absolute url, including http:// or https://)

* `runNames[]`: ..
* `runUrls[]`: ..
