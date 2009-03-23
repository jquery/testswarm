(function(){

	var url = window.location.search;
	url = decodeURIComponent( url.slice( url.indexOf("swarmURL=") + 9 ) );

	if ( !url || url.indexOf("http") !== 0 ) {
		return;
	}

	if ( typeof QUnit !== "undefined" ) {
		QUnit.done = function(fail, total){
			// Clean up the HTML (remove any un-needed scripts and test markup
			// Make sure that a styleshet is still being referenced
			var html = jQuery("html")
				.find("#nothiddendiv, #loadediframe, #dl, #main, script, div.testrunner-toolbar").remove().end()
				.find("ol").show().end()
				.find("link").attr("href", "/files/qunit/testsuite.css").end()
				.html().replace(/\s+/g, " ");

			submit( fail, total );
		};
	}

	function submit(fail, total){
		var results = document.documentElement.outerHTML ||
			"<html>" + document.documentElement.innerHTML + "</html>";

		results = results.replace(/\s+/g, " ").replace("'", "\\'");

		var form = document.createElement("form");
		form.action = url;
		form.method = "POST";
		var innerHTML = "<input type='hidden' name='fail' value='" + fail + "'/>" + 
			"<input type='hidden' name='total' value='" + total + "'/>" + 
			"<input type='hidden' name='results' value='" + results + "'/>";

		var paramItems = url.split("?")[1].split("&"), params = {};

		for ( var i = 0; i < paramItems.length; i++ ) {
			var parts = paramItems[i].split("=");
			innerHTML = "<input type='hidden' name='" + parts[0] + "' value='" + parts[1] + "'/>" + innerHTML;
		}

		form.innerHTML = innerHTML;

		document.body.appendChild( form );
		form.submit();
	}
})();
