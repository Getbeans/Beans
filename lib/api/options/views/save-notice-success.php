<?php
/**
 * View file for the save notice when the settings were successfully saved.
 *
 * @package Beans\Framework\API\Options
 *
 * @since   1.0.0
 * @since   1.5.0 Moved to view file.
 */

// phpcs:disable Generic.WhiteSpace.ScopeIndent.Incorrect, Generic.WhiteSpace.ScopeIndent.IncorrectExact -- View file is indented for HTML structure.
?>

<div id="message" class="updated">
	<p><?php esc_html_e( 'Settings saved successfully!', 'tm-beans' ); ?></p>
</div>
