<div class="desc">
	<p>TestSwarm provides distributed continuous integration testing for JavaScript in over 35 browsers on 5 platforms. Donate your browsers to help run tests and improve the stability of your favorite Open Source projects.</p>
<!-- Your browser: <?=$browser?> <?=$version?> <?=$os?> -->
</div>

<?php
  $found = 0;

  loadBrowsers("xp");
  loadBrowsers("vista");
  loadBrowsers("osx10.4");
  loadBrowsers("osx10.5");
  loadBrowsers("osx");
  loadBrowsers("linux");
  loadBrowsers("2000");

function loadBrowsers($name) {
  global $found, $browser, $version, $os;

  $result = mysql_queryf("SELECT useragents.engine as engine, useragents.name as name, (SELECT COUNT(*) FROM clients WHERE useragent_id=useragents.id AND DATE_ADD(updated, INTERVAL 1 minute) > NOW()) as clients, (engine=%s AND %s REGEXP version AND os=%s) as found FROM useragents WHERE os=%s AND active=1 ORDER BY engine, name;", $browser, $version, $os, $name);

  $engine = "";

  if ( $name == "xp" ) {
    $name = "Windows XP";
  } else if ( $name == "vista" ) {
    $name = "Windows Vista";
  } else if ( $name == "2000" ) {
    $name = "Windows 2000";
  } else if ( $name == "osx10.4" ) {
    $name = "OS X 10.4";
  } else if ( $name == "osx10.5" ) {
    $name = "OS X 10.5";
  } else if ( $name == "osx" ) {
    $name = "OS X";
  } else if ( $name == "linux" ) {
    $name = "Linux";
  }

  echo "<div class='browsers'><h3>$name</h3>";

  while ( $row = mysql_fetch_array($result) ) {
    if ( $row[3] ) {
      $found = 1;
    }

    if ( $row[0] != $engine ) {
      echo "<br style='clear:both;'/>";
    }
    # <?= $row[0] != $engine ? ' clear' : ''?
    $num = preg_replace('/\w+ /', "", $row[1]);
    ?>
		<div class="browser<?= $row[0] != $engine ? ' clear' : ''?><?= $row[3] ? ' you' : ''?>">
			<img src="/images/<?=$row[0]?>.sm.png" class="browser-icon <?=$row[0]?>" alt="<?=$row[1]?>" title="<?=$row[1]?>"/>
			<span class="browser-name"><?=$num?></span>
			<?php if ( intval($row[2]) > 0 ) {
				echo "<span class='active'>" . $row[2] . "</span>";
			}?>
		</div>
  <?php $engine = $row[0];
	}

  echo "</div>";
}

if ( $found ) { ?>
<div class="join">
<p><strong>TestSwarm Needs Your Help!</strong> You have a browser that we need to test against, you should join the swarm to help us out.</p>
<?php if ( !$_SESSION['username'] ) { ?>
<form action="/" method="get">
	<input type="hidden" name="state" value="run"/>
	<br/><strong>Your Name:</strong><br/>
	<input type="text" name="user" value=""/><br/>
	<input type="submit" value="Join the Swarm"/>
</form>
<?php } else { ?>
<br/><p><strong>&raquo; <?=$_SESSION['username']?></strong> <a href="/run/">Start Running Tests</a></p>
<?php } ?>
<?php } else { ?>
<div class="join">
<p>TestSwarm doesn't need your help at this time. If you wish to help run tests you should load up one of the below browsers.</p>
</div>
<?php } ?>
