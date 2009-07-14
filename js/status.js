jQuery(function(){
	setTimeout(function(){
		if ( jQuery("td.notdone").length ) {
			jQuery("table.results").load(window.location.href + " tbody");
			setTimeout(arguments.callee, 5000);
		}
	}, 5000);
	
	jQuery("td:has(a)").live("dblclick", function(){
		var params = /&.*$/.exec( jQuery(this).find("a").attr("href") );
		$.ajax({
			url: "/",
			type: "POST",
			data: "state=wiperun" + params
		});
		jQuery(this).empty().attr("class", "notstarted notdone");
	});
});
