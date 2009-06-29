<?php
	$_SESSION['username'] = "";
	$_SESSION['auth'] = "";

	session_write_close();
	header("Location: /");
	exit(); 
?>
