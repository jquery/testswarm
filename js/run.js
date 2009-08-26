var updateRate = 30, timeoutRate = 180;

var run_id, run_url, testTimeout, pauseTimer;

if ( typeof client_id !== "undefined" ) {
	jQuery( getTests );
}

if ( window.addEventListener ) {
	window.addEventListener( "message", handleMessage, false );
} else if ( window.attachEvent ) {
	window.attachEvent( "onmessage", handleMessage );
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
		log( "Connected to the swarm." );
	}

	run_id = 0;
	run_url = "";

	msg("Querying for more tests to run...");
	retrySend( "state=getrun&client_id=" + client_id, getTests, runTests );
}

function runTests( txt ) {
	var data = txt && typeof txt === "string" ? eval("(" + txt + ")") : txt;

	if ( data.cmd ) {
		if ( typeof cmds[ data.cmd ] === "function" ) {
			cmds[ data.cmd ].apply( cmds, data.args.split(" ") );
		}

		runTests({ desc: "Cooling down." });

	} else if ( data.id ) {
		run_id = data.id;
		run_url = data.url;

		log("Running " + (data.desc || "") + " tests...");

		var params = "run_id=" + run_id + "&client_id=" + client_id;
		var iframe = document.createElement("iframe");
		iframe.className = "test";
		iframe.src = run_url + (run_url.indexOf("?") > -1 ? "&" : "?") + 
			"_=" + (new Date).getTime() + "&swarmURL=" +
			encodeURIComponent("http://" + window.location.host + "?" + params + "&state=");
		jQuery("#iframes").append( iframe );

		// Timeout after a period of time
		testTimeout = setTimeout( testTimedout, timeoutRate * 1000 );

	} else {
		clearTimeout( pauseTimer );

		var run_msg = data.desc || "No new tests to run.";

		msg(run_msg);

		var timeLeft = (updateRate * (run_url ? 0.5 : 1)) - 1;

		pauseTimer = setTimeout(function leftTimer(){
			msg(run_msg + " Getting more in " + timeLeft + " seconds.");
			if ( timeLeft-- >= 1 ) {
				pauseTimer = setTimeout( leftTimer, 1000 );
			} else {
				getTests();
			}
		}, 1000);
	}
}

function done() {
	cancelTest();
	runTests({ desc: "Cooling down." });
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
	retrySend( "state=saverun&fail=-1&total=-1&results=Test%20Timed%20Out.&run_id="
		+ run_id + "&client_id=" + client_id,
		testTimedout, getTests );
}

function handleMessage(e){
	retrySend( e.data, function(){
		handleMessage(e);
	}, done );
}

var errorOut = 0;

function retrySend( data, retry, success ) {
	jQuery.ajax({
		type: "POST",
		url: "/",
		timeout: 10000,
		cache: false,
		data: data,
		error: function() {
			if ( errorOut++ > 4 ) {
				cmds.reload();
			} else {
				msg("Error connecting to server, retrying...");
				setTimeout( retry, 15000 );
			}
		},
		success: function(){
			errorOut = 0;
			success.apply( this, arguments );
		}
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
