function getBrowser(){
	return jQuery.browser.msie && "msie" ||
		jQuery.browser.safari && "webkit" ||
		jQuery.browser.opera && "opera" ||
		jQuery.browser.mozilla && "gecko";
}

jQuery(function(){
	var queue = [], browsers, browser = getBrowser(), version = jQuery.browser.version, name = "";

	function load(){
		jQuery.get("/browsers.txt", function(txt){
			browsers = txt.split("\n");
			for ( var i = 0; i < browsers.length; i++ ) {
				browsers[i] = browsers[i].split(", ");
				if ( browsers[i][0] == browser && version.indexOf( browsers[i][1] ) == 0 ) {
					version = browsers[i][1];
					name = browsers[i][2];
					break;
				}
			}

			if ( name ) {
				jQuery.get("/?run=yes", { browser: browser, version: version }, function(txt){
					queue = txt.split("\n");
					start();
				});
			} else {
				// TODO: Let them know that their help isn't needed
			}
		});
	}

	function handle(){
		if ( queue.length ) {
			var item = queue.shift();
			if ( item ) {
				var iframe = document.createElement("iframe");
				iframe.src = item + "test/";
				iframe.onload = function(){
					var interval = setInterval(function(){
						var html = iframe.contentDocument.documentElement.innerHTML;

						if ( html.indexOf("Tests completed") > -1 ) {
							jQuery("#nothiddendiv, #loadediframe, #dl, #main, script", iframe.contentDocument).remove();
							jQuery("ol", iframe.contentDocument).show();

							html = "<html>" + iframe.contentDocument.documentElement.innerHTML + "</html>";

							jQuery.post( "", {
								ticket: item.split("/")[2],
								patch: item.split("/")[3],
								browser: browser,
								version: version,
								results: html
							}, function(res){
								document.body.removeChild( iframe );
								handle();
							});

							clearInterval( interval );
						}
					}, 100);
				};
				document.body.appendChild( iframe );
			}
		} else {
			// TODO: All done! (Query for new tests)
		}
	}

	function start(){
		handle();
	}

	load();
});
