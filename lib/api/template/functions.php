<?php
/**
 * The Beans Templates API allows to load Beans template files as well as loading the entire document.
 *
 * @package API\Templates
 */

/**
 * Load the entire document.
 *
 * This function is the root of Beans's framework hierarchy. It must be called from a primary template file
 * (e.g. page.php) and must be the last function to be called. All modifications must be done before calling
 * this function. This includes modifying markup, attributes, fragments, etc.
 *
 * @since 1.0.0
 */
function beans_load_document() {

	/**
	 * Fires before the document is loaded.
	 *
	 * @since 1.0.0
	 */
	do_action( 'beans_before_load_document' );

		/**
		 * Fires when the document loads.
		 *
		 * This hook is the root of Beans's framework hierarchy. It all starts here!
		 *
		 * @since 1.0.0
		 */
		do_action( 'beans_load_document' );

	/**
	 * Fires after the document is loaded.
	 *
	 * @since 1.0.0
	 */
	do_action( 'beans_after_load_document' );

}


/**
 * Load Beans secondary template file.
 *
 * This function loads Beans's default template file. It must be called from a secondary template file
 * (e.g. comments.php) and must be the last function to be called. All modifications must be done before calling
 * this function. This includes modifying markup, attributes, fragments, etc.
 *
 * The default template files contain the hook on which the fragments are attached to. Bypassing this function
 * will completely remove the default content.
 *
 * @since 1.0.0
 *
 * @param string $file The filename of the secondary template files. __FILE__ is usually to argument to pass.
 *
 * @return bool False if file isn't found.
 */
function beans_load_default_template( $file ) {

	$file = BEANS_STRUCTURE_PATH . basename( $file );

	if ( !file_exists( $file ) )
		return false;

	require_once( $file );

}


/**
 * Load fragment file.
 *
 * This function can be short-circuited.
 *
 * @since 1.0.0
 *
 * @param string $slug The file name to include without extension.
 *
 * @return bool True on success, false on failure.
 */
function beans_load_fragment_file( $slug ) {

	/**
	 * Filter whether to load a fragment or not.
	 *
	 * The dynamic portion of the hook name, $slug, refers to the file name without extension. Passing a truthy
	 * value to the filter will short-circuit loading the fragment.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $pre True to short-circuit, False to let the function run.
	 */
	if ( apply_filters( 'beans_pre_load_fragment_' . $slug, false ) )
		return false;

	// Stop here if fragment file doesn't exists.
	if ( !file_exists( BEANS_FRAGMENTS_PATH . $slug . '.php' ) )
		return false;

	require_once( BEANS_FRAGMENTS_PATH . $slug . '.php' );

	return true;

}


/**
 * wp_list_comments callback function.
 *
 * This function adds the hooks to which the comment template part is attached to.
 * are attached.
 *
 * @since 1.0.0
 */
function beans_comment_callback( $comment, $args, $depth ) {

	global $comment;

	// Add args and depth to comment global.
	$comment->args = $args;
	$comment->depth = $depth;

	// Don't allow overwrite.
	echo '<li id="comment-' . get_comment_ID() . '" ' . comment_class( empty( $args['has_children'] ) ? '' : 'parent', null, null, false ) .'>';

		/**
		 * Fires in comment structural HTML.
		 *
		 * @since 1.0.0
		 */
		do_action( 'beans_comment' );

	// Don't close </li> tag.

}
