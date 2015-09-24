<?php
/**
 * Registers Beans's default widget areas.
 *
 * @package Render\Widgets
 */

beans_add_smart_action( 'widgets_init', 'beans_do_register_widget_areas', 5 );

/**
 * Register Beans's default widget areas.
 *
 * @since 1.0.0
 */
function beans_do_register_widget_areas() {

	// Keep primary sidebar first for default widget asignment.
	beans_register_widget_area( array(
		'name' => __( 'Sidebar Primary', 'tm-beans' ),
		'id' => 'sidebar_primary'
	) );

	beans_register_widget_area( array(
		'name' => __( 'Sidebar Secondary', 'tm-beans' ),
		'id' => 'sidebar_secondary'
	) );

	if ( current_theme_supports( 'offcanvas-menu' ) )
		beans_register_widget_area( array(
			'name' => __( 'Off-Canvas Menu', 'tm-beans' ),
			'id' => 'offcanvas_menu',
			'beans_type' => 'offcanvas',
		) );

}