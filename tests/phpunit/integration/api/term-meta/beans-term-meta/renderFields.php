<?php
/**
 * Tests for _beans_term_meta::render_fields()
 *
 * @package Beans\Framework\Tests\Integration\API\Term_Meta
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Term_Meta;

use Beans\Framework\Tests\Integration\API\Term_Meta\Includes\Beans_Term_Meta_Test_Case;
use _Beans_Term_Meta;

require_once BEANS_THEME_DIR . '/lib/api/term-meta/class-beans-term-meta.php';
require_once BEANS_THEME_DIR . '/lib/api/term-meta/functions-admin.php';
//require_once BEANS_THEME_DIR . '/lib/api/fields/class-beans-fields.php';
require_once dirname( __DIR__ ) . '/includes/class-beans-term-meta-test-case.php';

/**
 * Class Tests_BeanTermMeta_RenderFields
 *
 * @package Beans\Framework\Tests\Integration\API\Term_Meta
 * @group   api
 * @group   api-term-meta
 */
class Tests_BeansTermMeta_RenderFields extends Beans_Term_Meta_Test_Case {

	/**
	 * Tests render_fields() should output field html when called.
	 */
	public function test_render_fields_should_output_field_html_when_called() {
		// Register beans actions to render fields.
		beans_add_smart_action( 'beans_field_radio', 'beans_field_radio' );
		beans_add_smart_action( 'beans_field_checkbox', 'beans_field_checkbox' );
		beans_add_smart_action( 'beans_field_text', 'beans_field_text' );

		// Set WP state to admin and category tax.
		set_current_screen( 'edit' );
		$_POST['taxonomy'] = 'category';

		// Register term meta fields.
		beans_register_term_meta( static::$test_data['fields'], 'category', 'tm-beans' );

		// Create a term meta object.
		$term_meta = new _Beans_Term_Meta( 'tm-beans' );

		// Capture output of render_fields() method.
		ob_start();
		$term_meta->render_fields();
		$output = ob_get_clean();

		// Test printed radio field.
		$this->assertContains(
			'<input id="beans_layout_test_default_fallback" type="radio" name="beans_fields[beans_layout_test]" value="default_fallback"',
			$output
		);

		// Test printed checkbox field.
		$this->assertContains(
			'<input id="beans_checkbox_test" type="checkbox" name="beans_fields[beans_checkbox_test]" value="1" />',
			$output
		);

		// Test printed text field.
		$this->assertContains(
			'<input id="beans_text_test" type="text" name="beans_fields[beans_text_test]" value="Testing the text field." >',
			$output
		);
	}
}