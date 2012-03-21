(function ( $, SWARM, undefined ) {
	var currRunId, currRunUrl, testTimeout, pauseTimer, cmds, errorOut;

	function msg( txt ) {
		$( "#msg" ).html( txt );
	}

	function log( txt ) {
		$( "#history" ).prepend( "<li><strong>" +
			new Date().toString().replace( /^\w+ /, "" ).replace( /:[^:]+$/, "" ) +
			":</strong> " + txt + "</li>" );
		msg( txt );
	}

	/**
	 * Softly validate the SWARM object
	 */
	if ( !SWARM.client_id || !SWARM.client ) {
		$( function() {
			msg( "Error: No client id configured! Aborting." );
		});
		return;
	}

	errorOut = 0;
	cmds = {
		reload: function () {
			window.location.reload();
		}
	};

	function retrySend( query, retry, ok ) {
		$.ajax({
			type: "POST",
			url: SWARM.web.contextpath,
			timeout: 10000,
			cache: false,
			data: query,
			dataType: "json",
			success: function () {
				errorOut = 0;
				ok.apply( this, arguments );
			},
			error: function () {
				if ( errorOut > 4 ) {
					cmds.reload();
				} else {
					errorOut += 1;
					msg( "Error connecting to server, retrying..." );
					setTimeout( retry, 15000 );
				}
			}
		});
	}

	function getTests() {
		if ( currRunId === undefined ) {
			log( "Connected to the swarm." );
		}

		currRunId = 0;
		currRunUrl = false;

		msg( "Querying for more tests to run..." );
		retrySend( "state=getrun&client_id=" + SWARM.client_id, getTests, runTests );
	}

	function cancelTest() {
		if ( testTimeout ) {
			clearTimeout( testTimeout );
			testTimeout = 0;
		}

		$( "iframe" ).remove();
	}

	function testTimedout() {
		cancelTest();
		retrySend( "state=saverun&fail=-1&total=-1&results=Test%20Timed%20Out.&run_id="
			+ currRunId + "&client_id=" + SWARM.client_id,
			testTimedout, getTests );
	}

	/**
	 * @param data Object Data returned by state=getrun
	 */
	function runTests( data ) {
		var norun_msg, timeLeft, runInfo, params, iframe;

		if ( !$.isPlainObject( data ) ) {
			// Handle session timeout, where server sends back "Username required. ?user=USERNAME."
			// Handle TestSwarm reset, where server sends back "Client doesn't exist."
			if ( /^Username required|^Client doesn/.test( data ) ) {
				cmds.reload();
				return;
			}
		}

		// Handle configuration update
		if ( data.swarmUpdate ) {

			// Refresh control
			if ( SWARM.client.refresh_control < data.swarmUpdate.client.refresh_control ) {
				cmds.reload();
				return;
			}

			$.extend( SWARM, data.swarmUpdate );
		}

		// Handle run
		runInfo = data.runInfo;
		if ( runInfo ) {
			currRunId = runInfo.id;
			currRunUrl = runInfo.url;

			log( "Running " + ( runInfo.desc || "" ) + " tests..." );

			params = "run_id=" + currRunId + "&client_id=" + SWARM.client_id;
			iframe = document.createElement( "iframe" );
			iframe.width = 1000;
			iframe.height = 600;
			iframe.className = "test";
			iframe.src = currRunUrl + (currRunUrl.indexOf( "?" ) > -1 ? "&" : "?") +
				"_=" + new Date().getTime() + "&swarmURL=" +
				encodeURIComponent(window.location.protocol + "//" + window.location.host + window.location.pathname + "?" + params + "&state=" );
			$( "#iframes" ).append( iframe );

			// Timeout after a period of time
			testTimeout = setTimeout( testTimedout, SWARM.client.timeout_rate * 1000 );

		} else {

			clearTimeout( pauseTimer );

			norun_msg = data.timeoutMsg || "No new tests to run.";

			msg(norun_msg);

			// If we just completed a run, do a cooldown_rate timeout before we fetch the next
			// run (if there is one). If we just completed a cooldown a no runs where available,
			// go for a (usually longer, depending on configuration) update_rate timeout instead.
			timeLeft = currRunUrl ? SWARM.client.cooldown_rate : SWARM.client.update_rate;

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
	}

	function done() {
		cancelTest();
		runTests({ timeoutMsg: "Cooling down." });
	}

	function handleMessage(e) {
		e = e || window.event;
		retrySend( e.data, function () {
			handleMessage(e);
		}, done );
	}

	/**
	 * Bind
	 */
	if ( window.addEventListener ) {
		window.addEventListener( "message", handleMessage, false );
	} else if ( window.attachEvent ) {
		window.attachEvent( "onmessage", handleMessage );
	}

	$( document).ready( getTests );

}( jQuery, SWARM ) );
