<?php
/**
 * View file for the term meta nonce.
 *
 * @package Beans\Framework\API\Term_Meta
 *
 * @since   1.0.0
 * @since   1.5.0 Moved to view file.
 */

?>
<input type="hidden" name="beans_term_meta_nonce" value="<?php echo esc_attr( wp_create_nonce( 'beans_term_meta_nonce' ) ); ?>" />
