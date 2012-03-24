<blockquote>Welcome to the TestSwarm Alpha! Please be aware that TestSwarm is still under heavy testing and during this alpha period data may be lost or corrupted and clients may be unexpectedly disconnected. More information about TestSwarm can be found <a href="//github.com/jquery/testswarm/wiki">on the TestSwarm wiki</a>.</blockquote>
<?php
	$foundDesktop = loadBrowsers( "Desktop Browsers", /*mobile=*/0 );
	$foundMobile = loadBrowsers( "Mobile Browsers", /*mobile=*/1 );

	$found = $foundDesktop || $foundMobile;

if ( false ) {

	echo '<br style="clear: both;"/><div class="scores"><h3>High Score Board</h3><table class="scores">';

	$result = mysql_queryf(
		"SELECT
			users.name,
			SUM(total) as alltotal
		FROM
			clients, run_client, users
		WHERE	clients.id = run_client.client_id
		AND	clients.user_id = users.id
		GROUP BY user_id
		ORDER by alltotal DESC
		LIMIT 10;"
	);

	$num = 1;

	while ( $row = mysql_fetch_array( $result ) ) {
		$user = $row[0];
		$total = $row[1];

		echo '<tr><td class="num">' . $num . '</td><td><a href="' . swarmpath( "user/$user/" ) . '">' . $user . '</a></td><td class="num">' . $total . '</td></tr>';
		$num++;
	}

	echo '</table><p class="right"><a href="' . swarmpath( "scores/" ) . '">All Scores...</a></p><h3>Rarest Browsers</h3><table class="scores">';

	$result = mysql_queryf(
		"SELECT
			name,
			SUM(runs) as allruns
		FROM
			run_useragent, useragents
		WHERE	run_useragent.useragent_id = useragents.id
		GROUP BY name
		ORDER BY allruns
		LIMIT 10;"
	);

	$num = 1;

	while ( $row = mysql_fetch_array($result) ) {
		$name = $row[0];

		echo "<tr><td class='num'>$num</td><td>$name</td></tr>";
		$num++;
	}

	echo '</table></div>';

}

/** @return bool: Whether the current user was found in the swarm */
function loadBrowsers($headingTitle, $mobile) {
	global $swarmContext;
	$bi = $swarmContext->getBrowserInfo();
	$db = $swarmContext->getDB();

	$foundSelf = false;

	$rows = $db->getRows(str_queryf(
		"SELECT
			useragents.engine as engine,
			useragents.name as name,
			(
				SELECT COUNT(*)
				FROM clients
				WHERE	clients.useragent_id = useragents.id
				AND clients.updated > %u
			) as clients,
			(engine=%s AND %s REGEXP version) as found
		FROM
			useragents
		WHERE	active = 1
		AND	mobile = %s
		ORDER BY engine, name;",
		swarmdb_dateformat( strtotime( '1 minute ago' ) ),
		$bi->getBrowserCodename(),
		$bi->getBrowserVersion(),
		$mobile
	));

	$engine = "";

	echo "<div class=\"browsers\"><h3>$headingTitle</h3>";

	foreach ( $rows as $row ) {
		if ( $row->found ) {
			$foundSelf = true;
		}

		if ( $row->engine != $engine ) {
			echo '<br style="clear: both;"/>';
		}
		$namePart = preg_replace( "/\w+ /", "", $row->name );
		?>
		<div class="browser<?php echo $row->engine != $engine ? " clear" : "";?><?php echo $row->found ? " you" : "";?>">
			<img src="<?php echo swarmpath( "images/{$row->engine}.sm.png" ); ?>" class="browser-icon <?php echo $row->engine; ?>" alt="<?php echo $row->name; ?>" title="<?php echo $row->name; ?>"/>
			<span class="browser-name"><?php echo $namePart; ?></span>
			<?php if ( intval( $row->clients ) > 0 ) {
				echo '<span class="active">' . $row->clients . '</span>';
			}?>
		</div>
		<?php $engine = $row->engine;
	}

	echo '</div>';

	return $foundSelf;
}

$request = $swarmContext->getRequest();
$bi = $swarmContext->getBrowserInfo();

if ( $found ) { ?>
<div class="join">
	<p><strong>TestSwarm Needs Your Help!</strong> You have a browser that we need to test against, you should join the swarm to help us out.</p>
	<?php if ( !$request->getSessionData( "username" ) ) { ?>
	<form action="" method="get">
		<input type="hidden" name="action" value="run"/>
		<br/><strong>Username:</strong><br/>
		<input type="text" name="item" value=""/>
		<input type="submit" value="Join the Swarm"/>
	</form>
	<?php } else { ?>
	<br/><p><strong>&raquo; <?php echo $request->getSessionData( "username" ); ?></strong> <a href="<?php echo swarmpath("run/{$request->getSessionData( "username" )}/" ); ?>">Start Running Tests</a></p>
<?php } ?>
</div>
<?php } else { ?>
<div class="join">
	<p>TestSwarm doesn't need your help at this time. If you wish to help run tests you should load up one of the below browsers.</p>
	<p>If you feel that this may be a mistake, copy the following information (<?php echo $bi->getBrowserCodename(); ?> <?php echo $bi->getBrowserVersion(); ?> <?php echo $bi->getOsCodename(); ?>) and your <a href="http://useragentstring.com/">useragent string</a>, and post it to the <a href="//groups.google.com/group/testswarm">discussion group</a>.</a>
</div>
<?php }
