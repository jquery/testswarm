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
				jQuery.get("/", { state: "queue", browser: browser, version: version }, function(txt){
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
				iframe.src = "tests/" + item + "/test/";
				document.body.appendChild( iframe );

				window.done = function(data){
					jQuery.post( "/", {
						run: item,
						browser: data.browser,
						version: data.version,
						results: data.html
					}, function(res){
						document.body.removeChild( iframe );
						handle();
					});
				};
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
