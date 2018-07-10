<?php
/**
 * View file for the page's HTML.
 *
 * @package Beans\Framework\API\Options
 *
 * @since   1.0.0
 * @since   1.5.0 Moved to view file.
 */

// phpcs:disable Generic.WhiteSpace.ScopeIndent.Incorrect, Generic.WhiteSpace.ScopeIndent.IncorrectExact -- View file is indented for HTML structure.
?>

<form action="" method="post" class="bs-options" data-page="<?php echo esc_attr( beans_get( 'page' ) ); ?>">
	<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
	<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
	<input type="hidden" name="beans_options_nonce" value="<?php echo esc_attr( wp_create_nonce( 'beans_options_nonce' ) ); ?>" />
	<div class="metabox-holder<?php echo $column_class ? esc_attr( $column_class ) : ''; ?>">
		<?php
		do_meta_boxes( $page, 'normal', null );

		if ( $column_class ) {
			do_meta_boxes( $page, 'column', null );
		}
		?>
	</div>
	<p class="bs-options-form-actions">
		<input type="submit" name="beans_save_options" value="<?php esc_attr_e( 'Save', 'tm-beans' ); ?>" class="button-primary">
		<input type="submit" name="beans_reset_options" value="<?php esc_attr_e( 'Reset', 'tm-beans' ); ?>" class="button-secondary">
	</p>
</form>
