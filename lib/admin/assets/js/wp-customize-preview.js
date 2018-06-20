!(function( $, api ) {
    'use strict';

    var beansWpCustomize = function() {
        this.wpIframe = $( '#customize-preview iframe', window.parent.document.body );
        this.init();
        this.listen();
    }

    beansWpCustomize.prototype = {
        constructor: beansWpCustomize,

	    /**
	     * Viewport width adjustment handler.  Sets the previewer's width.
	     *
	     * @since 1.0.0
	     */
	    viewportWidth: function() {

            if ( true === api.value( 'beans_enable_viewport_width' )() ) {
                this.wpIframe.css( 'width', api.value( 'beans_viewport_width' )() );
            } else {
                this.wpIframe.css( 'width', '100%' );
            }
        },

	    /**
	     * Viewport height adjustment handler.  Sets the previewer's height.
	     *
	     * @since 1.0.0
	     */
        viewportHeight: function() {

            if ( true === api.value( 'beans_enable_viewport_height' )() ) {
                this.wpIframe.css( 'height', api.value( 'beans_viewport_height' )() );
            } else {
                this.wpIframe.css( 'height', '100%' );
            }

        },

	    /**
	     * Initialize.
	     *
	     * @since 1.0.0
	     */
        init: function() {
            this.wpIframe.css( {
                'position': 'absolute',
                'margin': 'auto',
                'top': '0',
                'left': '0',
                'right': '0',
            } );

            this.viewportWidth();
            this.viewportHeight();
        },

	    /**
	     * Listens for change events on the fields.
	     *
	     * @since 1.5.0
	     */
	    listen: function() {
            // Fire viewport width.
            api.value( 'beans_enable_viewport_width' ).bind( this.viewportWidth.bind( this ) );
            api.value( 'beans_viewport_width' ).bind( this.viewportWidth.bind( this ) );

            // Fire viewport height.
            api.value( 'beans_enable_viewport_height' ).bind( this.viewportHeight.bind( this ) );
            api.value( 'beans_viewport_height' ).bind( this.viewportHeight.bind( this ) );
        }
    };

    // Fire the plugin.
    $(document).ready(function() {
        new beansWpCustomize();
    });

})( window.jQuery, wp.customize );
