<?php
	$title = "Logged out";

	$_SESSION["username"] = "";
	$_SESSION["auth"] = "";

	session_write_close();
