<?php
/**
 * Echo header fragments.
 *
 * @package Fragments\Header
 */

beans_add_smart_action( 'beans_head', 'beans_head_meta', 0 );

/**
 * Echo head meta.
 *
 * @since 1.0.0
 */
function beans_head_meta() {

	echo '<meta charset="' . get_bloginfo( 'charset' ) . '" />' . "\n";
	echo '<meta name="viewport" content="width=device-width, initial-scale=1" />' . "\n";

}


beans_add_smart_action( 'beans_head', 'beans_head_title' );

/**
 * Echo head title.
 *
 * @since 1.0.0
 */
function beans_head_title() {

	echo '<title>';
		wp_title( '|', true, 'right' );
	echo '</title>';

}


beans_add_smart_action( 'wp_head', 'beans_head_pingback');

/**
 * Echo head pingback.
 *
 * @since 1.0.0
 */
function beans_head_pingback() {

	echo '<link rel="pingback" href="' . get_bloginfo( 'pingback_url' ) . '">' . "\n";

}

// Filter.
beans_add_smart_action( 'wp_title', 'beans_wp_title', 10, 2 );

/**
 * Modify head wp title.
 *
 * @since 1.0.0
 *
 * @param string $title The WordPress default title.
 * @param string $sep The title separator.
 *
 * @return string The modified title.
 */
function beans_wp_title( $title, $sep ) {

	global $page, $paged;

	if ( is_feed() )
		return $title;

	// Add the blog name.
	$title .= get_bloginfo( 'name' );

	// Add the blog description for the home/front page.
	if ( ( $site_description = get_bloginfo( 'description', 'display' ) ) && ( is_home() || is_front_page() ) )
		$title .= " $sep $site_description";

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $sep " . sprintf( __( 'Page %s', 'beans' ), max( $paged, $page ) );

	return $title;

}


beans_add_smart_action( 'wp_head', 'beans_favicon');

/**
 * Echo head favicon if no icon was added via the customizer.
 *
 * @since 1.0.0
 */
function beans_favicon() {

	// Stop here if and icon was added via the customizer.
	if ( function_exists( 'has_site_icon' ) && has_site_icon() )
		return;

	$path = file_exists( get_template_directory() . 'favicon.ico' ) ? get_template_directory() . 'favicon.ico' : BEANS_URL . 'favicon.ico';

	echo beans_selfclose_markup( 'beans_favicon', 'link', array(
		'rel' => 'Shortcut Icon',
		'href' => $path,
		'type' => 'image/x-icon',
	) );

}


beans_add_smart_action( 'wp_head', 'beans_header_image' );

/**
 * Print the header image css inline in the header.
 *
 * @since 1.0.0
 */
function beans_header_image() {

	if ( !current_theme_supports( 'custom-header' ) || !( $header_image = get_header_image() ) || empty( $header_image ) )
		return;

	?><style type="text/css">
		.tm-header {
			background-image: url(<?php echo esc_url( $header_image ); ?>);
			background-position: 50% 50%;
			background-size: cover;
			background-repeat: no-repeat;
		}
	</style><?php

}


beans_add_smart_action( 'beans_header', 'beans_site_branding' );

/**
 * Echo header site branding.
 *
 * @since 1.0.0
 */
function beans_site_branding() {

	$name = beans_output( 'beans_site_title_text', get_bloginfo( 'name' ) );

	if ( $logo = get_theme_mod( 'beans_logo_image', false ) )
		$name = beans_selfclose_markup( 'beans_logo_image', 'img', array(
			'class' => 'tm-logo',
			'src' => $logo,
			'alt' => $name,
		) );

	echo beans_open_markup( 'beans_site_branding', 'div', array(
		'class' => 'tm-site-branding uk-float-left' . ( !get_bloginfo( 'description' ) ? ' uk-margin-small-top' : null ),
	) );

		echo beans_open_markup( 'beans_site_title_link', 'a', array(
			'href' => esc_url( home_url() ),
			'rel' => 'home',
			'itemprop' => 'headline'
		) );

			echo $name;

		echo beans_close_markup( 'beans_site_title_link', 'a' );

	echo beans_close_markup( 'beans_site_branding', 'div' );

}


beans_add_smart_action( 'beans_site_branding_append_markup', 'beans_site_title_tag' );

/**
 * Echo header site title tag.
 *
 * @since 1.0.0
 */
function beans_site_title_tag() {

	// Stop here if there isn't a description.
	if ( !$description = get_bloginfo( 'description' ) )
		return;

	echo beans_open_markup( 'beans_site_title_tag', 'span', array(
		'class' => 'tm-site-title-tag uk-text-small uk-text-muted uk-display-block',
		'itemprop' => 'description'
	) );

		echo beans_output( 'beans_site_title_tag_text', $description );

	echo beans_close_markup( 'beans_site_title_tag', 'span' );

}