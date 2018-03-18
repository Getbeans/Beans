(function( $ ){
	'use strict';

	/**
	 * Initialize the script.
	 */
	var init = function() {
		$( 'some-button' ).on( 'click', clickHandler );
	}

	/**
	 * Handle the button's click event.
	 *
	 * @param event
	 */
	var clickHandler = function( event ) {
		event.preventDefault();

		// do something cool here.
	}

	/**
	 * Wait until the document is ready.
	 */
	$( document ).ready( function(){
		init();
	});

})( jQuery );
