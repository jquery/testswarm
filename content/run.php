<div class="userinfo">
	<div class="browser you">
		<img src="<?php echo swarmpath( "images/{$swarmBrowser->getBrowserCodename()}.sm.png" ); ?>" class="browser-icon <?php echo $swarmBrowser->getBrowserCodename(); ?>" alt="<?php echo $swarmBrowser->getSwarmUserAgentName(); ?>" title="<?php echo $swarmBrowser->getSwarmUserAgentName(); ?>"/>
		<span class="browser-name"><?php echo preg_replace('/\w+ /', "", $swarmBrowser->getSwarmUserAgentName()); ?></span>
	</div>

	<h3><?php echo $username; ?></h3>
	<p><strong>Status:</strong> <span id="msg">Loading...</span></p>
</div>

<div class="userinfo">
	<h3>History</h3>
	<ul id="history"></ul>
</div>

<div id="iframes"></div>
