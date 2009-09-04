<form action="/" method="POST">
<input type="hidden" name="client_id" value="<?php echo $client_id; ?>"/>
<input type="hidden" name="state" value="addjob"/>
<fieldset>
<legend>Job Information</legend>
<label>Job Name: <input type="text" name="job_name"/></label><br/>
<label>Your Username: <input type="text" name="user"/></label><br/>
<label>Your Auth Token: <input type="text" name="auth"/></label><br/>
<label>Number of Runs: <input type="text" name="max" value="1" size="2" maxlength="2"/></label><br/>
</fieldset>
<fieldset>
<legend>Browsers</legend>
<p>Choose the browsers in which your test suites will run (each browser will be run at least as many times as specified.</p>
<legend><input type="radio" name="browsers" value="all" /> <strong>All</strong> available browsers</legend><br/>
<legend><input type="radio" name="browsers" value="popular" checked="checked"/> The most <strong>popular</strong> browser (<a href="http://marketshare.hitslink.com/browser-market-share.aspx?qprid=2">99%+ of all browsers in use</a>)</legend><br/>
<legend><input type="radio" name="browsers" value="current" /> The <strong>current release</strong> of all the major browsers</legend><br/>
<legend><input type="radio" name="browsers" value="gbs" /> The browsers currently supported in <strong>Yahoo's <a href="http://developer.yahoo.com/yui/articles/gbs/">Graded Browser Support</a></strong></legend><br/>
<legend><input type="radio" name="browsers" value="beta" /> Upcoming <strong>alphas/betas</strong> of popular browsers</legend><br/>
<legend><input type="radio" name="browsers" value="popularbeta" /> <strong>Popular</strong> browsers and their upcoming <strong>alphas/betas</strong></legend><br/>
<!--<legend><input type="radio" name="browsers" value="mobile" /> Popular releases of <strong>mobile</strong> browsers</legend>-->
</fieldset>
<fieldset>
<legend>Test Suite</legend>
<p>URLs for test suites that'll be run for this job (all the test suites should probably have the same common code base or some other grouping characteristic.</p>
<p><b>Test Suite:</b><br/>
<legend>Name: <input type="text" name="suites[]"/></legend><br/>
<legend>URL: <input type="text" name="urls[]" value="http://" size="50"/></legend></p>
<p><b>Test Suite:</b><br/>
<legend>Name: <input type="text" name="suites[]"/></legend><br/>
<legend>URL: <input type="text" name="urls[]" value="http://" size="50"/></legend></p>
<p><b>Test Suite:</b><br/>
<legend>Name: <input type="text" name="suites[]"/></legend><br/>
<legend>URL: <input type="text" name="urls[]" value="http://" size="50"/></legend></p>
<p><b>Test Suite:</b><br/>
<legend>Name: <input type="text" name="suites[]"/></legend><br/>
<legend>URL: <input type="text" name="urls[]" value="http://" size="50"/></legend></p>
<p><b>Test Suite:</b><br/>
<legend>Name: <input type="text" name="suites[]"/></legend><br/>
<legend>URL: <input type="text" name="urls[]" value="http://" size="50"/></legend></p>
<p><b>Test Suite:</b><br/>
<legend>Name: <input type="text" name="suites[]"/></legend><br/>
<legend>URL: <input type="text" name="urls[]" value="http://" size="50"/></legend></p>
<p><b>Test Suite:</b><br/>
<legend>Name: <input type="text" name="suites[]"/></legend><br/>
<legend>URL: <input type="text" name="urls[]" value="http://" size="50"/></legend></p>
<p><b>Test Suite:</b><br/>
<legend>Name: <input type="text" name="suites[]"/></legend><br/>
<legend>URL: <input type="text" name="urls[]" value="http://" size="50"/></legend></p>
<p><b>Test Suite:</b><br/>
<legend>Name: <input type="text" name="suites[]"/></legend><br/>
<legend>URL: <input type="text" name="urls[]" value="http://" size="50"/></legend></p>
<p><b>Test Suite:</b><br/>
<legend>Name: <input type="text" name="suites[]"/></legend><br/>
<legend>URL: <input type="text" name="urls[]" value="http://" size="50"/></legend></p>
<p><b>Test Suite:</b><br/>
<legend>Name: <input type="text" name="suites[]"/></legend><br/>
<legend>URL: <input type="text" name="urls[]" value="http://" size="50"/></legend></p>
<p><b>Test Suite:</b><br/>
<legend>Name: <input type="text" name="suites[]"/></legend><br/>
<legend>URL: <input type="text" name="urls[]" value="http://" size="50"/></legend></p>
<p><b>Test Suite:</b><br/>
<legend>Name: <input type="text" name="suites[]"/></legend><br/>
<legend>URL: <input type="text" name="urls[]" value="http://" size="50"/></legend></p>
</fieldset>
<input type="submit" value="Create New Job"/>
</form>
