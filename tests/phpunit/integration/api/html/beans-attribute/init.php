<?php
/**
 * Tests for add() method of the _Beans_Attribute.
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\HTML;

use _Beans_Attribute;
use Beans\Framework\Tests\Integration\API\HTML\Includes\HTML_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-html-test-case.php';

/**
 * Class Tests_Beans_Attribute_Init
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_Beans_Attribute_Init extends HTML_Test_Case {

	/**
	 * Test init() should return null when the method does not exist.
	 */
	public function test_should_return_null_when_method_does_not_exist() {
		$instance = new _Beans_Attribute( 'foo', 'data-test' );
		$this->assertNull( $instance->init( 'does_not_exist' ) );
		$this->assertFalse( has_filter( 'foo_attributes', array( $instance, 'does_not_exist' ), 10 ) );
	}

	/**
	 * Test init() should return register the callback when method exists.
	 */
	public function test_should_register_the_callback_when_method_exists() {
		$instance = new _Beans_Attribute( 'foo', 'data-test' );

		$this->assertSame( $instance, $instance->init( 'add' ) );
		$this->assertSame( 10, has_filter( 'foo_attributes', array( $instance, 'add' ), 10 ) );

		// Clean up.
		remove_filter( 'foo_attributes', array( $instance, 'add' ) );
	}
}
