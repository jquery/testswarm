/**
 * JavaScript file for the "job" page in the browser.
 *
 * @since 0.1.0
 * @package TestSwarm
 */
jQuery(function ( $ ) {
	var updateInterval = SWARM.web.ajax_update_interval * 1000;

	setTimeout(function refreshTable() {
		if ( $( "td.status-new" ).length ) {
			$.get( window.location.href, function ( html ) {
				var tableHtml, $targetTable;

				tableHtml = $( html ).find( "table" ).html();
				$targetTable = $( "table.results" );
				if ( tableHtml !== $targetTable.html() ) {
					$targetTable.html( tableHtml );
				}
			});

			setTimeout( refreshTable, updateInterval );
		}
	}, updateInterval );

	$( document ).on( "dblclick", "td:has(a)", function () {
		var href, qs, $el;
		$el = $( this );
		href = $el.find( "a" ).attr( "href" );
		if ( href ) {
			// extract &run_id=..&client_id=.. from the "?action=runresults&run_id=6&client_id=252" url
			// basically transforming runresults into wiperun
			qs = href.match( /&.*$/ );
			$.ajax({
				url: SWARM.web.contextpath,
				dataType: "json",
				type: "POST",
				data: "action=wiperun" + ( qs ? qs[0] : "" ),
				success: function ( data ) {
					if ( data === "ok" ) {
						$el.empty().attr( "class", "notstarted notdone" );
					}
				}
			});
		}
	});
});
