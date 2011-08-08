<div class="userinfo">
	<div class="browser you">
		<img src="<?php echo swarmpath( "images/{$browser}.sm.png" ); ?>" class="browser-icon <?php echo $browser; ?>" alt="<?php echo $useragent_name; ?>" title="<?php echo $useragent_name; ?>"/>
		<span class="browser-name"><?php echo preg_replace('/\w+ /', "", $useragent_name); ?></span>
	</div>

	<h3><?php echo $username; ?></h3>
	<p><strong>Status:</strong> <span id="msg">Loading...</span></p>
</div>

<div class="userinfo">
	<h3>History</h3>
	<ul id="history"></ul>
</div>

<div id="iframes"></div>
