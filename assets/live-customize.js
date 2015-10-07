jQuery( document ).ready( function ( $ ) {
	'use strict';

	$.each( PTWCU, function ( index, setting ) {
		if ( setting.selectors.length && setting.cssProp.length ) {
			wp.customize( setting.settingID, function( value ) {
				value.bind( function( newval ) {

					// background image needs a little bit different treatment
					if ( 'background-image' === setting.cssProp ) {
						newval = 'url(' + newval + ')';
					}

					$( setting.selectors ).css( setting.cssProp, newval );
				} );
			} );
		}
	} );

} );