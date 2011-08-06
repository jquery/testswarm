<blockquote>All users with a score greater than zero. The score is the number of tests run by that user's clients.</blockquote>

<?php

	$result = mysql_queryf("SELECT name, score FROM scores ORDER by score DESC LIMIT " . $CLEAN["offset"] . ", " . $names_per_page . ";");
	
	function drawPages() {
		global $CLEAN;
		global $default_per_page;
		
		$pagi = windowed_offset($CLEAN['offset'], $default_per_page);

		$pages = pagination($CLEAN['start'], $CLEAN['end'], $pagi["offset"], $pagi["limit"], $default_per_page);
		echo "<div class='pagination'><ul>";
		foreach($pages as $page) {
			echo "<li class='" . $page['class'] . "'><a href='" . $page['href'] . "'>" . $page['textContent'] . "</a></li>";
		}
		echo "</ul></div>";
	}
	
	drawPages();

?>

<table class='scores'>
<?php

	$num = $CLEAN["offset"] + 1;

	while ( $row = mysql_fetch_array($result) ) {
		    $user = $row[0];
		    $total = $row[1];

		echo "<tr class='" . (($CLEAN["offset"] - $num) % 2 ? 'odd' : 'even') . "'><td class='rank num'>$num</td><td class='name'><a href='" . swarmpath("user/$user/") . "'>$user</a></td><td class='num total'>$total</td></tr>";
		$num++;
	}

?>
</table>

<?php
	drawPages();
?>
