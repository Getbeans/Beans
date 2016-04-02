<?php

// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) )
	exit();

if ( !isset( $_GET['beans-compiler'] ) && $_GET['beans-compiler'] != 'cached' )
	return;

$gzip = true;

// Stop here if zlib is not loaded.
if ( !extension_loaded( 'zlib' ) )
	$gzip = false;

// Stop here if zlib.output_compression is on.
if ( ini_get( 'zlib.output_compression' ) == 1 )
	$gzip = false;

// Stop here if a cache system is active then do not gzip.
if ( defined( 'WP_CACHE' ) && WP_CACHE )
	$gzip = false;

// Stop here if gzip has already happened.
if ( in_array( 'ob_gzhandler', ob_list_handlers() ) )
	$gzip = false;

if ( $gzip )
	ob_start( 'ob_gzhandler' );

// Include file.
if ( ( $id = $_GET['id'] ) && ( $file = $_GET['file'] ) && ( $type = $_GET['type'] ) ) {

	$expires = 60 * 60 * 24 * 30;

	header( "Pragma: public" );
	header( "Cache-Control: maxage=" . $expires );
	header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + $expires ) . ' GMT' );

	if ( $type == 'style' )
		header( "Content-type: text/css" );

	elseif ( $type == 'script' )
		header( "content-type: application/x-javascript" );

	$file = realpath( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $file );

	include( $file );

}

exit;