<?php
/**
 * "Addjob" page.
 *
 * @author John Resig, 2008-2011
 * @author Timo Tijhof, 2012
 * @since 0.1.0
 * @package TestSwarm
 */

class AddjobPage extends Page {

	public function execute() {
		$action = AddjobAction::newFromContext( $this->getContext() );
		$action->doAction();

		$this->setAction( $action );
		$this->content = $this->initContent();
	}

	protected function initContent() {
		$request = $this->getContext()->getRequest();

		$this->setTitle( "Add new job" );
		$this->bodyScripts[] = swarmpath( "js/jquery.js" );
		$this->bodyScripts[] = swarmpath( "js/addjob.js" );

		$html = "";

		$error = $this->getAction()->getError();
		$data = $this->getAction()->getData();
		if ( $request->wasPosted() ) {
			if ( $error ) {
				$html .= html_tag( "div", array( "class" => "errorbox" ), $error["info"] );
			} elseif ( $data && isset( $data["id"] ) ) {
				$html .= '<div class="successbox">'
					. '<strong><a href="' . htmlspecialchars( swarmpath( "job/{$data["id"]}" ) )
					. '">Job ' . $data["id"] . '</a> has been created!</strong><br>'
					. $data["runTotal"] . ' runs have been scheduled to be ran in ' . $data["uaTotal"]
					. ' different browsers.<br><br>'
					. '<a href="' . htmlspecialchars( swarmpath( "job/{$data["id"]}" ) )
					. '"><em>view job page &raquo;</em></a>'
					. '</div>';
			}
		}

		$html .= $this->getAddjobFormHtml();

		return $html;
	}

	protected function getAddjobFormHtml() {
		$request = $this->getContext()->getRequest();

		$addjobPageUrl = htmlspecialchars( swarmpath( "addjob" ) );
		$userName = $request->getSessionData( "username" ) && $request->getSessionData( "auth" ) == "yes"  ? htmlspecialchars( $request->getSessionData( "username" ) ) : "";

		$formHtml = <<<HTML
<form action="$addjobPageUrl" method="post">

	<fieldset>
		<legend>Authentication</legend>

		<label for="form-authUsername">User name:</label>
		<input type="text" name="authUsername" value="$userName" id="form-authUsername">
		<br>
		<label for="form-authToken">Auth token:</label>
		<input type="text" name="authToken" id="form-authToken">
	</fieldset>

	<fieldset>
		<legend>Job information</legend>

		<label for="form-jobName">Job name (may contain HTML):</label>
		<input type="text" size="50" name="jobName" id="form-jobName">
		<br>
		<label for="form-runMax">Run max:</label>
		<input type="number" size="5" name="runMax" id="form-runMax" value="3" min="1" max="99">
		<p>This is the maximum number of times a run is ran in a user agent. If a run passes
		without failures then it is only ran once. If it does not pass, TestSwarm will re-try the run
		(up to "Run max" times) for that useragent to avoid error pollution due to time-outs, slow
		computers or other unrelated conditions that can cause the server to not receive a success report.</p>
	</fieldset>

	<fieldset>
		<legend>Browsers</legend>
		<p>Choose which groups of user agents this job should be ran in. Some of the groups
		overlap eachother, tests will not be ran twice in the same browser.</p>
		<p><strong>Note:</strong> The list of user agents is queried based on an <strong>inclusive</strong> algorithm.
		For example, not only "beta" (without "mobile") will not exclude all mobile browsers per se (e.g. "beta" includes
		some mobile browsers).</p>

		<input type="checkbox" name="browserSets[]" value="current" id="form-browserset-current">
		<label for="form-browserset-current"><strong>Current</strong>: The current stable release of all the major browsers.</label>
		<br>
		<input type="checkbox" name="browserSets[]" value="popular" id="form-browserset-popular" checked>
		<label for="form-browserset-popular"><strong>Popular</strong>: The most popular browsers (<a href="http://marketshare.hitslink.com/browser-market-share.aspx?qprid=2">99%+ of all browsers in use</a>).</label>
		<br>
		<input type="checkbox" name="browserSets[]" value="gbs" id="form-browserset-gbs">
		<label for="form-browserset-gbs"><strong>GBS</strong>: The browsers currently supported by Yahoo's <a href="http://developer.yahoo.com/yui/articles/gbs/">Graded Browser Support</a>.</label>
		<br>
		<input type="checkbox" name="browserSets[]" value="beta" id="form-browserset-beta">
		<label for="form-browserset-beta"><strong>Beta</strong>: Upcoming beta versions of popular browsers.</label>
		<br>
		<input type="checkbox" name="browserSets[]" value="mobile" id="form-browserset-mobile">
		<label for="form-browserset-mobile"><strong>Mobile</strong>: Popular releases of mobile browsers.</label>
		<br>
	</fieldset>

	<fieldset>
		<legend>Runs</legend>

		<p>Each job consists of several runs. Every run has a name and a url to where that test suite can be ran. All the test suites should probably have the same common code base or some other grouping characteristic, where each run is part of the larger test suite. As example, for a QUnit test suite the <code>filter</code> url parameter can be used to only run one of the "modules" so every run would be the name of that module and the URL to the testsuite with <code>?filter=modulename</code> appended to it.</p>

		<div id="runs-container">
			<fieldset>
				<legend>Run 1</legend>

				<label for="form-runNames1">Run name:</label>
				<input type="text" name="runNames[]" id="form-runNames1">
				<br>
				<label for="form-runUrls1">Run URL:</label>
				<input type="text" name="runUrls[]" placeholder="http://" size="50" id="form-runUrls1">
			</fieldset>
			<fieldset>
				<legend>Run 2</legend>

				<label for="form-runNames2">Run name:</label>
				<input type="text" name="runNames[]" id="form-runNames2">
				<br>
				<label for="form-runUrls2">Run URL:</label>
				<input type="text" name="runUrls[]" placeholder="http://" size="50" id="form-runUrls2">
			</fieldset>
			<fieldset>
				<legend>Run 3</legend>

				<label for="form-runNames3">Run name:</label>
				<input type="text" name="runNames[]" id="form-runNames3">
				<br>
				<label for="form-runUrls3">Run URL:</label>
				<input type="text" name="runUrls[]" placeholder="http://" size="50" id="form-runUrls3">
			</fieldset>
		</div>
	</fieldset>

	<input type="submit" value="Create job">
</form>
HTML;
		return $formHtml;
	}
}
