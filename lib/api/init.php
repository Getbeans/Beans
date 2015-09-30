<?php
/**
 *
 * Load components.
 *
 * @ignore
 *
 * @package Beans
 */

// Stop here if the API was already loaded.
if ( defined( 'BEANS_API' ) )
	return;

// Mode.
if ( !defined( 'SCRIPT_DEBUG' ) )
	define( 'SCRIPT_DEBUG', false );

// Declare Beans API.
define( 'BEANS_API', true );

// Assets.
define( 'BEANS_MIN_CSS', SCRIPT_DEBUG ? '' : '.min' );
define( 'BEANS_MIN_JS', SCRIPT_DEBUG ? '' : '.min' );

// Path.
define( 'BEANS_API_COMPONENTS_PATH', wp_normalize_path( trailingslashit( dirname( __FILE__ ) ) ) );
define( 'BEANS_API_COMPONENTS_ADMIN_PATH', BEANS_API_COMPONENTS_PATH . 'admin/' );

// Load dependencies here as it is used further down.
require_once( BEANS_API_COMPONENTS_PATH . 'utilities/functions.php' );
require_once( BEANS_API_COMPONENTS_PATH . 'components.php' );

// Url.
define( 'BEANS_API_COMPONENTS_URL', beans_path_to_url( BEANS_API_COMPONENTS_PATH ) );