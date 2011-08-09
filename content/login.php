<form action="" method="post">
	<fieldset>
		<legend>Login</legend>
		<?php echo $error; ?>
		<p>Login using your TestSwarm username and password. If you don't have one you may <a href="<?php echo swarmpath( "signup/" ); ?>">Signup Here</a>.</p>
		<label>Username: <input type="text" name="username"/></label><br/>
		<label>Password: <input type="password" name="password"/></label><br/>
		<input type="submit" value="Login"/>
	</fieldset>
</form>
