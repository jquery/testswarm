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
		$this->bodyScripts[] = swarmpath( "js/addjob.js" );

		$html = "";

		$error = $this->getAction()->getError();
		$data = $this->getAction()->getData();
		if ( $request->wasPosted() ) {
			if ( $error ) {
				$html .= html_tag( "div", array( "class" => "alert alert-error" ), $error["info"] );
			} elseif ( $data && isset( $data["id"] ) ) {
				$html .= '<div class="alert alert-success">'
					. '<strong><a href="' . htmlspecialchars( swarmpath( "job/{$data["id"]}" ) )
					. '">Job ' . $data["id"] . '</a> has been created!</strong><br>'
					. $data["runTotal"] . ' runs have been scheduled to be ran in ' . $data["uaTotal"]
					. ' different browsers.<br><br>'
					. '<a class="btn btn-primary btn-small" href="' . htmlspecialchars( swarmpath( "job/{$data["id"]}" ) )
					. '">continue to job page &raquo;</a>'
					. '</div>';
			}
		}

		$html .= $this->getAddjobFormHtml();

		return $html;
	}

	protected function getAddjobFormHtml() {
		$conf = $this->getContext()->getConf();
		$request = $this->getContext()->getRequest();

		$swarmUaIndex = BrowserInfo::getSwarmUAIndex();

		$addjobPageUrl = htmlspecialchars( swarmpath( "addjob" ) );
		$userName = $request->getSessionData( "username" ) && $request->getSessionData( "auth" ) == "yes"  ? htmlspecialchars( $request->getSessionData( "username" ) ) : "";

		$formHtml = <<<HTML
<form action="$addjobPageUrl" method="post" class="form-horizontal">

	<fieldset>
		<legend>Authentication</legend>

		<div class="control-group">
			<label class="control-label" for="form-authUsername">User name:</label>
			<div class="controls">
				<input type="text" name="authUsername" value="$userName" id="form-authUsername">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="form-authToken">Auth token:</label>
			<div class="controls">
				<input type="text" name="authToken" id="form-authToken" class="input-xlarge">
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>Job information</legend>

		<div class="control-group">
			<label class="control-label" for="form-jobName">Job name:</label>
			<div class="controls">
				<input type="text" name="jobName" id="form-jobName" class="input-xlarge" maxlength="255">
				<span class="help-inline">HTML, up to 255 characters</span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="form-runMax">Run max:</label>
			<div class="controls">
				<input type="number" size="5" name="runMax" id="form-runMax" value="2" min="1" max="99">
				<p class="help-block">This is the maximum number of times a run is ran in a user agent. If a run passes
				without failures then it is only ran once. If it does not pass, TestSwarm will re-try the run
				(up to "Run max" times) for that useragent to avoid error pollution due to time-outs, slow
				computers or other unrelated conditions that can cause the server to not receive a success report.</p>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>Browsers</legend>

		<p>Choose which groups of user agents this job should be ran in. Some of the groups may
		overlap each other, TestSwarm will detect and remove duplicate entries in the resulting set.</p>

HTML;
		foreach ( $conf->browserSets as $set => $browsers ) {
			$set = htmlspecialchars( $set );
			$browsersHtml = '';
			$last = count( $browsers ) - 1;
			foreach ( $browsers as $i => $browser ) {
				if ( $i !== 0 ) {
					$browsersHtml .= $i === $last ? '<br> and ' : ',<br>';
				} else {
					$browsersHtml .= '<br>';
				}
				$browsersHtml .= htmlspecialchars( $swarmUaIndex->$browser->displaytitle );
			}
			$formHtml .= <<<HTML
		<div class="control-group">
			<label class="checkbox" for="form-browserset-$set">
				<input type="checkbox" name="browserSets[]" value="$set" id="form-browserset-$set">
				<strong>$set</strong>: $browsersHtml.
			</label>
		</div>
HTML;
		}

		$formHtml .= <<<HTML
	</fieldset>

	<fieldset>
		<legend>Runs</legend>

		<p>Each job consists of several runs. Every run has a name and a url to where that test suite can be ran. All the test suites should probably have the same common code base or some other grouping characteristic, where each run is part of the larger test suite. As example, for a QUnit test suite the <code>filter</code> url parameter can be used to only run one of the "modules" so every run would be the name of that module and the URL to the testsuite with <code>?filter=modulename</code> appended to it.</p>

		<div id="runs-container" class="well">
			<fieldset>
				<legend>Run 1</legend>

				<label for="form-runNames1">Run name:</label>
				<input type="text" name="runNames[]" id="form-runNames1" maxlength="255">
				<br>
				<label for="form-runUrls1">Run URL:</label>
				<input type="text" name="runUrls[]" placeholder="http://" class="input-xlarge"id="form-runUrls1">
			</fieldset>
			<fieldset>
				<legend>Run 2</legend>

				<label for="form-runNames2">Run name:</label>
				<input type="text" name="runNames[]" id="form-runNames2" maxlength="255">
				<br>
				<label for="form-runUrls2">Run URL:</label>
				<input type="text" name="runUrls[]" placeholder="http://" class="input-xlarge"id="form-runUrls2">
			</fieldset>
			<fieldset>
				<legend>Run 3</legend>

				<label for="form-runNames3">Run name:</label>
				<input type="text" name="runNames[]" id="form-runNames3" maxlength="255">
				<br>
				<label for="form-runUrls3">Run URL:</label>
				<input type="text" name="runUrls[]" placeholder="http://" class="input-xlarge"id="form-runUrls3">
			</fieldset>
		</div>
	</fieldset>

	<div class="form-actions">
		<input type="submit" value="Create job" class="btn btn-primary btn-large">
	</div>
</form>
HTML;

		return $formHtml;
	}
}
