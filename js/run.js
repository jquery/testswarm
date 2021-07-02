/**
 * JavaScript file for the "run" page in the browser.
 *
 * @author John Resig
 * @author Timo Tijhof
 * @since 0.1.0
 * @package TestSwarm
 */
/* eslint-env browser */
/* global jQuery, SWARM */
(function( $ ) {
	var currRunId, currRunUrl, testTimeout, pauseTimer, cmds, errorOut;

	function msg( htmlMsg ) {
		$( "#msg" ).html( htmlMsg );
	}

	function log( htmlMsg ) {
		$( "#history" ).prepend( "<li><strong>" +
			new Date().toString().replace( /^\w+ /, "" ).replace( /:[^:]+$/, "" ) +
			":</strong> " + htmlMsg + "</li>"
		);

		msg( htmlMsg );
	}

	/**
	 * Softly validate the SWARM object
	 */
	if ( !SWARM.client_id || !SWARM.conf ) {
		$( function() {
			msg( "Error: No client id configured! Aborting." );
		});
		return;
	}

	errorOut = 0;
	cmds = {
		reload: function() {
			location.reload();
		}
	};

	/**
	 * @param query String|Object: $.ajax "data" option, converted with $.param.
	 * @param retry Function
	 * @param ok Function
	 */
	function retrySend( query, retry, ok ) {
		function error( errMsg ) {
				if ( errorOut > SWARM.conf.client.saveRetryMax ) {
					cmds.reload();
				} else {
					errorOut += 1;
					errMsg = errMsg ? (" (" + errMsg + ")") : "";
					msg( "Error connecting to server" + errMsg + ", retrying..." );
					setTimeout( retry, SWARM.conf.client.saveRetrySleep * 1000 );
				}
		}

		$.ajax({
			type: "POST",
			url: SWARM.conf.web.contextpath + "api.php",
			timeout: SWARM.conf.client.saveReqTimeout * 1000,
			cache: false,
			data: query,
			dataType: "json",
			success: function( data ) {
				if ( !data || data.error ) {
					error( data.error.info );
				} else {
					errorOut = 0;
					ok.apply( this, arguments );
				}
			},
			error: function() {
				error();
			}
		});
	}

	function getTests() {
		if ( currRunId === undefined ) {
			log( "Connected to the swarm." );
		}

		currRunId = 0;
		currRunUrl = false;

		msg( "Querying for tests to run..." );
		retrySend( {
			action: "getrun",
			client_id: SWARM.client_id,
			run_token: SWARM.run_token
		}, getTests, runTests );
	}

	function cancelTest() {
		if ( testTimeout ) {
			clearTimeout( testTimeout );
			testTimeout = 0;
		}

		$( "iframe" ).remove();
	}

	function testAborted( runInfo, reportHtml ) {
		cancelTest();
		retrySend(
			{
				action: "saverun",
				fail: 0,
				error: 0,
				total: 0,
				status: 3, // ResultAction::STATE_ABORTED
				report_html: reportHtml || "Test Timed Out.",
				run_id: currRunId,
				client_id: SWARM.client_id,
				run_token: SWARM.run_token,
				results_id: runInfo.resultsId,
				results_store_token: runInfo.resultsStoreToken
			},
			function() {
				testAborted( runInfo, reportHtml );
			},
			function( data ) {
				if ( data.saverun === "ok" ) {
					SWARM.runDone();
				} else {
					getTests();
				}
			}
		);
	}

	/**
	 * @param data Object: Reponse from api.php?action=getrun
	 */
	function runTests( data ) {
		var norun_msg, timeLeft, runInfo, iframe, xhr;

		if ( !$.isPlainObject( data ) || data.error ) {
			// Handle session timeout, where server sends back "Username required."
			// Handle TestSwarm reset, where server sends back "Client doesn't exist."
			if ( data.error ) {
				$(function() {
					msg( "action=getrun failed. " + $( "<div>" ).text( data.error.info ).html() );
				});
				return;
			}
		}

		if ( data.getrun ) {

			// Handle actual retreived tests from runInfo
			runInfo = data.getrun.runInfo;
			if ( runInfo ) {
				currRunId = runInfo.id;
				currRunUrl = runInfo.url;

				log( "Running " + ( runInfo.desc || "" ) + " tests..." );

				iframe = document.createElement( "iframe" );
				iframe.width = 1000;
				iframe.height = 600;
				iframe.className = "test-runner-frame";
				iframe.src = currRunUrl + (currRunUrl.indexOf( "?" ) > -1 ? "&" : "?") + $.param({
					// Cache buster
					"_" : new Date().getTime(),
					// Homing signal for inject.js so that it can find its target for action=saverun
					"swarmURL" : location.protocol + "//" + location.host + SWARM.conf.web.contextpath +
						"index.php?" +
						$.param({
							status: 2, // ResultAction::STATE_FINISHED
							run_id: currRunId,
							client_id: SWARM.client_id,
							run_token: SWARM.run_token,
							results_id: runInfo.resultsId,
							results_store_token: runInfo.resultsStoreToken
						})
				});

				$( "#iframes" ).append( iframe );

				// Timeout after a period of time
				testTimeout = setTimeout( function() {
					testAborted( runInfo );
				}, SWARM.conf.client.runTimeout * 1000 );

				// There is sometimes a backlog of old re-runs that have already been garbage
				// collected on the build server and thus respond with 404 Not Found.
				// These can take a very long time to process as through <iframe>, even on the
				// same origin, there is no reliable way to determine whether the src url
				// returned 404 Not Found, and the only other exit path we have is the timeout
				// (which is generally 5-10 minutes).
				// Try to short-circuit this by sending an XHR, and aborting the test immediately
				// if we find a network error or HTTP error status.
				if ( typeof XMLHttpRequest !== "undefined" ) {
					xhr = new XMLHttpRequest();
					xhr.open( "GET", currRunUrl );
					xhr.onerror = function() {
						if ( testTimeout ) {
							testAborted( runInfo, "TestSwarm Error: Failed to load the run URL (XHR Network Error)." );
						}
					};
					xhr.onload = function() {
						if ( testTimeout && xhr.status >= 400 && xhr.status <= 599 ) {
							testAborted( runInfo, "TestSwarm Error: The run URL responsed with an error (HTTP " + xhr.status + ")." );
						}
					};
					xhr.send();
				}

				return;
			}

		}

		// If we're still here then either there are no new tests to run, or this is a call
		// triggerd by an iframe to continue the loop. We'll do so a short timeout,
		// optionally replacing the message by data.timeoutMsg
		clearTimeout( pauseTimer );

		norun_msg = data.timeoutMsg || "No new tests to run.";

		msg( norun_msg );

		// If we just completed a run, do a cooldown before we fetch the next run (if there is one).
		// If we just completed a cooldown a no runs where available, nonewruns_sleep instead.
		timeLeft = currRunUrl ? SWARM.conf.client.cooldownSleep : SWARM.conf.client.nonewrunsSleep;

		pauseTimer = setTimeout(function leftTimer() {
			msg(norun_msg + " Getting more in " + timeLeft + " seconds." );
			if ( timeLeft >= 1 ) {
				timeLeft -= 1;
				pauseTimer = setTimeout( leftTimer, 1000 );
			} else {
				timeLeft -= 1;
				getTests();
			}
		}, 1000);

	}

	// Needs to be a publicly exposed function,
	// so that when inject.js does a <form> submission,
	// it can call this from within the frame
	// as window.parent.SWARM.runDone();
	SWARM.runDone = function() {
		cancelTest();
		runTests({ timeoutMsg: "Cooling down." });
	};

	function handleMessage(e) {
		e = e || window.event;
		retrySend( e.data, function() {
			handleMessage(e);
		}, SWARM.runDone );
	}

	function confUpdate() {
		$.ajax({
			type: "POST",
			url: SWARM.conf.web.contextpath + "api.php",
			timeout: SWARM.conf.client.saveReqTimeout * 1000,
			cache: false,
			data: {
				action: "ping",
				client_id: SWARM.client_id,
				run_token: SWARM.run_token
			},
			dataType: "json"
		}).done( function( data ) {
			// Handle configuration update
			if ( data.ping && data.ping.confUpdate ) {
				// Refresh control
				if ( SWARM.conf.client.refreshControl < data.ping.confUpdate.client.refreshControl ) {
					cmds.reload();
					return;
				}

				$.extend( SWARM.conf, data.ping.confUpdate );
			}
		}).always( function() {
			setTimeout( confUpdate, SWARM.conf.client.pingTime * 1000 );
		});
	}


	/**
	 * Bind
	 */
	if ( window.addEventListener ) {
		window.addEventListener( "message", handleMessage, false );
	} else if ( window.attachEvent ) {
		window.attachEvent( "onmessage", handleMessage );
	}

	$( function() {
		getTests();
		confUpdate();
	});

}( jQuery ) );
