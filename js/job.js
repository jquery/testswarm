/**
 * JavaScript file for the "job" page in the browser.
 *
 * @author John Resig, 2008-2011
 * @since 0.1.0
 * @package TestSwarm
 */
jQuery(function ( $ ) {
	var updateInterval = SWARM.conf.web.ajax_update_interval * 1000,
		$wipejobErr = $("#swarm-wipejob-error"),
		refreshTableTimout;

	function refreshTable() {
		if ( refreshTableTimout ) {
			clearTimeout( refreshTableTimout );
		}
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
	}

	refreshTableTimout = setTimeout( refreshTable, updateInterval );

	$( document ).on( "dblclick", "td:has(a)", function () {
		var href, qs, $el;
		$el = $( this );
		href = $el.find( "a" ).attr( "href" );
		if ( href ) {
			// extract &run_id=..&client_id=.. from the "?action=runresults&run_id=6&client_id=252" url
			// basically transforming runresults into wiperun
			qs = href.match( /&.*$/ );
			$.ajax({
				url: SWARM.conf.web.contextpath,
				type: "POST",
				data: "action=wiperun" + ( qs ? qs[0] : "" ),
				dataType: "json",
				success: function ( data ) {
					if ( data === "ok" ) {
						$el.empty().attr( "class", "notstarted notdone" );
					}
				}
			});
		}
	});

	function wipejobFail( data ) {
		$wipejobErr.hide().text( data.error && data.error.info || "Action failed." ).slideDown();
	}

	$( "#swarm-job-delete" ).click( function () {
		$wipejobErr.hide();
		$.ajax({
			url: SWARM.conf.web.contextpath + "api.php",
			type: "POST",
			data: {
				action: "wipejob",
				job_id: SWARM.jobInfo.id,
				type: "delete"
			},
			dataType: "json",
			success: function ( data ) {
				if ( data.wipejob && data.wipejob.result === "ok" ) {
					// Right now the only user authorized to delete a job is the creator,
					// the below code makes that assumption.
					window.location.href = SWARM.conf.web.contextpath + 'user/' + SWARM.session.username;
					return;
				}
				wipejobFail( data );
			},
			error: wipejobFail
		});
	} );

	$( "#swarm-job-reset" ).click( function () {
		$wipejobErr.hide();
		$.ajax({
			url: SWARM.conf.web.contextpath + "api.php",
			type: "POST",
			data: {
				action: "wipejob",
				job_id: SWARM.jobInfo.id,
				type: "reset"
			},
			dataType: "json",
			success: function ( data ) {
				if ( data.wipejob && data.wipejob.result === "ok" ) {
					refreshTable();
					return;
				}
				wipejobFail( data );
			},
			error: wipejobFail
		});
	} );

});
