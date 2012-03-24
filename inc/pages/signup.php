<form action="" method="post">
	<fieldset>
		<legend>Signup</legend>
		<?php echo $error; ?>
		<p>Signup to use TestSwarm. If you already have an account you may <a href="<?php echo swarmpath( "login/" ); ?>">Login Here</a>.</p>
		<label>Username: <input type="text" name="username"/></label><br/>
		<label>Password: <input type="password" name="password"/></label><br/>
		<label>Email: <input type="text" name="email"/></label><br/>
		<label>Request Auth Code:<br/>
		If you run an Open Source project and wish to submit jobs to be run on TestSwarm please provide a URL and description of the project below. Your auth code will be emailed to you, pending approval.<br/><textarea cols="40" rows="6" name="request"></textarea></label><br/>
		<input type="submit" value="Signup"/>
	</fieldset>
</form>
