<?php
/**
 * Echo the widget area and widget loop structural markup. It also calls the widget area and widget loop
 * action hooks.
 *
 * @package Structure\Widget_Area
 */

// This includes everything added to wp hooks before the widgets.
echo beans_get_widget_area( 'before_widgets' );

	if ( beans_get_widget_area( 'beans_type' ) == 'grid' )
		echo beans_open_markup( 'beans_widget_area_grid' . _beans_widget_area_subfilters(), 'div', array( 'class' => 'uk-grid', 'data-uk-grid-margin' => '' ) );

	if ( beans_get_widget_area( 'beans_type' ) == 'offcanvas' ) {

		echo beans_open_markup( 'beans_widget_area_offcanvas_wrap' . _beans_widget_area_subfilters(), 'div', array(
			'id' => beans_get_widget_area( 'id' ), // Automatically escaped.
			'class' => 'uk-offcanvas'
		) );

			echo beans_open_markup( 'beans_widget_area_offcanvas_bar' . _beans_widget_area_subfilters(), 'div', array( 'class' => 'uk-offcanvas-bar' ) );

	}

		// Widgets.
		if ( beans_have_widgets() ) :

			/**
			 * Fires before widgets loop.
			 *
			 * This hook only fires if widgets exist.
			 *
			 * @since 1.0.0
			 */
			do_action( 'beans_before_widgets_loop' );

				while ( beans_have_widgets() ) : beans_setup_widget();

					if ( beans_get_widget_area( 'beans_type' ) == 'grid' )
						echo beans_open_markup( 'beans_widget_grid' . _beans_widget_subfilters(), 'div', beans_widget_shortcodes( 'class=uk-width-medium-1-{count}' ) );

						echo beans_open_markup( 'beans_widget_panel' . _beans_widget_subfilters(), 'div', beans_widget_shortcodes( 'class=tm-widget uk-panel widget_{type} {id}' ) );

							/**
							 * Fires in each widget panel structural HTML.
							 *
							 * @since 1.0.0
							 */
							do_action( 'beans_widget' );

						echo beans_close_markup( 'beans_widget_panel' . _beans_widget_subfilters(), 'div' );

					if ( beans_get_widget_area( 'beans_type' ) == 'grid' )
						echo beans_close_markup( 'beans_widget_grid' . _beans_widget_subfilters(), 'div' );

				endwhile;

			/**
			 * Fires after the widgets loop.
			 *
			 * This hook only fires if widgets exist.
			 *
			 * @since 1.0.0
			 */
			do_action( 'beans_after_widgets_loop' );

		else :

			/**
			 * Fires if no widgets exist.
			 *
			 * @since 1.0.0
			 */
			do_action( 'beans_no_widget' );

		endif;

	if ( beans_get_widget_area( 'beans_type' ) == 'offcanvas' ) {

			echo beans_close_markup( 'beans_widget_area_offcanvas_bar' . _beans_widget_area_subfilters(), 'div' );

		echo beans_close_markup( 'beans_widget_area_offcanvas_wrap' . _beans_widget_area_subfilters(), 'div' );

	}

	if ( beans_get_widget_area( 'beans_type' ) == 'grid' )
		echo beans_close_markup( 'beans_widget_area_grid' . _beans_widget_area_subfilters(), 'div' );

// This includes everything added to wp hooks after the widgets.
echo beans_get_widget_area( 'after_widgets' );