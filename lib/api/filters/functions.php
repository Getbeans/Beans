<?php
/**
 * Beans Filters extends WordPress Filters by registering sub filters if it is told to do so.
 *
 * @package API\Filters
 */

/**
 * Hooks a function or method to a specific filter action.
 *
 * This function is similar to {@link http://codex.wordpress.org/Function_Reference/add_filter add_filter()}
 * with the exception that it accepts a $callback argument which is used to automatically create an
 * anonymous function.
 *
 * @since 1.0.0
 *
 * @param string   $id       The filter ID.
 * @param callback $callback The name of the function you wish to be called. Inline content will automatically
 *                           create an anonymous function.
 * @param int      $priority Optional. Used to specify the order in which the functions
 *                           associated with a particular action are executed. Default 10.
 *                           Lower numbers correspond with earlier execution,
 *                           and functions with the same priority are executed
 *                           in the order in which they were added to the action.
 * @param int      $args     Optional. The number of arguments the function accepts. Default 1.
 *
 * @return bool Will always return true.
 */
function beans_add_filter( $id, $callback, $priority = 10, $args = 1 ) {

	if ( is_callable( $callback ) )
		return add_filter( $id, $callback, $priority, $args );

	return _beans_add_anonymous_filter( $id, $callback, $priority, $args );

}


/**
 * Call the functions added to a filter hook.
 *
 * This function is similar to {@link http://codex.wordpress.org/Function_Reference/apply_filters apply_filters()}
 * with the exception of creating sub-filters if it is told to do so.
 *
 * Sub-filters must be set in square brackets as part of the filter id argument. Sub-filters are cascaded
 * in a similar way to CSS classes.
 *
 * @since 1.0.0
 *
 * @param string $id    A unique string used as a reference. Sub-hook(s) must be set in square brackets. Each sub-
 *                      hook will create a filter. For instance, 'hook[_sub_hook]' will create a
 *                      'hook_name' filter as well as a 'hook_sub_hook' filter. The id may contain
 *                      multiple sub hooks such as 'hook[_sub_hook][_sub_sub_hook]'.
 *                      In this case, four filters will be created 'hook', 'hook_sub_hook',
 *                      'hook_sub_sub_hook' and 'hook_sub_hook_sub_sub_hook'. Sub-hooks
 *                      always run the parent filter first, so a filter set to the parent will apply
 *                      to all sub-filters.
 * @param mixed  $value The value on which the filters hooked to <tt>$id</tt> are applied to it.
 * @param mixed  $var   Additional variables passed to the functions hooked to <tt>$id</tt>.
 *
 * @return mixed The filtered value after all hooked functions are applied to it.
 */
function beans_apply_filters( $id, $value ) {

	$args = func_get_args();

	// Return simple filter if no subfilter is set.
	if ( !preg_match_all( '#\[(.*?)\]#', $args[0], $matches ) )
		return call_user_func_array( 'apply_filters', $args );

	// Apply base filter.
	$base_args = $args;
	$base_args[0] = preg_replace( '#\[(.*?)\]#', '', $id );
	$filter = call_user_func_array( 'apply_filters', $base_args );

	// Apply sub filters.
	for ( $i = 0 ; $i < count( $matches[0] ) ; $i++ ) {

		$_id = str_replace( $matches[0][$i], $matches[1][$i], $id );
		$_id = preg_replace( '#\[(.*?)\]#', '', $_id );

		$args[0] = $_id;
		$args[1] = $filter;

		$filter = call_user_func_array( 'apply_filters', $args );

	}

	if ( !preg_match( '#\[.*\]\[.*\]#', $id ) )
		return $filter;

	// Apply combined sub filters.
	$args[0] = preg_replace( '#(\[|\])#', '', $id );
	$args[1] = $filter;

	return call_user_func_array( 'apply_filters', $args );

}


/**
 * Check if any filter has been registered for a hook.
 *
 * This function is similar to {@link http://codex.wordpress.org/Function_Reference/has_filters has_filters()}
 * with the exception of checking sub-filters if it is told to do so.
 *
 * @since 1.0.0
 *
 * @param string   $id       	  The filter ID.
 * @param callback|bool $callback Optional. The callback to check for. Default false.
 *
 * @return bool|int If $callback is omitted, returns boolean for whether the hook has
 *                  anything registered. When checking a specific function, the priority of that
 *                  hook is returned, or false if the function is not attached. When using the
 *                  $callback argument, this function may return a non-boolean value
 *                  that evaluates to false (e.g. 0), so use the === operator for testing the
 *                  return value.
 */
function beans_has_filters( $id, $callback = false ) {

	// Check simple filter if no subfilter is set.
	if ( !preg_match_all( '#\[(.*?)\]#', $id, $matches ) )
		return has_filter( $id, $callback );

	// Check combined sub filters.
	if ( has_filter( $id, $callback ) )
		return true;

	$matches[0] = array_reverse( $matches[0] );
	$matches[1] = array_reverse( $matches[1] );

	// Check sub filters.
	for ( $i = 0 ; $i < count( $matches[0] ) ; $i++ ) {

		$_id = str_replace( $matches[0][$i], $matches[1][$i], $id );

		if ( has_filter( $_id = preg_replace( '#\[(.*?)\]#', '', $_id ), $callback ) )
			return true;

	}

	return has_filter( preg_replace( '#\[(.*?)\]#', '', $id ), $callback );

}


/**
 * Add anonymous callback using a class since php 5.2 is still supported.
 *
 * @ignore
 */
function _beans_add_anonymous_filter( $id, $callback, $priority = 10, $args = 1 ) {

	require_once( BEANS_API_COMPONENTS_PATH . 'filters/class.php' );

	new _Beans_Anonymous_Filters( $id, $callback, $priority, $args );

}