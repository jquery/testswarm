<?php
        $run = ereg_replace("[^0-9]", "", $_POST['run']);
        $browser = ereg_replace("[^a-z]", "", $_POST['browser']);
        $version = ereg_replace("[^0-9.]", "", $_POST['version']);
        $results = $_POST['results'];

	if ( !empty($run) && !empty($browser) && !empty($version) && !empty($results) ) {
		#$f = fopen( "results/$run/$browser-$version.html", "w" );
		#fwrite( $f, $results );
		#fclose( $f );

		echo "done";
		exit();
	}
?>
<script src="jquery.js"></script>
<script src="run.js"></script>
