<?php
/**
 * Sets up Beans's menus.
 *
 * @package Render\Menu
 */

beans_add_smart_action( 'after_setup_theme', 'beans_do_register_default_menu' );
/**
 * Register default menu.
 *
 * @since 1.0.0
 */
function beans_do_register_default_menu() {

	// Stop here if a menu already exists.
	if ( wp_get_nav_menus() ) {
		return;
	}

	$name = __( 'Navigation', 'tm-beans' );

	// Set up default menu if it doesn't exists.
	if ( ! wp_get_nav_menu_object( $name ) ) {
		wp_update_nav_menu_item(
			wp_create_nav_menu( $name ),
			0,
			array(
				'menu-item-title'   => __( 'Home', 'tm-beans' ),
				'menu-item-classes' => 'home',
				'menu-item-url'     => home_url( '/' ),
				'menu-item-status'  => 'publish',
			)
		);
	}

}

beans_add_smart_action( 'after_setup_theme', 'beans_do_register_nav_menus' );
/**
 * Register nav menus.
 *
 * @since 1.0.0
 */
function beans_do_register_nav_menus() {

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'tm-beans' ),
	) );

}

// Filter.
beans_add_smart_action( 'wp_nav_menu_args', 'beans_modify_menu_args' );
/**
 * Modify wp_nav_menu arguments.
 *
 * This function converts the wp_nav_menu to UIKit format. It uses Beans custom walker and also makes
 * use of the Beans HTML API.
 *
 * @since 1.0.0
 *
 * @param array $args The wp_nav_menu arguments.
 *
 * @return array The modified wp_nav_menu arguments.
 */
function beans_modify_menu_args( $args ) {

	// Get type.
	$type = beans_get( 'beans_type', $args );

	// Check if the menu is in a widget area and set the type accordingly if it is defined.
	if ( $widget_area_type = beans_get_widget_area( 'beans_type' ) ) {
		$type = ( 'stack' == $widget_area_type ) ? 'sidenav' : $widget_area_type;
	}

	// Stop if it isn't a beans menu.
	if ( ! $type ) {
		return $args;
	}

	// Default item wrap attributes.
	$attr = array(
		'id'    => '%1$s',
		'class' => array( beans_get( 'menu_class', $args ) ),
	);

	// Add UIKit navbar item wrap attributes.
	if ( 'navbar' == $type ) {
		$attr['class'][] = 'uk-navbar-nav';
	}

	// Add UIKit sidenav item wrap attributes.
	if ( 'sidenav' == $type ) {

		$attr['class'][] = 'uk-nav uk-nav-parent-icon uk-nav-side';
		$attr['data-uk-nav'] = '{multiple:true}';

	}

	// Add UIKit offcanvas item wrap attributes.
	if ( 'offcanvas' == $type ) {

		$attr['class'][] = 'uk-nav uk-nav-parent-icon uk-nav-offcanvas';
		$attr['data-uk-nav'] = '{multiple:true}';

	}

	// Implode to avoid empty spaces.
	$attr['class'] = implode( ' ', array_filter( $attr['class'] ) );

	// Set to null if empty to avoid outputing empty class html attribute.
	if ( ! $attr['class'] ) {
		$attr['class'] = null;
	}

	$location_subfilter = ( $location = beans_get( 'theme_location', $args ) ) ? "[_{$location}]" : null;

	// Force beans menu arguments.
	$force = array(
		'beans_type' => $type,
		'items_wrap' => beans_open_markup( "beans_menu[_{$type}]{$location_subfilter}", 'ul', $attr, $args ) . '%3$s' . beans_close_markup( "beans_menu[_{$type}]{$location_subfilter}", 'ul', $args ),
	);

	// Allow walker overwrite.
	if ( ! beans_get( 'walker', $args ) ) {
		$args['walker'] = new _Beans_Walker_Nav_Menu;
	}

	// Adapt level to walker depth.
	$force['beans_start_level'] = ( $level = beans_get( 'beans_start_level', $args ) ) ? ( $level - 1 ) : 0;

	return array_merge( $args, $force );

}
