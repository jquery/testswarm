QUnit.done = function(fail, total){
	// Clean up the HTML (remove any un-needed scripts and test markup
	// Make sure that a styleshet is still being referenced
	var html = jQuery("html")
		.find("#nothiddendiv, #loadediframe, #dl, #main, script, div.testrunner-toolbar").remove().end()
		.find("ol").show().end()
		.find("link").attr("href", "/files/qunit/testsuite.css").end()
		.html().replace(/\s+/g, " ");

	// Send the results back to the top frame
	if ( typeof window.top.done === "function" ) {
		window.top.done({
			run: item,
			fail: fail,
			total: total,
			results: "<html>" + html + "</html>"
		});
	}
};
