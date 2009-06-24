(function(){

	var DEBUG = false;

	var url = window.location.search;
	url = decodeURIComponent( url.slice( url.indexOf("swarmURL=") + 9 ) );

	if ( !DEBUG && (!url || url.indexOf("http") !== 0) ) {
		return;
	}

	if ( typeof QUnit !== "undefined" ) {
		QUnit.done = function(fail, total){
			// Clean up the HTML (remove any un-needed scripts and test markup
			// Make sure that a styleshet is still being referenced
			var html = jQuery("html")
				.find("#nothiddendiv, #loadediframe, #dl, #main, script, div.testrunner-toolbar").remove().end()
				.find("ol").show().end()
				.find("link").each(function(){
					jQuery(this).attr("href", this.href);
				})
				.html().replace(/\s+/g, " ");

			submit({
				fail: fail,
				total: total,
				results: "<html>" + html + "</html>"
			});
		};
	}

	function submit(params){
		var paramItems = (url.split("?")[1] || "").split("&");

		for ( var i = 0; i < paramItems.length; i++ ) {
			if ( paramItems[i] ) {
				var parts = paramItems[i].split("=");
				params[ parts[0] ] = parts[1];
			}
		}

		var form = document.createElement("form");
		form.action = url;
		form.method = "POST";

		for ( var i in params ) {
			var input = document.createElement("input");
			input.type = "hidden";
			input.name = i;
			input.value = params[i];
			form.appendChild( input );
		}

		if ( DEBUG ) {
			alert( form.innerHTML );
		} else {
			document.body.appendChild( form );
			form.submit();
		}
	}

})();
