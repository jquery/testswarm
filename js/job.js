/**
 * JavaScript file for the "job" page in the browser.
 *
 * @author John Resig
 * @author Timo Tijhof
 * @since 0.1.0
 * @package TestSwarm
 */
jQuery(function( $ ) {
	var updateInterval = SWARM.conf.web.ajaxUpdateInterval * 1000,
		$wipejobErr = $( ".swarm-wipejob-error" ),
		$targetTable = $( "table.swarm-results" ),
		refreshTableTimout, indicatorText, $indicator;

	indicatorText = document.createTextNode( "updating" );
	$indicator = $( "<span class='btn pull-right disabled'> <i class='icon-refresh'></i></span>" )
		.prepend( indicatorText )
		.css( "opacity", 0 );

	function indicateAction( label ) {
		// Make sure any scheduled action is dequeued, we"re doing something now.
		if ( refreshTableTimout ) {
			clearTimeout( refreshTableTimout );
		}
		// $.text() is a getter
		// $.fn.text() does empty/append, which means the reference is no meaningless
		indicatorText.nodeValue = label;
		$indicator.stop( true, true ).css( "opacity", 1 );
	}

	function actionComplete() {
		setTimeout( function() {
			$indicator.stop(true, true).animate({
				opacity: 0
			});
		}, 10 );
	}

	function refreshTable() {
		indicateAction( "updating" );

		jQuery.ajax({
			type: "GET",
			url: location.href,
			headers: {
				"X-Swarm-Partial": "1"
			}
		})
			.done( function( html ) {
				var tableHtml;

				tableHtml = $( $.parseHTML( html ) ).filter( "table.swarm-results" ).html();
				if ( tableHtml !== $targetTable.html() ) {
					$targetTable.html( tableHtml );
				}
			})
			.complete( function() {
				// Whether done or failed: Clean up and schedule next update
				actionComplete();
				refreshTableTimout = setTimeout( refreshTable, updateInterval );
			});
	}

	// Schedule first update
	refreshTableTimout = setTimeout( refreshTable, updateInterval );

	function wipejobFail( data ) {
		$wipejobErr.hide().text( data.error && data.error.info || "Action failed." ).slideDown();
	}

	function resetRun( $el ) {
		if ( $el.data( "runStatus" ) !== "new" ) {
			$.ajax({
				url: SWARM.conf.web.contextpath + "api.php",
				type: "POST",
				data: {
					action: "wiperun",
					job_id: $el.data( "jobId" ),
					run_id: $el.data( "runId" ),
					client_id: $el.data( "clientId" ),
					useragent_id: $el.data( "useragentId" ),
					authID: SWARM.auth.project.id,
					authToken: SWARM.auth.sessionToken
				},
				dataType: "json",
				success: function( data ) {
					if ( data.wiperun && data.wiperun.result === "ok" ) {
						$el.empty().attr( "class", "swarm-status swarm-status-new" );
						refreshTable();
					}
				}
			});
		}
	}

	$targetTable.before( $indicator );

	if ( SWARM.auth ) {

		// This needs to be bound as a delegate, because the table auto-refreshes.
		$targetTable
			.on( "click", ".swarm-reset-run-single", function() {
				resetRun( $( this ).closest( "td" ) );
			})
			.removeClass( "swarm-results-unbound-auth" );

		$( ".swarm-reset-runs-failed" ).on( "click", function() {
			var $els = $( "td[data-run-status='failed'], td[data-run-status='error'], td[data-run-status='timedout']" );
			if ( !$els.length || !window.confirm( "Are you sure you want to reset all failed runs?" ) ) {
				return;
			}
			$els.each( function() {
				resetRun( $( this ) );
			});
		});
		$( ".swarm-delete-job" ).click( function() {
			if ( !window.confirm( "Are you sure you want to delete this job?" ) ) {
				return;
			}
			$wipejobErr.hide();
			indicateAction( "deleting" );

			$.ajax({
				url: SWARM.conf.web.contextpath + "api.php",
				type: "POST",
				data: {
					action: "wipejob",
					job_id: SWARM.jobInfo.id,
					type: "delete",
					authID: SWARM.auth.project.id,
					authToken: SWARM.auth.sessionToken
				},
				dataType: "json",
				success: function( data ) {
					if ( data.wipejob && data.wipejob.result === "ok" ) {
						// Right now the only user authorized to delete a job is the creator,
						// the below code makes that assumption.
						location.href = SWARM.conf.web.contextpath + "project/" + SWARM.auth.project.id;
						return;
					}
					actionComplete();
					wipejobFail( data );
				},
				error: function( error ) {
					actionComplete();
					wipejobFail( error );
				}
			});
		});

		$( ".swarm-reset-runs" ).click( function() {
			if ( !window.confirm( "Are you sure you want to reset this job?" ) ) {
				return;
			}
			$wipejobErr.hide();
			indicateAction( "resetting" );

			$.ajax({
				url: SWARM.conf.web.contextpath + "api.php",
				type: "POST",
				data: {
					action: "wipejob",
					job_id: SWARM.jobInfo.id,
					type: "reset",
					authID: SWARM.auth.project.id,
					authToken: SWARM.auth.sessionToken
				},
				dataType: "json",
				success: function( data ) {
					actionComplete();
					if ( data.wipejob && data.wipejob.result === "ok" ) {
						refreshTable();
						return;
					}
					wipejobFail( data );
				},
				error: function( error ) {
					actionComplete();
					wipejobFail( error );
				}
			});
		});

	}

});
