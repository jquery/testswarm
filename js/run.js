var updateRate = 30, timeoutRate = 300;

var run_id, testTimeout;

if ( typeof client_id !== "undefined" ) {
	jQuery(getTests);
}

function msg(txt){
	jQuery("p.msg").text( txt );
}

function getTests(){
	run_id = 0;

	msg("Querying for more tests to run...");
	retrySend("state=getrun&client_id=" + client_id, getTests, runTests);
}

function runTests(id){
	run_id = id;

	if ( run_id ) {
		msg("Running tests...");

		var iframe = document.createElement("iframe");
		iframe.src = "/?state=showrun&run_id=" + run_id + "&client_id=" + client_id;
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

function done(data){
	msg("Saving test results...");

	cancelTest();

	retrySend({
			"state": "saverun",
			"run_id": run_id,
			"results": data.results,
			"total": data.total,
			"fail": data.fail
		},
		done,
		getTests
	);
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
