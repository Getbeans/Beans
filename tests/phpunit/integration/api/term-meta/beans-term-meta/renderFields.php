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
	 * Tests render_fields() should configure the correct beans actions and attributes when called.
	 */
	public function test_render_fields_should_configure_correct_beans_actions_and_attributes_when_called() {
		global $_beans_registered_actions;

		set_current_screen( 'edit' );
		$_POST['taxonomy'] = 'category';
		beans_register_term_meta( static::$test_data['fields'], 'category', 'tm-beans' );

		$term_meta = new _Beans_Term_Meta( 'tm-beans' );

		ob_start();
		$term_meta->render_fields();
		ob_get_clean();

		$this->assertArrayHasKey( 'beans_field_label', $_beans_registered_actions['removed'] );
		$this->assertArrayHasKey( 'beans_field_description', $_beans_registered_actions['modified'] );
		// $this->assertEquals( 10, has_filter( 'beans_field_description_markup', array( '\\_Beans_Anonymous_Filters', 'callback' ) ) );
		// $this->assertEquals( 10, has_filter( 'beans_field_description_attribute', array( \\_Beans_Anonymous_Filters', 'callback' ) ) );
	}

	/**
	 * Tests render_fields() should output field html when called.
	 */
	public function test_render_fields_should_output_field_html_when_called() {
		// Register beans actions to render fields.
		beans_add_smart_action( 'beans_field_radio', 'beans_field_radio' );
		beans_add_smart_action( 'beans_field_checkbox', 'beans_field_checkbox' );
		beans_add_smart_action( 'beans_field_text', 'beans_field_text' );

		set_current_screen( 'edit' );
		$_POST['taxonomy'] = 'category';
		beans_register_term_meta( static::$test_data['fields'], 'category', 'tm-beans' );

		$term_meta = new _Beans_Term_Meta( 'tm-beans' );

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
