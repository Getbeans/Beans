<?php
/**
 * Echo footer fragments.
 *
 * @package Fragments\Footer
 */

beans_add_smart_action( 'beans_footer', 'beans_footer_content' );

/**
 * Echo the footer content.
 *
 * @since 1.0.0
 */
function beans_footer_content() {

	echo beans_open_markup( 'beans_footer_credit', 'div', array( 'class' => 'uk-clearfix uk-text-small uk-text-muted' ) );

		echo beans_open_markup( 'beans_footer_credit_left', 'span', array(
			'class' => 'uk-align-medium-left uk-margin-small-bottom'
		) );

			echo beans_output( 'beans_footer_credit_text', sprintf(
				__( '&#x000A9; %1$s - %2$s. All rights reserved.', 'tm-beans' ),
				date( "Y" ),
				get_bloginfo( 'name' )
			) );

		echo beans_close_markup( 'beans_footer_credit_left', 'span' );

		$framework_link = beans_open_markup( 'beans_footer_credit_framework_link', 'a', array(
			'href' => 'http://www.getbeans.io', // Automatically escaped.
			'rel' => 'designer'
		) );

			$framework_link .= beans_output( 'beans_footer_credit_framework_link_text', 'tm-beans' );

		$framework_link .= beans_close_markup( 'beans_footer_credit_framework_link', 'a' );

		echo beans_open_markup( 'beans_footer_credit_right', 'span', array(
			'class' => 'uk-align-medium-right uk-margin-bottom-remove'
		) );

			echo beans_output( 'beans_footer_credit_right_text', sprintf(
				__( '%1$s theme for WordPress.', 'tm-beans' ),
				$framework_link
			) );

		echo beans_close_markup( 'beans_footer_credit_right', 'span' );


	echo beans_close_markup( 'beans_footer_credit', 'div' );

}


beans_add_smart_action( 'wp_footer', 'beans_replace_nojs_class' );

/**
 * Print inline JavaScript in the footer to replace the 'no-js' class with 'js'.
 *
 * @since 1.0.0
 */
function beans_replace_nojs_class() {

	?><script type="text/javascript">
		(function() {
			document.body.className = document.body.className.replace('no-js','js');
		}());
	</script><?php

}