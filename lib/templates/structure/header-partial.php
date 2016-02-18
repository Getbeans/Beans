<?php
/**
 * Since WordPress force us to use the header.php name to open the document, we add a header-partial.php template for the actual header.
 *
 * @package Structure\Header
 */

echo beans_open_markup( 'beans_header', 'header', array(
	'class' => 'tm-header uk-block',
	'role' => 'banner',
	'itemscope' => 'itemscope',
	'itemtype' => 'http://schema.org/WPHeader'
) );

	echo beans_open_markup( 'beans_fixed_wrap[_header]', 'div', 'class=uk-container uk-container-center' );

		/**
		 * Fires in the header.
		 *
		 * @since 1.0.0
		 */
		do_action( 'beans_header' );

	echo beans_close_markup( 'beans_fixed_wrap[_header]', 'div' );

echo beans_close_markup( 'beans_header', 'header' );