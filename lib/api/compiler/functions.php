<?php
/**
 * Compile and cache CSS, LESS and JS files.
 *
 * The Beans Compiler is there to compile multiple internal or external CSS, LESS and JS files on a
 * per page basis. LESS content will automatically be converted to CSS.
 *
 * When development mode is enabled, internal files changes will automatically be detected. Third party enqueued
 * styles and scripts can be compiled and cached into a single file.
 *
 * @package API\Compiler
 */

/**
 * Compile CSS fragments and enqueue compiled file.
 *
 * This function should be used in a similar fashion to
 * {@link http://codex.wordpress.org/Function_Reference/wp_enqueue_script wp_enqueue_script()}.
 *
 * Fragments can be added to the compiler using {@see beans_compiler_add_fragment()}.
 *
 * @since 1.0.0
 *
 * @param string       $id        A unique string used as a reference. Similar to the WordPress scripts
 *                                $handle argument.
 * @param string|array $fragments File(s) absolute path. Internal or external file(s) url accepted but may increase compiling time.
 * @param array        $args      {
 *      Optional. Array of arguments used by the compiler.
 *
 * 		@type array $depedencies An array of registered handles this script depends on. Default false.
 * }
 */
function beans_compile_css_fragments( $id, $fragments, $args = array() ) {

	if ( empty( $fragments ) ) {
		return false;
	}

	$params = array(
		'id'        => $id,
		'type'      => 'style',
		'format'    => 'css',
		'fragments' => (array) $fragments,
	);

	new _Beans_Compiler( $params + $args );

}

/**
 * Compile LESS fragments, convert to CSS and enqueue compiled file.
 *
 * This function should be used in a similar fashion to
 * {@link http://codex.wordpress.org/Function_Reference/wp_enqueue_script wp_enqueue_script()}.
 *
 * Fragments can be added to the compiler using {@see beans_compiler_add_fragment()}.
 *
 * @since 1.0.0
 *
 * @param string       $id        The compiler ID. Similar to the WordPress scripts $handle argument.
 * @param string|array $fragments File(s) absolute path. Internal or external file(s) url accepted but may increase compiling time.
 * @param array        $args      {
 *      Optional. Array of arguments used by the compiler.
 *
 * 		@type array $depedencies An array of registered handles this script depends on. Default false.
 * }
 */
function beans_compile_less_fragments( $id, $fragments, $args = array() ) {

	if ( empty( $fragments ) ) {
		return false;
	}

	$params = array(
		'id'        => $id,
		'type'      => 'style',
		'format'    => 'less',
		'fragments' => (array) $fragments,
	);

	new _Beans_Compiler( $params + $args );

}

/**
 * Compile JS fragments and enqueue compiled file.
 *
 * This function should be used in a similar fashion to
 * {@link http://codex.wordpress.org/Function_Reference/wp_enqueue_script wp_enqueue_script()}.
 *
 * Fragments can be added to the compiler using {@see beans_compiler_add_fragment()}.
 *
 * @since 1.0.0
 *
 * @param string       $id        The compiler ID. Similar to the WordPress scripts $handle argument.
 * @param string|array $fragments File(s) absolute path. Internal or external file(s) url accepted but may increase compiling time.
 * @param array        $args      {
 *      Optional. Array of arguments used by the compiler.
 *
 * 		@type array $depedencies An array of registered handles this script depends on. Default false.
 * 		@type bool  $in_footer   Whether to enqueue the script before </head> or before </body>. Default false.
 * 		@type bool  $minify_js   Whether the JavaScript should be minified or not. Be aware that minifying
 * 		      					 the JavaScript can considerably slow down the process of compiling files.
 * 		      					 Default false.
 * }
 */
function beans_compile_js_fragments( $id, $fragments, $args = array() ) {

	if ( empty( $fragments ) ) {
		return false;
	}

	$params = array(
		'id'        => $id,
		'type'      => 'script',
		'format'    => 'js',
		'fragments' => (array) $fragments,
	);

	new _Beans_Compiler( $params + $args );

}

/**
 * Add CSS, LESS or JS fragments to a compiler.
 *
 * This function should be used in a similar fashion to
 * {@link http://codex.wordpress.org/Function_Reference/wp_enqueue_script wp_enqueue_script()}.
 *
 * @since 1.0.0
 *
 * @param string       $id        The compiler ID. Similar to the WordPress scripts $handle argument.
 * @param string|array $fragments File(s) absolute path. Internal or external file(s) url accepted but may increase compiling time.
 * @param string       $format    Compiler format the fragments should be added to. Accepts 'css',
 *                                'less' or 'js'.
 */
function beans_compiler_add_fragment( $id, $fragments, $format ) {

	if ( empty( $fragments ) ) {
		return false;
	}

	global $_beans_compiler_added_fragments;

	foreach ( (array) $fragments as $key => $fragment ) {

		// Stop here if the format isn't valid.
		if ( ! isset( $_beans_compiler_added_fragments[ $format ] ) ) :
			continue;

		// Register new compiler id if it doesn't exist and add fragment.
		elseif ( ! isset( $_beans_compiler_added_fragments[ $format ][ $id ] ) ) :
			$_beans_compiler_added_fragments[ $format ][ $id ] = array( $fragment );

		// Add fragment to existing compiler.
		else :
			$_beans_compiler_added_fragments[ $format ][ $id ][] = $fragment;

		endif;

	}

}

/**
 * Flush cached compiler files.
 *
 * Each compiler has its own folder which contains the cached CSS and JS files. Cached files format
 * can be specified if needed.
 *
 * @since 1.0.0
 *
 * @param string      $id          The compiler ID. Similar to the WordPress scripts $handle argument.
 * @param string|bool $file_format Optional. Define which files format should be removed. Both CSS and JS
 *                                 files will be removed if set to false. Accepts 'false', 'css' or 'js'.
 * @param bool        $admin       Optional. Whether it is an admin compiler or not.
 */
function beans_flush_compiler( $id, $file_format = false, $admin = false ) {

	static $beans_flushed = false;

	$cache_dir = beans_get_compiler_dir( $admin );

	// Always flush beans global chache.
	if ( ! $beans_flushed ) {

		$beans_flushed = true;

		beans_flush_compiler( 'beans', $file_format, $admin );

	}

	$dir = trailingslashit( $cache_dir )  . $id;

	// Stop here if directory doesn't exist.
	if ( ! is_dir( $dir ) ) {
		return;
	}

	// Remove only specified format files.
	if ( $file_format ) {

		$items = scandir( $dir );
		unset( $items[0], $items[1] );

		foreach ( $items as $item ) {

			if ( false !== stripos( $item, '.' . $file_format ) ) {
				@unlink( trailingslashit( $dir ) . $item );
			}
		}
	} else { // Remove all format files.

		beans_remove_dir( $dir );

	}

}

/**
 * Flush admin cached compiler files.
 *
 * This function is a shortcut of {@see beans_flush_compiler()}.
 *
 * @since 1.0.0
 *
 * @param string      $id          The compiler ID. Similar to the WordPress scripts $handle argument.
 * @param string|bool $file_format Optional. Define which files format should be removed. Both CSS and JS
 *                                 files will be removed if set to false. Accepts 'false', 'css' or 'js'.
 */
function beans_flush_admin_compiler( $id, $file_format = false ) {

	beans_flush_compiler( $id, $file_format, true );

}

/**
 * Get beans compiler directory.
 *
 * @since 1.0.0
 *
 * @return string Compiler directory.
 */
function beans_get_compiler_dir( $admin = false ) {

	$wp_upload_dir = wp_upload_dir();
	$suffix = $admin ? 'beans/admin-compiler/' : 'beans/compiler/';

	/**
	 * Deprecated. Filter the beans compiler directory.
	 *
	 * This filter is deprecated for security and compatibility purposes.
	 *
	 * @since 1.0.0
	 * @deprecated 1.3.0
	 */
	$deprecated = apply_filters( 'beans_compiler_dir', false, $admin );

	return wp_normalize_path( trailingslashit( $wp_upload_dir['basedir'] ) . $suffix );

}

/**
 * Get beans compiler url.
 *
 * @since 1.3.0
 *
 * @return string Compiler directory.
 */
function beans_get_compiler_url( $admin = false ) {

	$wp_upload_dir = wp_upload_dir();
	$suffix = $admin ? 'beans/admin-compiler/' : 'beans/compiler/';

	return trailingslashit( $wp_upload_dir['baseurl'] ) . $suffix;

}

/**
 * Check if development mode is enabled taking in consideration legacy constant.
 *
 * @ignore
 */
function _beans_is_compiler_dev_mode() {

	if ( defined( 'BEANS_COMPILER_DEV_MODE' ) ) {
		return BEANS_COMPILER_DEV_MODE;
	}

	return get_option( 'beans_dev_mode', false );

}

/**
 * Initialize added fragments global.
 *
 * @ignore
 */
global $_beans_compiler_added_fragments;

if ( ! isset( $_beans_compiler_added_fragments ) ) {
	$_beans_compiler_added_fragments = array(
		'css'  => array(),
		'less' => array(),
		'js'   => array(),
	);
}
