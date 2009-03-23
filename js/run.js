var updateRate = 30, timeoutRate = 300;

var run_id, run_url, testTimeout;

if ( typeof client_id !== "undefined" ) {
	jQuery(getTests);
}

function msg(txt){
	jQuery("p.msg").text( txt );
}

function getTests(){
	run_id = 0;
	run_url = "";

	msg("Querying for more tests to run...");
	retrySend("state=getrun&client_id=" + client_id, getTests, runTests);
}

function runTests(txt){
	var parts = txt.split(" ");
	run_id = parts[0];
	run_url = parts[1];

	if ( run_id ) {
		msg("Running tests...");

		var params = "&run_id=" + run_id + "&client_id=" + client_id;
		var iframe = document.createElement("iframe");
		iframe.src = run_url + (run_url.indexOf("?") > -1 ? "&" : "?") + "_=" + (new Date).getTime() + "&swarmURL=" +
			encodeURIComponent("http://" + window.location.host + "?state=saverun" + params);
		document.body.appendChild( iframe );

		// Timeout after a period of time
		testTimeout = setTimeout(testTimedout, timeoutRate * 1000);

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

function done(){
	cancelTest();
	getTests();
}

function cancelTest(){
	if ( testTimeout ) {
		clearTimeout(testTimeout);
		testTimeout = 0;
	}

	jQuery("iframe").remove();
}

function testTimedout(){
	cancelTest();
	retrySend("state=timeoutrun&run_id=" + run_id + "&client_id=" + client_id,
		testTimedout, getTests);
}

function retrySend(data, retry, success){
	jQuery.ajax({
		url: "/",
		timeout: 10000,
		cache: false,
		data: data,
		error: function(){
			msg("Error connecting to server, retrying...");
			setTimeout( retry, 15000 );
		},
		success: success
	});
}

function msg(txt){
	jQuery("p.msg").text( txt );
}
