<h3>Cloud Status:</h3>

<?php
  $found = 0;
  $result = mysql_queryf("SELECT useragents.engine as engine, useragents.name as name, (SELECT COUNT(*) FROM clients WHERE useragent_id=useragents.id AND DATE_ADD(updated, INTERVAL 1 minute) > NOW()) as clients, (engine=%s AND %s REGEXP version) as found FROM useragents ORDER BY name;", $browser, $version);

	$engine = "";

  while ( $row = mysql_fetch_array($result) ) {
    if ( $row[3] ) {
      $found = 1;
    }
    ?>
		<div class="browser<?= $row[0] != $engine ? ' clear' : ''?><?= $row[3] ? ' you' : ''?>">
			<img src="/images/<?=$row[0]?>.png" class="browser-icon <?=$row[0]?>"/>
			<span class="browser-name"><?=$row[1]?></span>
			<?php if ( intval($row[2]) > 0 ) {
				echo "<span class='active'>" . $row[2] . "</span>";
			}?>
		</div>
  <?php $engine = $row[0];
	}

if ( $found ) { ?>
<div class="join">
<p><strong>TestSwarm Needs Your Help!</strong> You have a browser that we need to test against, you should join the swarm to help us out.</p>
<form action="/" method="get">
	<input type="hidden" name="state" value="run"/>
	Your Username: <input type="text" name="user" value=""/>
	<input type="submit" value="Join the Swarm"/>
</form>
<?php } else { ?>
<div class="join">
<p>TestSwarm doesn't need your help at this time. If you wish to help run tests you should load up one of the below browsers.</p>
</div>
<?php } ?>
