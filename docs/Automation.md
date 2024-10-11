# Automated Distributed Continuous Integration for JavaScript

_Just getting started with TestSwarm? Check out the [README](../README.md) instead._

This document describes how **TestSwarm** can be used in a large scale testing environment. 

Features:

* Fully **automated submissions of jobs** to TestSwarm
* The **swarm is automatically populated** with browser clients
* Events can be used to create additional **post-build actions** (such as IRC notifications).

<a name="p-techsum"></a>
Main frameworks: [TestSwarm](https://github.com/jquery/testswarm), [Jenkins](http://jenkins-ci.org/), [QUnit](https://github.com/jquery/qunit)

Utilities: [node-testswarm](https://github.com/jzaefferer/node-testswarm), [node-browserstack](https://github.com/scottgonzalez/node-browserstack), [testwarm-browserstack](https://github.com/clarkbox/testswarm-browserstack), [grunt](https://github.com/cowboy/grunt)

**TL;DR: &rarr; Check out the [End result](#h-end_result).**

## Prerequisites

The following prerequisites are assumed to be installed already (this document does not cover setting that up, these software packages have their own manuals):

* Install [jQuery TestSwarm](https://github.com/jquery/testswarm) (obviously)
 * Create an account for your project in TestSwarm, and take note of the authToken for that account.
* Install [Jenkins](http://jenkins-ci.org/)<br>with the following plugins: <small>(depending on your application and development workflow you may need different ones)</small>
 * [Git Plugin](https://wiki.jenkins-ci.org/display/JENKINS/Git+Plugin)
 * [Github Plugin](https://wiki.jenkins-ci.org/display/JENKINS/Github+Plugin)
 * [Instant Messaging Plugin](https://wiki.jenkins-ci.org/display/JENKINS/Instant+Messaging+Plugin)
 * [IRC Plugin](https://wiki.jenkins-ci.org/display/JENKINS/IRC+Plugin)
 * [AnsiColor Plugin](https://wiki.jenkins-ci.org/display/JENKINS/AnsiColor+Plugin)
* Account at BrowserStack (required to use the [BrowserStack API](http://www.browserstack.com/automated-browser-testing-api))

Note that none of these are claimed to be the "ultimate" tool. They all have alternatives and competitors.

For example:

* Jenkins: It is not required for a working continuous integration environment with TestSwarm. TestSwarm itself is (although basic) a fine continuous integration framework on its own. If you prefer to use TestSwarm (instead of Jenkins) as the central manager of your workflow that is also possible. In that case you will have to find a different way to trigger jobs. For example you could write your own script (e.g. ran from crontab) that checks your incoming source of commits (Self-hosted Git or SVN repo, GitHub, Gerrit, ..) and take care of copying the source at the current revision into a [static directory](#h-static_clone) and submit jobs from there.
* BrowserStack: There are alternatives (check out [this StackOverflow post](http://stackoverflow.com/a/10387902/319266)), but those may or may not have an API that allows automatic starting and terminating of browsers. You can also populate your swarm manually by crowdsourcing it. If you can rely on that, go for it (and that's free, whereas cloudbased solutions like BrowserStack usually aren't).
* QUnit: Developed and used by all jQuery projects and many other projects as well. However, TestSwarm also supports [various other frameworks](https://github.com/jquery/testswarm/tree/master/scripts/addjob#test-suite-runs).

<a name="h-static_clone"></a>

## Static clone

Due to the asynchronous nature of TestSwarm it is important that tests are ran on a static copy of your source code, fixed on the version that it should be testing. Depending on your project there can be a few ways to do this. Here is an example for jQuery and MediaWiki.

### jQuery

[jQuery Core](http://jquery.com/) only contains static files (static, e.g. there are no PHP files that should be ran from a webserver or need database installation). So making a static copy for this is fairly straight forward. All we do is copy the repository directory (except for the `.git` folder) into another directory that is named after the current commit ID, and make is accessible from the web. That location is then submitted to TestSwarm so that all browser clients can run the tests.

The following is an example of such a script to make a static copy. The script in particular is used by jQuery on http://swarm.jquery.org/ to make static clones of their git repositories after the "build" process is completed (e.g. concatenate all modules and produce the the raw and minified versions):

```bash
#!/bin/bash

# Make a static clone the working copy of a Jenkins build
# to a public directory for browser testing.
# Also cleans clones older than 14 days
#
# Parameters:
# $1: Publish target (absolute path, without "$1")
# $2: ID to use for the static clone directory
#
# Usage:
# From within the Jenkins working copy directory.
#
# version 5 (2012-07-31)

dest=/var/www/builds.jenkins.jquery.com/htdocs

#echo "Creating a static clone of the current directory."
#echo "Group: $1"
#echo "Copy ID: $2"

mkdir -p $dest/$1/$2

echo "Copying... to " $dest/$1/$2
rsync -a --exclude=".git*" . $dest/$1/$2

echo "Cleaning up outdated copies"
cd $dest/$1
find . -mindepth 1 -maxdepth 1 -ctime +14 | grep -v master | xargs rm -rf
```

### MediaWiki

If your project is more of a web application that contains PHP files and requires actually being "installed" in order to run the test suite, then this example (based on [MediaWiki](https://www.mediawiki.org/)) may be more helpful. MediaWiki is maintained in a Git repository managed by a Gerrit install at the [Wikimedia Foundation](https://www.wikimedia.org/). All commits are reviewed and tested before being merged into master. More about that:
* [Gerrit Code Review](https://code.google.com/p/gerrit/)
* [Gerrit Trigger](https://wiki.jenkins-ci.org/display/JENKINS/Gerrit+Trigger) (Jenkins plugin)

The build for MediaWiki goes through several steps. Most steps are not related to making a static copy for TestSwarm, so those steps are simplified here. The summary here is intended as guide of inspiration for your own project
* Fetch from Gerrit ([fetch_gerrit_head.sh](https://gerrit.wikimedia.org/r/gitweb?p=integration/jenkins.git;a=blob;f=bin/fetch_gerrit_head.sh;h=f464d0606a74662b7f2469aa527d6c65964a9cfc;hb=HEAD))
* Install it
 * Using php from the command line to invoke an installation script
* Snapshot ([testswarm-snapshot](https://gerrit.wikimedia.org/r/gitweb?p=integration/jenkins.git;a=blob;f=jobs/_shared/build.xml;h=aee477988fdc5f3fe231e0b35180466ffbe8826d;hb=HEAD#l188))
 * Make a copy (without the `.git` dir) of the working space into a fixed place (e.g. `/srv/integration.mediawiki.org/clones/mw/{gerrit_change_id}/{patch_version}`)
 * Copy database (in case of sqlite this is fairly simple, just copying the file. In the case of MySQL it is more complicated. You'll need to rename/move the database to unique name)
 * Tweak configuration (update database settings and location-dependent settings)
 * Clear location-dependent caches

## Jenkins

### Jenkins job configuration:

* GitHub project: `https://github.com/example/test/`
* Source Code Management: `Git`
 * URL of repository: `git://github.com/example/test.git`
 * Branch Specifier: `master`
* Build Triggers
 * [x] Build when a change is pushed to GitHub
* Build<br>**This highly depends on your project. This example is for jQuery**
 * Execute shell<br>- installs the dependencies of your project as specified in `package.json` ([jquery example](https://github.com/jquery/jquery/blob/master/package.json))<br>- executes the main building proces and testing procedure as specified in `Gruntfile.js` ([jquery example](https://github.com/jquery/jquery/blob/master/Gruntfile.js)).<br>If your only test is TestSwarm, then this "grunt" step is redundant. Examples of things that could be in this "grunt" step are: `jshint` or `jslint`, `csslint`, `htmllint`, "running qunit in phantomjs" etc. 
```bash
npm install
grunt
```
 * Execute shell<br>This is making the static clone, more about that in the previous section.
```bash
/srv/swarm.jquery.org/tools/jenkins-testswarm-static-copy.sh jquery ${GIT_COMMIT}
```
 * Execute shell<br>This executes the "testswarm" task as defined in `Gruntfile.js` ([jquery example](https://github.com/jquery/jquery/blob/master/Gruntfile.js#L111)). This task uses the `node-testswarm` module to create a request to the TestSwarm installation and creates a new job, using the current Jenkins build as the target. `node-testswarm-config.json` is not related to the `node-testswarm` package itself, but is used by jquery's Gruntfile.js to not hardcode configuration settings in the repository (and for private settings, such as authTokens).
```bash
grunt testswarm:${GIT_COMMIT}:/srv/swarm.jquery.org/tools/node-testswarm-config.json
```
* Post-build Actions
 * [x] IRC Notification (Channels: _optionally override the channels for this build from the main plugin config_)

### Jenkins plugin configuration

#### IRC

* [x] Enable IRC Notification
 * Hostname: irc.freenode.net
 * Port: 6667
 * Channels: _default list of channels for this project, can be overridden in the job config_
 * Nickname: _nickname of jenkins irc bot_

#### GitHub Web Hook

 * [x] Manually manage hook URLs

## Cron

Last but not least there are a few things that should happen frequently but have no other place to go. Those are put into crontab.

### BrowserStack

Using the [BrowserStack API](https://github.com/browserstack/api) and the [TestSwarm API](./API.md) it becomes very easy to keep an eye on the swarm and see which browsers are needed, and fill the swarm with the appropriate browsers by instantly creating new virtual machines with the correct OS/browser/browser-version combination and pointing the browser to the TestSwarm run page. This last sentence sounds like a lot of work, but it couldn't be simpler with the BrowserStack API and the [testswarm-browserstack](https://github.com/clarkbox/testswarm-browserstack/) module.

* Install this module ([readme](https://github.com/clarkbox/testswarm-browserstack#readme))
* Copy [sample-config.json](https://github.com/clarkbox/testswarm-browserstack/blob/master/sample-config.json) to `config.json` and fill in the variables (swarm location, swarm run page, browserstack credentials).
* Copy [sample-run.sh](https://github.com/clarkbox/testswarm-browserstack/blob/master/sample-run.sh) to `run.sh`
* Add to crontab.

With just that, every 2 minutes the testswarm-browserstack module will query the TestSwarm API to see which browsers have pending runs, query the BrowserStack API with your credentials and check which browsers are running. Then it will terminate no longer needed browsers, and where not already, start additional browsers to join the swarm.
<a name="h-end_result"></a>

## End result

The [used programs](#p-techsum) are independent but work really well when put together. This guide is fairly in-depth, but with a little luck you'll be able to set it up in no-time.

The end result is as follows (note that this workflow is beyond theory, it is the reality for most jQuery projects right now!)

1. **Commit**<br>Someone pushes a commit to the repository <small>(or in the case of a system based on GitHub pull-request or Gerrit merge-request, even when someone sends a patch)</small>
2. **Build**<br>Jenkins will pick up on it and start a build ([example](http://swarm.jquery.org:8080/job/QUnit/119/)) and goes through the steps ([example](http://swarm.jquery.org:8080/job/QUnit/119/console)):
   * Execute any necessary building steps and/or run local tests (e.g. lint or qunit)
   * Submit job to TestSwarm (with node-testswarm)
   * Meanwhile, the cron testswarm-browserstack command will keep querying TestSwarm API and start up missing browsers and let them join the swarm, and stop no longer needed virtual machines.
   * Meanwhile, TestSwarm keeps distributing pending runs to the swarm clients, which send results back through the TestSwarm API with AJAX
   * node-testswarm detects there are no more pending runs for this job, gets the job runresults and marks the Jenkins build as completed and set the Jenkins build status to passed (or failed)
   * Any registered hooks will be ran (notification to irc, a comment on the merge-request with stats, etc.)
3. **Repeat** :)
