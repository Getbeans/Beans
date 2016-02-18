<?php
/**
 * Despite its name, this template echos between the opening HTML markup and the opening primary markup.
 *
 * This template must be called using get_header().
 *
 * @package Structure\Header
 */

echo beans_output( 'beans_doctype', '<!DOCTYPE html>' );

echo beans_open_markup( 'beans_html', 'html', str_replace( ' ', '&', str_replace( '"', '', beans_render_function( 'language_attributes' ) ) ) );

	echo beans_open_markup( 'beans_head', 'head' );

		/**
		 * Fires in the head.
		 *
		 * This hook fires in the head HTML section, not in wp_header().
		 *
		 * @since 1.0.0
		 */
		do_action( 'beans_head' );

		wp_head();

	echo beans_close_markup( 'beans_head', 'head' );

	echo beans_open_markup( 'beans_body', 'body', array(
		'class' => implode( ' ', get_body_class( 'uk-form no-js' ) ),
		'itemscope' => 'itemscope',
		'itemtype' => 'http://schema.org/WebPage'

	) );

		echo beans_open_markup( 'beans_site', 'div', array( 'class' => 'tm-site' ) );

			echo beans_open_markup( 'beans_main', 'main', array( 'class' => 'tm-main uk-block' ) );

				echo beans_open_markup( 'beans_fixed_wrap[_main]', 'div', 'class=uk-container uk-container-center' );

					echo beans_open_markup( 'beans_main_grid', 'div', array( 'class' => 'uk-grid', 'data-uk-grid-margin' => '' ) );

						echo beans_open_markup( 'beans_primary', 'div', array(
							'class' => 'tm-primary ' . beans_get_layout_class( 'content' )
						) );