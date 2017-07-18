/*jshint node:true, es3: false */
var assert = require( "assert" ),
	tk = require( "timekeeper" ),
	prettyDate = require( "../js/pretty" ).prettyDate;

console.log( "## Test: /js/pretty.js " );

tk.freeze( "2011-04-01T00:00:00Z" );
( [
	{ input: "", output: undefined },
	{ input: "2010-04-01T00:00:00Z", output: undefined },
	{ input: "2011-01-01T00:00:00Z", output: undefined },
	{ input: "2011-02-01T00:00:00Z", output: undefined },
	{ input: "2011-03-01T00:00:00Z", output: undefined },
	{ input: "2011-03-10T00:00:00Z", output: "4 weeks ago" },
	{ input: "2011-03-21T00:00:00Z", output: "2 weeks ago" },
	{ input: "2011-03-22T00:00:00Z", output: "2 weeks ago" },
	{ input: "2011-03-23T00:00:00Z", output: "2 weeks ago" },
	{ input: "2011-03-24T00:00:00Z", output: "2 weeks ago" },
	{ input: "2011-03-25T00:00:00Z", output: "1 week ago" },
	{ input: "2011-03-26T00:00:00Z", output: "6 days ago" },
	{ input: "2011-03-29T00:00:00Z", output: "3 days ago" },
	{ input: "2011-03-30T00:00:00Z", output: "2 days ago" },
	{ input: "2011-03-31T00:00:00Z", output: "Yesterday" },
	{ input: "2011-03-31T10:00:00Z", output: "14 hours ago" },
	{ input: "2011-03-31T22:00:00Z", output: "2 hours ago" },
	{ input: "2011-03-31T23:00:00Z", output: "1 hour ago" },
	{ input: "2011-03-31T23:30:00Z", output: "30 minutes ago" },
	{ input: "2011-03-31T23:45:40Z", output: "14 minutes ago" },
	{ input: "2011-03-31T23:59:00Z", output: "1 minute ago" },
	{ input: "2011-03-31T23:59:30Z", output: "30 seconds ago" },
	{ input: "2011-03-31T23:59:49Z", output: "11 seconds ago" },
	{ input: "2011-03-31T23:59:55Z", output: "just now" }
] ).forEach( function ( val ) {
	console.log( "-", JSON.stringify( val.input ), JSON.stringify( val.output ) );
	assert.strictEqual( prettyDate( val.input ), val.output );
} );

console.log( "Done!\n" );
