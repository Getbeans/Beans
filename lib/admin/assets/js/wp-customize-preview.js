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
	    	var newWidth = '100%';

            if ( true === api( 'beans_enable_viewport_width' ).get() ) {
	            newWidth = api( 'beans_viewport_width' ).get();
            }

            this.wpIframe.css( 'width', newWidth );
        },

	    /**
	     * Viewport height adjustment handler.  Sets the previewer's height.
	     *
	     * @since 1.0.0
	     */
        viewportHeight: function() {
        	var newHeight = '100%';

            if ( true === api( 'beans_enable_viewport_height' ).get() ) {
	            newHeight = api( 'beans_viewport_height' ).get();
            }

            this.wpIframe.css( 'height', newHeight );
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
		    var that = this;

            // Fire viewport width.
		    api( 'beans_enable_viewport_width', function( setting ) {
			    setting.bind( that.viewportWidth.bind(that) );
		    } );
		    api( 'beans_viewport_width', function( setting ) {
			    setting.bind( that.viewportWidth.bind(that) );
		    } );

            // Fire viewport height.
		    api( 'beans_enable_viewport_height', function( setting ) {
			    setting.bind( that.viewportHeight.bind(that) );
		    } );
		    api( 'beans_viewport_height', function( setting ) {
			    setting.bind( that.viewportHeight.bind(that) );
		    } );
        }
    };

    // Fire the plugin.
    $(document).ready(function() {
        new beansWpCustomize();
    });

})( window.jQuery, wp.customize );
