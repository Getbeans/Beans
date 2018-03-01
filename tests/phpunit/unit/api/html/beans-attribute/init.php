<?php
/**
 * Tests for add() method of the _Beans_Attribute.
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\HTML;

use _Beans_Attribute;
use Beans\Framework\Tests\Unit\API\HTML\Includes\HTML_Test_Case;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-html-test-case.php';

/**
 * Class Tests_Beans_Attribute_Init
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 * @group   unit-tests
 * @group   api
 */
class Tests_Beans_Attribute_Init extends HTML_Test_Case {

	/**
	 * Test init() should return null when the method does not exist.
	 */
	public function test_should_return_null_when_method_does_not_exist() {
		$instance = new _Beans_Attribute( 'foo', 'data-test' );
		$this->assertNull( $instance->init( 'does_not_exist' ) );
	}

	/**
	 * Test init() should return register the callback when method exists.
	 */
	public function test_should_register_the_callback_when_method_exists() {
		$instance = new _Beans_Attribute( 'foo', 'data-test' );

		Monkey\Functions\expect( 'beans_add_filter' )
			->with( 'foo_attributes', array( $instance, 'add' ) )
			->once();

		$this->assertSame( $instance, $instance->init( 'add' ) );
	}
}
