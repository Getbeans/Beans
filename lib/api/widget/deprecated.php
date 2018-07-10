<?php
/**
 * Deprecated widget functions.
 *
 * @package Beans\Framework\API\Widget
 * @since 1.5.0
 */

/**
 * Deprecated. Display a widget area.
 *
 * This function has been replaced with {@see beans_get_widget_area_output()}.
 *
 * @since 1.0.0
 * @deprecated 1.5.0
 *
 * @param string $id The ID of the registered widget area.
 *
 * @return string|bool The output if a widget area was found and called. False if not found.
 */
function beans_widget_area( $id ) {
	_deprecated_function( __FUNCTION__, '1.5.0', 'beans_get_widget_area_output()' );

	return beans_get_widget_area_output( $id );
}
