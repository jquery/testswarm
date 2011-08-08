<blockquote>Welcome to the TestSwarm Alpha! Please be aware that TestSwarm is still under heavy testing and during this alpha period data may be lost or corrupted and clients may be unexpectedly disconnected. More information about TestSwarm can be found <a href="http://wiki.github.com/jquery/testswarm">on the TestSwarm wiki</a>.</blockquote>
<?php
  $found = 0;

  loadBrowsers("Desktop Browsers", 0);
  loadBrowsers("Mobile Browsers", 1);

if ( false ) {

	echo '<br style="clear: both;"><div class="scores"><h3>High Score Board</h3><table class="scores">';

	$result = mysql_queryf("SELECT users.name, SUM(total) as alltotal FROM clients, run_client, users WHERE clients.id=run_client.client_id AND clients.user_id=users.id  GROUP BY user_id ORDER by alltotal DESC LIMIT 10;");

	$num = 1;

	while ( $row = mysql_fetch_array($result) ) {
		$user = $row[0];
		$total = $row[1];

		echo '<tr><td class="num">' . $num . '</td><td><a href="' . swarmpath( "user/$user/" ) . '">' . $user . '</a></td><td class="num">' . $total . '</td></tr>';
		$num++;
	}

	echo '</table><p class="right"><a href="' . swarmpath( "scores/" ) . '">All Scores...</a></p><h3>Rarest Browsers</h3><table class="scores">';

	$result = mysql_queryf("SELECT name, SUM(runs) as allruns FROM run_useragent, useragents WHERE run_useragent.useragent_id=useragents.id GROUP BY name ORDER BY allruns LIMIT 10;");

	$num = 1;

	while ( $row = mysql_fetch_array($result) ) {
		$name = $row[0];

		echo "<tr><td class='num'>$num</td><td>$name</td></tr>";
		$num++;
	}

	echo '</table></div>';

}

function loadBrowsers($name, $mobile) {
  global $found, $browser, $version, $os;

  $result = mysql_queryf("SELECT useragents.engine as engine, useragents.name as name, (SELECT COUNT(*) FROM clients WHERE useragent_id=useragents.id AND updated > DATE_SUB(NOW(), INTERVAL 1 minute)) as clients, (engine=%s AND %s REGEXP version) as found FROM useragents WHERE active=1 AND mobile=%s ORDER BY engine, name;", $browser, $version, $mobile);

  $engine = "";

  echo "<div class='browsers'><h3>$name</h3>";

  while ( $row = mysql_fetch_array($result) ) {
    if ( $row[3] ) {
      $found = 1;
    }

    if ( $row[0] != $engine ) {
      echo '<br style="clear:both;"/>';
    }
    $num = preg_replace("/\w+ /", "", $row[1]);
    ?>
		<div class="browser<?php echo $row[0] != $engine ? " clear" : "";?><?php echo $row[3] ? " you" : "";?>">
			<img src="<?php echo swarmpath( "images/{$row[0]}.sm.png" ); ?>" class="browser-icon <?php echo $row[0]; ?>" alt="<?php echo $row[1]; ?>" title="<?php echo $row[1]; ?>"/>
			<span class="browser-name"><?php echo $num; ?></span>
			<?php if ( intval($row[2]) > 0 ) {
				echo '<span class="active">' . $row[2] . '</span>';
			}?>
		</div>
  <?php $engine = $row[0];
	}

  echo '</div>';
}

if ( $found ) { ?>
<div class="join">
	<p><strong>TestSwarm Needs Your Help!</strong> You have a browser that we need to test against, you should join the swarm to help us out.</p>
	<?php if ( !$_SESSION["username"] ) { ?>
	<form action="" method="get">
		<input type="hidden" name="state" value="run"/>
		<br/><strong>Username:</strong><br/>
		<input type="text" name="user" value=""/>
		<input type="submit" value="Join the Swarm"/>
	</form>
	<?php } else { ?>
	<br/><p><strong>&raquo; <?php echo $_SESSION["username"]; ?></strong> <a href="<?php echo swarmpath("run/{$_SESSION["username"]}/" ); ?>">Start Running Tests</a></p>
<?php } ?>
</div>
<?php } else { ?>
<div class="join">
	<p>TestSwarm doesn't need your help at this time. If you wish to help run tests you should load up one of the below browsers.</p>
	<p>If you feel that this may be a mistake, copy the following information (<?php echo $browser; ?> <?php echo $version; ?> <?php echo $os; ?>) and your <a href="http://useragentstring.com/">useragent string</a>, and post it to the <a href="http://groups.google.com/group/testswarm">discussion group</a>.</a>
</div>
<?php }
