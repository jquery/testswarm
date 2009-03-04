function getBrowser(){
	return jQuery.browser.msie && "msie" ||
		jQuery.browser.safari && "webkit" ||
		jQuery.browser.opera && "opera" ||
		jQuery.browser.mozilla && "gecko";
}

var updateRate = 30;

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
				getTests();
			} else {
				jQuery("p.msg").text("Thanks, but we don't need to run tests for your browser.");
			}
		});
	}

	function getTests(){
		jQuery("p.msg").text("Querying for more tests...");

		jQuery.get("index.php", { state: "queue", browser: browser, version: version }, function(txt){
			queue = txt.split("\n");
			start();
		});
	}

	function handle(){
		if ( queue.length ) {
			jQuery("p.msg").text(queue.length + " more test(s) to run.");

			var item = queue.shift();
			if ( item ) {
				var iframe = document.createElement("iframe");
				iframe.src = "tests/" + item + "/test/";
				document.body.appendChild( iframe );

				window.done = function(data){
					jQuery.post( "/", {
						run: item,
						browser: browser,
						version: version,
						results: data.results,
						total: data.total,
						fail: data.fail
					}, function(res){
						document.body.removeChild( iframe );
						handle();
					});
				};
			}
		} else {
			jQuery("p.msg").text("No new tests to run.");

			var timeLeft = updateRate - 1;
			setTimeout(function leftTimer(){
				jQuery("p.msg").text("No new tests to run. Getting more in " + timeLeft + " seconds.");
				if ( timeLeft-- > 1 ) {
					setTimeout( leftTimer, 1000 );
				}
			}, 1000);
				
			setTimeout( getTests, updateRate * 1000 );
		}
	}

	function start(){
		handle();
	}

	load();
});
