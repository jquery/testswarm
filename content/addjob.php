<form action="/" method="POST">
<input type="hidden" name="client_id" value="<?=$client_id?>"/>
<input type="hidden" name="state" value="addjob"/>
<fieldset>
<legend>Job Information</legend>
<label>Job Name: <input type="text" name="job_name"/></label><br/>
<label>Your Username: <input type="text" name="user"/></label><br/>
<label>Number of Runs: <input type="text" name="max" value="1" size="2" maxlength="2"/></label><br/>
</fieldset>
<fieldset>
<legend>Browsers</legend>
<p>Choose the browsers in which your test suites will run (each browser will be run at least as many times as specified.</p>
<?php
	$result = mysql_queryf("SELECT useragents.id as id, useragents.name as name, (SELECT COUNT(*) FROM clients WHERE useragent_id=useragents.id AND DATE_ADD(updated, INTERVAL 2 minute) > NOW()) as clients FROM useragents ORDER BY name;");

	while ( $row = mysql_fetch_array($result) ) {?>
		<legend><input type="checkbox" name="browsers[]" value="<?=$row[0]?>" checked="checked"/> <?=$row[1]?><?php if ( intval($row[2]) > 0 ) {
			echo " (" . $row[2] . " Connected)";
		}?></legend><br/>
	<?php }
?>
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
