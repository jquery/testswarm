<blockquote>All users with a score greater than zero. The score is the number of tests run by that user's clients.</blockquote>
<table class="scores">
<?php
$result = mysql_queryf("SELECT users.name, SUM(total) as alltotal FROM clients, run_client, users WHERE clients.id=run_client.client_id AND clients.user_id=users.id GROUP BY user_id HAVING alltotal > 0 ORDER by alltotal DESC;");

$num = 1;

while ( $row = mysql_fetch_array($result) ) {
        $user = $row[0];
        $total = $row[1];

	echo '<tr><td class="num">' . $num. '</td><td><a href="' . swarmpath("user/$user/") . '">' . $user. '</a></td><td class="num">' .$total . '</td></tr>';
	$num++;
}
?>
</table>
