<div class="userinfo">
<div class="browser you">
	<img src="/images/<?=$browser?>.png" class="browser-icon <?=$browser?>" alt="<?=$useragent_name?>" title="<?=$useragent_name?>"/>
	<span class="browser-name"><?=preg_replace('/\w+ /', "", $useragent_name)?></span>
</div> 

<h3><?=$username?></h3>
<p><strong>Status:</strong> <span id="msg">Loading...</span></p>
</div>

<div class="userinfo">
<h3>History</h3>
<ul id="history"></ul>
</div>

<div id="iframes"></div>
