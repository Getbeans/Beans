<?php
/**
 * The Beans Post Meta component extends the Beans Fields and make it easy add fields to any Post Type.
 *
 * @package API\Post_Meta
 */

/**
 * Register Post Meta.
 *
 * This function should only be invoked through the 'admin_init' action.
 *
 * @since 1.0.0
 *
 * @param array  $fields {
 *      Array of fields to register.
 *
 * 		@type string $id          A unique id used for the field. This id will also be used to save the value in
 * 		      					  the database.
 * 		@type string $type 		  The type of field to use. Please refer to the Beans core field types for more
 * 		      					  information. Custom field types are accepted here.
 *      @type string $label 	  The field label. Default false.
 *      @type string $description The field description. The description can be truncated using <!--more-->
 *            					  as a delimiter. Default false.
 *      @type array  $attributes  An array of attributes to add to the field. The array key defines the
 *            					  attribute name and the array value defines the attribute value. Default array.
 *      @type mixed  $default     The default field value. Default false.
 *      @type array  $fields      Must only be used for 'group' field type. The array arguments are similar to the
 *            					  {@see beans_register_fields()} $fields arguments.
 *      @type bool   $db_group    Must only be used for 'group' field type. Defines whether the group of fields
 *            					  registered should be saved as a group in the database or as individual
 *            					  entries. Default false.
 * }
 * @param array  $post_types Array of post type for which the post meta should be registered.
 * @param string $section    A section id to define the group of fields.
 * @param array  $args {
 *      Optional. Array of arguments used to register the fields.
 *
 * 		@type string $title    The metabox Title. Default 'Undefined'.
 * 		@type string $context  Where on the page the metabox should be shown
 * 		      				   ('normal', 'advanced', or 'side'). Default 'normal'.
 * 		@type int    $priority The priority within the context where the boxes should show
 * 		      				   ('high', 'core', 'default' or 'low'). Default 'high'.
 * }
 *
 * @return bool True on success, false on failure.
 */
function beans_register_post_meta( array $fields, $post_types, $section, $args = array() ) {

	$fields = apply_filters( "beans_post_meta_fields_{$section}", _beans_pre_standardize_fields( $fields ) );
	$post_types = apply_filters( "beans_post_meta_post_types_{$section}", $post_types );

	// Stop here if the current page isn't concerned.
	if ( !_beans_is_admin_post_type( $post_types ) || !is_admin() )
		return;

	// Stop here if the field can't be registered.
	if ( !beans_register_fields( $fields, 'post_meta', $section ) )
		return false;

	// Load the class only if this function is called to prevent unnecessary memory usage.
	require_once( BEANS_API_COMPONENTS_PATH . 'post-meta/class.php' );

	new _Beans_Post_Meta( $section, $args );

}


/**
 * Check if the current screen is a given post type.
 *
 * @ignore
 */
function _beans_is_admin_post_type( $post_types ) {

	// Check if it is a new post and treat it as such.
	if ( stripos( $_SERVER['REQUEST_URI'], 'post-new.php' ) !== false ) {

		if ( !$current_post_type = beans_get( 'post_type' ) )
			return false;

	} else {

		// Try to get id from $_GET.
		if ( $id = beans_get( 'post' ) )
			$post_id = $id;
		// Try to get id from $_POST.
		elseif ( $id = beans_post( 'post_ID' ) )
			$post_id = $id;

		if ( !isset( $post_id ) )
			return false;

		$current_post_type = get_post_type( $post_id );

	}

	if ( $post_types === true )
		return true;

	if ( in_array( $current_post_type, (array) $post_types ) )
		return true;

	// Support post ids.
	if ( isset( $post_id ) && in_array( $post_id, (array) $post_types ) )
		return true;

	return false;

}