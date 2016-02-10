<?php
/**
 * Despite its name, this template echos between the closing primary markup and the closing HTML markup.
 *
 * This template must be called using get_footer().
 *
 * @package Structure\Footer
 */

						echo beans_close_markup( 'beans_primary', 'div' );

					echo beans_close_markup( 'beans_main_grid', 'div' );

				echo beans_close_markup( 'beans_fixed_wrap[_main]', 'div' );

			echo beans_close_markup( 'beans_main', 'main' );

		echo beans_close_markup( 'beans_site', 'div' );

		wp_footer();

	echo beans_close_markup( 'beans_body', 'body' );

echo beans_close_markup( 'beans_html', 'html' );