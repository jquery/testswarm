/**
 * JavaScript file for the "addjob" page.
 *
 * @author Timo Tijhof
 * @since 1.0.0
 * @package TestSwarm
 */
/* global jQuery */
jQuery(function( $ ) {
	var $addRunBtn,
		$runsContainer = $( "#runs-container" ),
		$runFieldsetClean = $runsContainer.children( "fieldset" ).eq( 0 ).clone().detach(),
		cnt = $runsContainer.children( "fieldset" ).length;

	$addRunBtn = $( "<button>" )
		.text( "+ Run" )
		.addClass( "btn" )
		.click(function ( e ) {
			e.preventDefault();

			cnt += 1;

			function fixNum( i, val ) {
				return val.replace( "1", cnt );
			}

			$addRunBtn.before(
				$runFieldsetClean.clone()
					.find( "input" ).val( "" )
					.end()
					.find( "[for*='1']" ).attr( "for", fixNum )
					.end()
					.find( "[id*='1']" ).attr( "id", fixNum )
					.end()
					.find( "legend" ).text( fixNum )
					.end()
			);
		})
		.appendTo( "<div class='form-actions'></div>" )
		.parent();

	$runsContainer.append( $addRunBtn );
});
