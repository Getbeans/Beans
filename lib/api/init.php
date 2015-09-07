<?php
/**
 *
 * Load components.
 *
 * @ignore
 *
 * @package Beans
 */

// Mode.
if ( !defined( 'SCRIPT_DEBUG' ) )
	define( 'SCRIPT_DEBUG', false );

// Assets.
define( 'BEANS_MIN_CSS', SCRIPT_DEBUG ? '' : '.min' );
define( 'BEANS_MIN_JS', SCRIPT_DEBUG ? '' : '.min' );

// Path.
define( 'BEANS_API_COMPONENTS_PATH', trailingslashit( dirname( __FILE__ ) ) );
define( 'BEANS_API_COMPONENTS_ADMIN_PATH', BEANS_API_COMPONENTS_PATH . 'admin/' );

// Load dependencies here as it is used further down.
require_once( BEANS_API_COMPONENTS_PATH . 'utilities/functions.php' );
require_once( BEANS_API_COMPONENTS_PATH . 'components.php' );

// Url.
define( 'BEANS_API_COMPONENTS_URL', beans_path_to_url( BEANS_API_COMPONENTS_PATH ) );