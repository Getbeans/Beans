<?php
/**
 * This class handles the UIkit components.
 *
 * @package Beans\Framework\API\UIkit
 *
 * @since   1.0.0
 */

/**
 * Compile UIkit components.
 *
 * @since   1.0.0
 * @ignore
 * @access  private
 *
 * @package Beans\Framework\API\UIkit
 */
final class _Beans_Uikit {

	/**
	 * Components to ignore.
	 *
	 * @var array
	 */
	private $ignored_components = array( 'uikit-customizer', 'uikit' );

	/**
	 * The configured components' dependencies.
	 *
	 * @var array
	 */
	private static $configured_components_dependencies;

	/**
	 * Compile enqueued items.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function compile() {
		global $_beans_uikit_enqueued_items;

		/**
		 * Filter UIkit enqueued style components.
		 *
		 * @since 1.0.0
		 *
		 * @param array $components An array of UIkit style component files.
		 */
		$styles = apply_filters( 'beans_uikit_euqueued_styles', $this->register_less_components() );

		/**
		 * Filter UIkit enqueued script components.
		 *
		 * @since 1.0.0
		 *
		 * @param array $components An array of UIkit script component files.
		 */
		$scripts = apply_filters( 'beans_uikit_euqueued_scripts', $this->register_js_components() );

		/**
		 * Filter UIkit style compiler arguments.
		 *
		 * @since 1.0.0
		 *
		 * @param array $components An array of UIkit style compiler arguments.
		 */
		$styles_args = apply_filters( 'beans_uikit_euqueued_styles_args', array() );

		/**
		 * Filter UIkit script compiler arguments.
		 *
		 * @since 1.0.0
		 *
		 * @param array $components An array of UIkit script compiler arguments.
		 */
		$scripts_args = apply_filters(
			'beans_uikit_euqueued_scripts_args',
			array(
				'depedencies' => array( 'jquery' ),
			)
		);

		// Compile less.
		if ( $styles ) {
			beans_compile_less_fragments( 'uikit', array_unique( $styles ), $styles_args );
		}

		// Compile js.
		if ( $scripts ) {
			beans_compile_js_fragments( 'uikit', array_unique( $scripts ), $scripts_args );
		}
	}

	/**
	 * Register less components.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function register_less_components() {
		global $_beans_uikit_enqueued_items;

		$components = array();

		foreach ( $_beans_uikit_enqueued_items['components'] as $type => $items ) {

			// Add core before the components.
			if ( 'core' === $type ) {
				$items = array_merge( array( 'variables' ), $items );
			}

			beans_join_arrays(
				$components,
				$this->get_components_from_directory( $items, $this->get_less_directories( $type ), 'styles' )
			);
		}

		// Add fixes.
		if ( ! empty( $components ) ) {
			beans_join_arrays( $components, array( BEANS_API_PATH . 'uikit/src/fixes.less' ) );
		}

		return $components;
	}

	/**
	 * Register JavaScript components.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function register_js_components() {
		global $_beans_uikit_enqueued_items;

		$components = array();

		foreach ( $_beans_uikit_enqueued_items['components'] as $type => $items ) {

			// Add core before the components.
			if ( 'core' === $type ) {
				$items = array_merge(
					array(
						'core',
						'component',
						'utility',
						'touch',
					),
					$items
				);
			}

			// Fetch components from directories.
			$components = array_merge( $components, $this->get_components_from_directory( $items, $this->get_js_directories( $type ), 'scripts' ) );
		}

		return $components;
	}

	/**
	 * Get LESS directories.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Type of the UIkit components.
	 *
	 * @return array
	 */
	public function get_less_directories( $type ) {

		if ( 'add-ons' === $type ) {
			$type = 'components';
		}

		global $_beans_uikit_enqueued_items;

		// Define the UIkit src directory.
		$directories = array( BEANS_API_PATH . 'uikit/src/less/' . $type );

		// Add the registered theme directories.
		foreach ( $_beans_uikit_enqueued_items['themes'] as $id => $directory ) {
			$directories[] = wp_normalize_path( untrailingslashit( $directory ) );
		}

		return $directories;
	}

	/**
	 * Get JavaScript directories.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Type.
	 *
	 * @return array
	 */
	public function get_js_directories( $type ) {

		if ( 'add-ons' === $type ) {
			$type = 'components';
		}

		// Define the UIkit src directory.
		return array( BEANS_API_PATH . 'uikit/src/js/' . $type );
	}

	/**
	 * Get components from directories.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $components  Array of UIkit Components.
	 * @param array  $directories Array of directories containing the UIkit Components.
	 * @param string $format      File format.
	 *
	 * @return array
	 */
	public function get_components_from_directory( array $components, array $directories, $format ) {

		if ( empty( $components ) ) {
			return array();
		}

		$extension = 'styles' === $format ? 'less' : 'min.js';

		$return = array();

		foreach ( $components as $component ) {

			// Fetch the components from all directories set.
			foreach ( $directories as $directory ) {
				$file = trailingslashit( $directory ) . $component . '.' . $extension;

				// Make sure the file exists.
				if ( is_readable( $file ) ) {
					$return[] = $file;
				}
			}
		}

		return $return;
	}

	/**
	 * Get all components.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Type of UIkit components ('core' or 'add-ons').
	 *
	 * @return array
	 */
	public function get_all_components( $type ) {
		// Fetch all directories.
		$directories = array_merge( $this->get_less_directories( $type ), $this->get_js_directories( $type ) );

		$components = array();

		foreach ( $directories as $dir_path ) {

			if ( ! is_dir( $dir_path ) ) {
				continue;
			}

			$scandir = scandir( $dir_path );

			// Unset scandir defaults.
			unset( $scandir[0], $scandir[1] );

			// Only return the filname and remove empty elements.
			$components = array_merge( $components, array_filter( array_map( array(
				$this,
				'to_filename',
			), $scandir ) ) );
		}

		return $components;
	}

	/**
	 * Gets all of the required dependencies for the given components.
	 *
	 * @since 1.0.0
	 *
	 * @param array $components The given components to search for dependencies.
	 *
	 * @return array
	 */
	public function get_autoload_components( array $components ) {
		$dependencies = array(
			'core'    => array(),
			'add-ons' => array(),
		);

		$this->init_component_dependencies();

		// Build dependencies for each component.
		foreach ( (array) $components as $component ) {
			$component_dependencies = beans_get( $component, self::$configured_components_dependencies, array() );

			foreach ( $component_dependencies as $type => $dependency ) {
				$dependencies[ $type ] = array_merge( $dependencies[ $type ], $dependency );
			}
		}

		return $this->remove_duplicate_values( $dependencies );
	}

	/**
	 * Removes duplicate values from the given source array.
	 *
	 * @since 1.5.0
	 *
	 * @param array $source The given array to iterate and remove duplicate values.
	 *
	 * @return array
	 */
	private function remove_duplicate_values( array $source ) {

		foreach ( $source as $key => $value ) {

			if ( empty( $value ) || ! is_array( $value ) ) {
				continue;
			}

			$source[ $key ] = array_values( array_unique( $value ) );
		}

		return $source;
	}

	/**
	 * Initialize the components' dependencies, by loading from its configuration file when null.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	private function init_component_dependencies() {

		if ( ! is_null( self::$configured_components_dependencies ) ) {
			return;
		}

		self::$configured_components_dependencies = require dirname( __FILE__ ) . '/config/component-dependencies.php';
	}

	/**
	 * Convert component to a filename.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file File name.
	 *
	 * @return null|string
	 */
	public function to_filename( $file ) {
		$pathinfo = pathinfo( $file );


		// If the given file is not valid, bail out.
		if ( ! isset( $pathinfo['filename'] ) ) {
			return null;
		}

		// Stop here if it isn't a valid file or if it should be ignored.
		if ( $this->ignore_component( $pathinfo['filename'] ) ) {
			return null;
		}

		// Return the filename without the .min to avoid duplicates.
		return str_replace( '.min', '', $pathinfo['filename'] );
	}

	/**
	 * Checks if the given component's filename should be ignored.
	 *
	 * @since 1.5.0
	 *
	 * @param string $filename The filename to check against the ignored components.
	 *
	 * @return bool
	 */
	private function ignore_component( $filename ) {
		return in_array( $filename, $this->ignored_components, true );
	}
}
