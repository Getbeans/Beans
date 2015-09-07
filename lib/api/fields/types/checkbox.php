<?php
/**
 * @package API\Fields\Types
 */

beans_add_smart_action( 'beans_field_checkbox', 'beans_field_checkbox' );

/**
 * Echo checkbox field type.
 *
 * @since 1.0.0
 *
 * @param array $field {
 *      For best practices, pass the array of data obtained using {@see beans_get_fields()}.
 *
 *      @type mixed  $value          The field value.
 *      @type string $name           The field name value.
 *      @type array  $attributes     An array of attributes to add to the field. The array key defines the
 *            					     attribute name and the array value defines the attribute value. Default array.
 *      @type mixed  $default        The default value. Default false.
 *      @type string $checkbox_label The field checkbox label. Default 'Enable'.
 * }
 */
function beans_field_checkbox( $field ) {

	$checked = $field['value'] ? ' checked="checked"' : null;

	echo '<input type="hidden" value="0" name="' . $field['name'] . '" />';

	echo '<input type="checkbox" name="' . $field['name'] . '" value="1" ' . $checked . ' ' . beans_sanatize_attributes( $field['attributes'] ) . ' />';

	if ( $checkbox_label = beans_get( 'checkbox_label', $field, 'Enable' ) )
		echo '<span class="bs-checkbox-label">' . $checkbox_label . '</span>';

}