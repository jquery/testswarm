jQuery(function(){
	setTimeout(function(){
		if ( jQuery("td.notdone").length ) {
			jQuery.get(window.location.href, function(html){
				var table = jQuery("table", html).html(), insert = jQuery("table.results");
				if ( table !== insert.html() ) {
					insert.html( table );
				}
			});
			setTimeout(arguments.callee, 5000);
		}
	}, 5000);

	jQuery("td:has(a)").live("dblclick", function(){
		var params = /&.*$/.exec( jQuery(this).find("a").attr("href") );
		jQuery.ajax({
			url: ".",
			type: "POST",
			data: "state=wiperun" + params
		});
		jQuery(this).empty().attr("class", "notstarted notdone");
	});
});
