<?php
/**
 * Tests for _Beans_Anonymous_Filters __construct() method.
 *
 * @package Beans\Framework\Tests\Unit\API\Filters
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Filters;

use _Beans_Anonymous_Filters;
use Beans\Framework\Tests\Unit\Test_Case;

/**
 * Class Tests_Beans_Anonymous_Filters_Construct.
 *
 * @package Beans\Framework\Tests\Unit\API\Filters
 * @group   unit-tests
 * @group   api
 */
class Tests_Beans_Anonymous_Filters_Construct extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . 'api/filters/class-beans-anonymous-filters.php';
	}

	/**
	 * Test _construct() should store callback.
	 */
	public function test_should_store_callback() {
		$object = new _Beans_Anonymous_Filters( 'do_foo', 'foo', 20 );

		$this->assertSame( 'foo', $object->value_to_return );

		// Clean up.
		remove_action( 'do_foo', array( $object, 'callback' ), 20 );
	}

	/**
	 * Test _Beans_Anonymous_Filters() should register callback to the given hook.
	 */
	public function test_should_register_callback_to_hook() {
		$object = new _Beans_Anonymous_Filters( 'do_foo', false, 20 );

		$this->assertTrue( has_filter( 'do_foo', array( $object, 'callback' ) ) !== false );

		// Clean up.
		remove_action( 'do_foo', array( $object, 'callback' ), 20 );
	}
}
