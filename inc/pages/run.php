<?php
$bi = $swarmContext->getBrowserInfo();
?>
<div class="userinfo">
	<div class="browser you">
		<img src="<?php echo swarmpath( "images/{$bi->getBrowserCodename()}.sm.png" ); ?>" class="browser-icon <?php echo $bi->getBrowserCodename(); ?>" alt="<?php echo $bi->getSwarmUserAgentName(); ?>" title="<?php echo $bi->getSwarmUserAgentName(); ?>"/>
		<span class="browser-name"><?php echo preg_replace('/\w+ /', "", $bi->getSwarmUserAgentName()); ?></span>
	</div>

	<h3><?php echo $username; ?></h3>
	<p><strong>Status:</strong> <span id="msg">Loading...</span></p>
</div>

<div class="userinfo">
	<h3>History</h3>
	<ul id="history"></ul>
</div>

<div id="iframes"></div>
