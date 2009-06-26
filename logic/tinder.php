<?php

	$search_user = ereg_replace("[^a-zA-Z0-9]", "", $_REQUEST['user']);

	$title = "$search_user";

?>
