<?php
/**
 * Deprecated utility functions.
 *
 * @package API\Utilities
 */


/**
 * Deprecated. Sanatize HTML attributes from array to string.
 *
 * This functions has been replaced with {@see beans_sanitize_attributes()} due to a typography mistake.
 *
 * @since 1.0.0
 * @deprecated 1.3.1
 *
 * @param array $attributes The array key defines the attribute name and the array value define the
 *                          attribute value.
 *
 * @return string The sanatized attributes.
 */
function beans_sanatize_attributes( $attributes ) {

	_deprecated_function( __FUNCTION__, '1.3.1', 'beans_sanitize_attributes()' );

	return beans_sanitize_attributes( $attributes );

}