/**
 * JavaScript Pretty Date
 * Copyright (c) 2008 John Resig (jquery.com)
 * Licensed under the MIT license.
 *
 * This is a modified version for TestSwarm.
 */

/**
 * Take a timestamp and turn it into a relative time representation
 *
 * @param time String ISO formatted timestamp (YYYY-MM-DDTHH:II:SSZ, where T and Z are literal)
 * @return String|undefined Relative time in English or undefined if too long ago
 */
function prettyDate( time ) {
	if ( !time ) {
		return;
	}

	var date = new Date( time );
	var diff = ( new Date().getTime() - date.getTime() ) / 1000;
	var dayDiff = Math.floor( diff / 86400 );

	if ( isNaN( dayDiff ) || dayDiff < 0 || dayDiff >= 31 ) {
		return;
	}

	return dayDiff === 0 && (
		diff < 10 && "just now" ||
		diff < 50 && Math.floor( diff ) + " seconds ago" ||
			diff < 120 && "1 minute ago" ||
				diff < 3000 && Math.floor( diff / 60 ) + " minutes ago" ||
					diff < 7200 && "1 hour ago" ||
						diff < 86400 && Math.floor( diff / 3600 ) + " hours ago"
	) ||
		dayDiff === 1 && "Yesterday" ||
		dayDiff < 7 && dayDiff + " days ago" ||
		dayDiff < 8 && "1 week ago" ||
		dayDiff < 31 && Math.ceil( dayDiff / 7 ) + " weeks ago";
}

/* global module */
if ( typeof module !== "undefined" && module.exports ) {
	module.exports.prettyDate = prettyDate;
}
