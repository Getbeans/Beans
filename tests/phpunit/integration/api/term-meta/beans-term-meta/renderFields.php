<?php
/**
 * Tests for _Beans_Term_Meta::render_fields()
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
	 * Tests _Beans_Term_Meta::render_fields() should output field html.
	 */
	public function test_should_output_field_html() {

		// Register beans actions to render fields.
		beans_add_smart_action( 'beans_field_radio', 'beans_field_radio' );
		beans_add_smart_action( 'beans_field_checkbox', 'beans_field_checkbox' );
		beans_add_smart_action( 'beans_field_text', 'beans_field_text' );

		// Set WP to Edit Category screen.
		set_current_screen( 'edit' );
		$_POST['taxonomy'] = 'category';

		// Register test fields.
		beans_register_term_meta( static::$test_data['fields'], 'category', 'tm-beans' );

		// Call render_fields() method and capture output.
		$term_meta = new _Beans_Term_Meta( 'tm-beans' );
		ob_start();
		$term_meta->render_fields();
		$output = trim( ob_get_clean() );

		// Check output is as expected.
		$beans_theme_url = BEANS_THEME_URL;
		$expected_output = require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fixtures/test-field-outout-html.php';
		$this->assertEquals( $expected_output, $output );
	}
}
