<?php
/**
 * View file for rendering a term meta field.
 *
 * @package Beans\Framework\API\Term_Meta
 *
 * @since   1.0.0
 * @since   1.5.0 Moved to view file.
 */

?>
<tr class="form-field">
	<th scope="row">
		<?php beans_field_label( $field ); ?>
	</th>
	<td>
		<?php beans_field( $field ); ?>
	</td>
</tr>
