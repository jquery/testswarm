<p>Your browser engine: <strong><?=$browser?> <?=$version?></strong></p>

<form action="/" method="get">
	<input type="hidden" name="state" value="run"/>
	Your Username: <input type="text" name="user" value=""/>
	<input type="submit" value="Join the Swarm"/>
</form>

<h3>Cloud Status:</h3>

<?php
  $result = mysql_queryf("SELECT useragents.engine as engine, useragents.name as name, (SELECT COUNT(*) FROM clients WHERE useragent_id=useragents.id AND DATE_ADD(updated, INTERVAL 1 minute) > NOW()) as clients FROM useragents ORDER BY name;");

	$engine = "";

  while ( $row = mysql_fetch_array($result) ) {?>
		<div class="browser<?= $row[0] != $engine ? ' clear' : ''?>">
			<img src="/icons/<?=$row[0]?>.png" class="browser-icon <?=$row[0]?>"/>
			<span class="browser-name"><?=$row[1]?></span>
			<?php if ( intval($row[2]) > 0 ) {
				echo "<span class='active'>" . $row[2] . "</span>";
			}?>
		</div>
  <?php $engine = $row[0];
	}
?>
