<?php
/**
 * Tests for beans_load_default_template()
 *
 * @package Beans\Framework\Tests\Integration\API\Template
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Template;

use WP_UnitTestCase;

/**
 * Class Tests_BeansLoadDefaultTemplate
 *
 * @package Beans\Framework\Tests\Integration\API\Template
 * @group   integration-tests
 * @group   api
 */
class Tests_BeansLoadDefaultTemplate extends WP_UnitTestCase {

	/**
	 * Test beans_load_default_template() should return false when the template does not exist.
	 */
	public function test_should_return_false_when_template_does_not_exist() {
		$this->assertFileNotExists( BEANS_STRUCTURE_PATH . 'does-not-exist.php' );
		$this->assertFalse( beans_load_default_template( 'path/to/does-not-exist.php' ) );

		$this->assertFileNotExists( BEANS_STRUCTURE_PATH . basename( __FILE__ ) );
		$this->assertFalse( beans_load_default_template( __FILE__ ) );
	}

	/**
	 * Test beans_load_default_template() should return true after loading the given structure.
	 */
	public function test_should_return_true_after_loading_structure() {
		// Check with an invalid path to the structure.
		$file = '/path/to/structure/content.php';
		$this->assertFileExists( BEANS_STRUCTURE_PATH . basename( $file ) );
		ob_start();
		$this->assertTrue( beans_load_default_template( $file ) );
		$this->assertStringStartsWith( '<div class="tm-content"', ob_get_clean() );

		// Check with an absolute path to the structure.
		$file = __DIR__ . 'fixtures/structure/header.php';
		$this->assertFileExists( BEANS_STRUCTURE_PATH . basename( $file ) );
		ob_start();
		$this->assertTrue( beans_load_default_template( $file ) );
		$this->assertStringStartsWith( '<!DOCTYPE html>', ob_get_clean() );
	}
}
