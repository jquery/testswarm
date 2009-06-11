var updateRate = 30, timeoutRate = 180;

var run_id, run_url, testTimeout;

if ( typeof client_id !== "undefined" ) {
	jQuery( getTests );
}

var cmds = {
	reload: function() {
		window.location.reload();
	},
	rate: function( num ) {
		updateRate = parseInt( num );
	}
};

function getTests() {
	if ( typeof run_id === "undefined" ) {
		log( "Connecting to the swarm." );
	}

	run_id = 0;
	run_url = "";

	msg("Querying for more tests to run...");
	retrySend( "state=getrun&client_id=" + client_id, getTests, runTests );
}

function runTests( txt ) {
	var parts = txt.split(" ");
	run_id = parts.shift();
	run_url = parts.join(" ");

	if ( run_id === "cmd" ) {
		if ( typeof cmds[ parts[0] ] === "function" ) {
			cmds[ parts[0] ].apply( cmds, parts.slice(1) );
		}

	} else if ( run_id ) {
		log("Running tests...");

		var params = "&run_id=" + run_id + "&client_id=" + client_id;
		var iframe = document.createElement("iframe");
		iframe.className = "test";
		iframe.src = run_url + (run_url.indexOf("?") > -1 ? "&" : "?") + 
			"_=" + (new Date).getTime() + "&swarmURL=" +
			encodeURIComponent("http://" + window.location.host + "?state=saverun" + params);
		jQuery("#iframes").append( iframe );

		// Timeout after a period of time
		testTimeout = setTimeout( testTimedout, timeoutRate * 1000 );

	} else {
		msg("No new tests to run.");

		var timeLeft = updateRate - 1;

		setTimeout(function leftTimer(){
			msg("No new tests to run. Getting more in " + timeLeft + " seconds.");
			if ( timeLeft-- >= 1 ) {
				setTimeout( leftTimer, 1000 );
			} else {
				getTests();
			}
		}, 1000);
	}
}

function done() {
	cancelTest();
	setTimeout( getTests, 3000 );
}

function cancelTest() {
	if ( testTimeout ) {
		clearTimeout( testTimeout );
		testTimeout = 0;
	}

	jQuery("iframe").remove();
}

function testTimedout() {
	cancelTest();
	retrySend( "state=saverun&fail=1&total=1&results=Test%20Timed%20Out.&run_id="
		+ run_id + "&client_id=" + client_id,
		testTimedout, getTests );
}

function retrySend( data, retry, success ) {
	jQuery.ajax({
		url: "/",
		timeout: 10000,
		cache: false,
		data: data,
		error: function() {
			msg("Error connecting to server, retrying...");
			setTimeout( retry, 15000 );
		},
		success: success
	});
}

function log( txt ) {
	jQuery("#history").prepend( "<li><strong>" + 
		(new Date).toString().replace(/^\w+ /, "").replace(/:[^:]+$/, "") +
		":</strong> " + txt + "</li>" );
	msg( txt );
}

function msg( txt ) {
	jQuery("#msg").text( txt );
}
