!(function($) {

    "use strict";

    var beansWpCustomize = function() {

        this.wpIframe = $( '#customize-preview iframe', window.parent.document.body );
        this.init();
        this.listen();

    }

    beansWpCustomize.prototype = {

        constructor: beansWpCustomize,

        viewportWidth: function() {

            if ( true == wp.customize.value( 'beans_enable_viewport_width' )() ) {
                this.wpIframe.css( 'width', wp.customize.value( 'beans_viewport_width' )() );
            } else {
                this.wpIframe.css( 'width', '100%' );
            }

        },
        viewportHeight: function() {

            if ( true == wp.customize.value( 'beans_enable_viewport_height' )() ) {
                this.wpIframe.css( 'height', wp.customize.value( 'beans_viewport_height' )() );
            } else {
                this.wpIframe.css( 'height', '100%' );
            }

        },
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
        listen: function() {

            var that = this;

            // Fire viewport width.
            wp.customize.value( 'beans_enable_viewport_width' ).bind( function( to ) {
                that.viewportWidth();
            } );

            // Fire viewport width.
            wp.customize.value( 'beans_viewport_width' ).bind( function( to ) {
                that.viewportWidth();
            } );

            // Fire viewport height.
            wp.customize.value( 'beans_enable_viewport_height' ).bind( function( to ) {
                that.viewportHeight();
            } );

            // Fire viewport height.
            wp.customize.value( 'beans_viewport_height' ).bind( function( to ) {
                that.viewportHeight();
            } );

        }

    };

    // Fire the plugin.
    $(document).ready(function($) {

        new beansWpCustomize();

    });

})( window.jQuery );
