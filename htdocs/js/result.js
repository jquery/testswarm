/**
 * JavaScript file for the "result" page in the browser.
 *
 * @author Timo Tijhof, 2012
 * @since 1.0
 * @package TestSwarm
 */
jQuery(function ( $ ) {

	// Even-out scrollbars
	$( '.swarm-result-frame' ).on( 'load', function () {
		var frame = this, frameDoc = frame.contentWindow.document;
		setTimeout( function () {
			frame.height = frameDoc.height;
		}, 50);
	});

	// Popup links
	$( '.swarm-popuplink' ).on( 'click', function ( e ) {
		window.open( this.href, '_blank', [
			'menubar=no',
			'toolbar=no',
			'location=yes',
			'personalbar=no',
			'status=yes',
			'resizable=yes',
			'scrollbars=yes',
			'minimizable=yes'
		].join(',') );
		e.preventDefault();
	});

});
