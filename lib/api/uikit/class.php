<?php
/**
 * Compile Uikit components.
 *
 * @ignore
 *
 * @package API\Uikit
 */
class _Beans_Uikit {

	/**
	 * Compile enqueued items.
	 */
	function compile() {

		global $_beans_uikit_enqueued_items;

		// Set filters for third parties, eventhough it should rarely be used.
		$styles = apply_filters( 'beans_uikit_euqueued_styles', $this->register_less_components() );
		$scripts = apply_filters( 'beans_uikit_euqueued_scripts', $this->register_js_components() );

		// Set default args filters for third parties.
		$styles_args = apply_filters( 'beans_uikit_euqueued_styles_args', array() );
		$scripts_args = apply_filters( 'beans_uikit_euqueued_scripts_args', array(
			'depedencies' => array( 'jquery' )
		) );

		// Compile less.
		if ( $styles )
			beans_compile_less_fragments( 'uikit', array_unique( $styles ), $styles_args );

		// Compile js.
		if ( $scripts )
			beans_compile_js_fragments( 'uikit', array_unique( $scripts ), $scripts_args );

	}


	/**
	 * Register less components.
	 */
	function register_less_components() {

		global $_beans_uikit_enqueued_items;

		$components = array();

		foreach ( $_beans_uikit_enqueued_items['components'] as $type => $items ) {

			// Add core before the components.
			if ( $type == 'core' )
				$items = array_merge( array( 'variables' ), $items );

			// Fetch components from directories.
			$components = array_merge( $components, $this->get_components_from_directory( $items, $this->get_less_directories( $type ), 'styles' ) );

		}

		// Add fixes.
		if ( !empty( $components ) )
			$components = array_merge( $components, array( BEANS_API_COMPONENTS_PATH . 'uikit/src/fixes.less' ) );

		return $components;

	}


	/**
	 * Register js components.
	 */
	function register_js_components() {

		global $_beans_uikit_enqueued_items;

		$components = array();

		foreach ( $_beans_uikit_enqueued_items['components'] as $type => $items ) {

			// Add core before the components.
			if ( $type == 'core' )
				$items = array_merge(
					array(
						'core',
						'component',
						'utility',
						'touch',
					),
					$items
				);

			// Fetch components from directories.
			$components = array_merge( $components, $this->get_components_from_directory( $items, $this->get_js_directories( $type ), 'scripts' ) );


		}

		return $components;

	}


	/**
	 * Get LESS directories.
	 */
	function get_less_directories( $type ) {

		if ( $type == 'add-ons' )
			$type = 'components';

		global $_beans_uikit_enqueued_items;

		// Define uikit src directory.
		$directories = array( BEANS_API_COMPONENTS_PATH . 'uikit/src/less/' . $type );
		// Add the registered theme directories.
		foreach ( $_beans_uikit_enqueued_items['themes'] as $id => $directory )
			$directories[] = wp_normalize_path( untrailingslashit( $directory ) );

		return $directories;

	}


	/**
	 * Get JS directories.
	 */
	function get_js_directories( $type ) {

		if ( $type == 'add-ons' )
			$type = 'components';

		// Define uikit src directory.
		return array( BEANS_API_COMPONENTS_PATH . 'uikit/src/js/' . $type );

	}


	/**
	 * Get components from directories.
	 */
	function get_components_from_directory( $components, $directories, $format ) {

		$extension = ( $format == 'styles' ) ? 'less' : 'min.js';

		$return = array();

		foreach ( $components as $component ) {

			// Fectch components from all directories set.
			foreach ( $directories as $directory ) {

				$file = trailingslashit( $directory ) . $component . '.' . $extension;

				// Make sure the file exists.
				if ( file_exists( $file ) )
					$return[] = $file;

			}

		}

		return $return;

	}


	/**
	 * Get all components.
	 */
	function get_all_components( $type ) {

		// Fetch all directories.
		$directories = array_merge( $this->get_less_directories( $type ), $this->get_js_directories( $type ) );

		$components = array();

		foreach ( $directories as $dir_path ) {

			if ( !is_dir( $dir_path ) )
				continue;

			$scandir = scandir( $dir_path );

			// Unset scandir defaults.
			unset( $scandir[0], $scandir[1] );

			// Only return the filname and remove empty elements.
			$components = array_merge( $components, array_filter( array_map( array( $this, 'to_filename'), $scandir ) ) );

		}

		return $components;

	}


	/**
	 * Convert component to a filename.
	 */
	function to_filename( $file ) {

		$pathinfo = pathinfo( $file );

		$ignore = array(
			'uikit-customizer',
			'uikit'
		);

		// Stop here if it isn't a valid file or if it should be ignored.
		if ( !isset( $pathinfo['filename'] ) || in_array( $pathinfo['filename'], $ignore ) )
			return null;

		// Return the filename without the .min to avoid duplicates.
		return str_replace( '.min', '', $pathinfo['filename'] );

	}

}