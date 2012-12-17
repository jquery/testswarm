/**
 * JavaScript Pretty Date
 *
 * @author John Resig (ejohn.org), 2008-2011
 * @license Licensed under the MIT and GPL licenses.
 *
 * Modified version for TestSwarm (2012).
 */

/**
 * Take a timestamp and turn it into a relative time representation
 *
 * @param time String ISO formatted timestamp (YYYY-MM-DDTHH:II:SSZ, where T and Z are literal)
 * @return String|undefined Relative time in English or undefined if too long ago
 */
function prettyDate( time ) {
	var date, diff, day_diff;

	if ( !time ) {
		return;
	}

	date = new Date( time );
	diff = ( new Date().getTime() - date.getTime() ) / 1000;
	day_diff = Math.floor( diff / 86400 );

	if ( isNaN( day_diff ) || day_diff < 0 || day_diff >= 31 ) {
		return;
	}

	return day_diff === 0 && (
		diff < 3 && 'just now' ||
		diff < 60 && Math.floor( diff ) + ' seconds ago' ||
			diff < 120 && '1 minute ago' ||
				diff < 3600 && Math.floor( diff / 60 ) + ' minutes ago' ||
					diff < 7200 && '1 hour ago' ||
						diff < 86400 && Math.floor( diff / 3600 ) + ' hours ago'
	) ||
		day_diff === 1 && 'Yesterday' ||
		day_diff < 7 && day_diff + ' days ago' ||
		day_diff < 31 && Math.ceil( day_diff / 7 ) + ' weeks ago';
}

// If jQuery is included in the page, adds a jQuery plugin to handle it as well
if ( typeof jQuery !== 'undefined' ) {
	jQuery.fn.prettyDate = function () {
		return this.each( function () {
			var date = prettyDate( this.title );
			if ( date ) {
				jQuery( this ).text( date );
			}
		} );
	};
}
