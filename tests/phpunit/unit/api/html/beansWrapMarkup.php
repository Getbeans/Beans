<?php
/**
 * Tests for beans_wrap_markup().
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\HTML;

use Beans\Framework\Tests\Unit\api\html\fixtures\Anonymous_Action_Stub;
use Beans\Framework\Tests\Unit\API\HTML\Includes\HTML_Test_Case;
use Brain\Monkey;

require_once __DIR__ . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansWrapMarkup
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansWrapMarkup extends HTML_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		require_once __DIR__ . '/fixtures/class-anonymous-action-stub.php';
	}

	/**
	 * Test beans_wrap_markup() should register beans_open_markup() to the given ID's '_before_markup' hook.
	 */
	public function test_should_register_beans_open_markup_to_given_id_before_markup_hook() {
		// Setup the tests.
		$args                  = array(
			'beans_open_markup',
			array(
				1 => 'new_foo',
				2 => 'div',
				3 => array( 'class' => 'test-wrap' ),
			),
		);
		$anonymous_action_mock = new Anonymous_Action_Stub( 'foo_before_markup', $args, 9999 );
		Monkey\Functions\expect( '_beans_add_anonymous_action' )
			->once()
			->with( 'foo_before_markup', $args, 9999 )
			->andReturn( $anonymous_action_mock );

		// Run the tests.
		$this->assertTrue( beans_wrap_markup( 'foo', 'new_foo', 'div', array( 'class' => 'test-wrap' ) ) );
		$this->assertTrue( has_action( 'foo_before_markup', array( $anonymous_action_mock, 'callback' ) ) );

		// Clean up.
		remove_action( 'foo_before_markup', array( $anonymous_action_mock, 'callback' ), 9999 );
	}

	/**
	 * Test beans_wrap_markup() should register beans_close_markup() to the given ID's '_after_markup' hook.
	 */
	public function test_should_register_beans_close_markup_to_given_id_after_markup_hook() {
		// Setup the tests.
		$args                  = array(
			'beans_close_markup',
			array(
				1 => 'new_foo',
				2 => 'div',
			),
		);
		$anonymous_action_mock = new Anonymous_Action_Stub( 'foo_after_markup', $args, 1 );
		Monkey\Functions\expect( '_beans_add_anonymous_action' )
			->once()
			->with( 'foo_after_markup', $args, 1 )
			->andReturn( $anonymous_action_mock );

		// Run the tests.
		$this->assertTrue( beans_wrap_markup( 'foo', 'new_foo', 'div', array( 'class' => 'test-wrap' ) ) );
		$this->assertTrue( has_action( 'foo_after_markup', array( $anonymous_action_mock, 'callback' ) ) );

		// Clean up.
		remove_action( 'foo_before_markup', array( $anonymous_action_mock, 'callback' ), 1 );
	}

	/**
	 * Test beans_wrap_markup() should not pass the given attributes to anonymous action.
	 */
	public function test_should_not_pass_attributes_for_after_markup_hook() {
		// Setup the tests.
		$args                  = array(
			'beans_close_markup',
			array(
				1 => '',
				2 => 'div',
			),
		);
		$anonymous_action_mock = new Anonymous_Action_Stub( 'no_atts_after_markup', $args, 1 );
		Monkey\Functions\expect( '_beans_add_anonymous_action' )
			->once()
			->with( 'no_atts_after_markup', $args, 1 )
			->andReturn( $anonymous_action_mock );

		// Run the tests.
		$this->assertTrue( beans_wrap_markup( 'no_atts', '', 'div', array( 'class' => 'test-wrap' ) ) );
		$this->assertSame( $args, $anonymous_action_mock->callback );

		// Clean up.
		remove_action( 'no_atts_after_markup', array( $anonymous_action_mock, 'callback' ), 1 );
	}

	/**
	 * Test beans_wrap_markup() should pass the extra arguments to the anonymous action for the given ID's '_before_markup' hook.
	 */
	public function test_should_pass_extra_arguments_for_before_markup_hook() {
		// Setup the tests.
		$args                  = array(
			'beans_open_markup',
			array(
				1 => '',
				2 => 'div',
				3 => array( 'class' => 'test-wrap' ),
				4 => 47,
				5 => 'Beans Rocks!',
			),
		);
		$anonymous_action_mock = new Anonymous_Action_Stub( 'extra_args_before_markup', $args, 9999 );
		Monkey\Functions\expect( '_beans_add_anonymous_action' )
			->once()
			->with( 'extra_args_before_markup', $args, 9999 )
			->andReturn( $anonymous_action_mock );

		// Run the tests.
		$this->assertTrue( beans_wrap_markup( 'extra_args', '', 'div', array( 'class' => 'test-wrap' ), 47, 'Beans Rocks!' ) );
		$this->assertSame( $args, $anonymous_action_mock->callback );

		// Clean up.
		remove_action( 'extra_args_before_markup', array( $anonymous_action_mock, 'callback' ), 9999 );
	}

	/**
	 * Test beans_wrap_markup() should pass the extra arguments to the anonymous action for the given ID's '_after_markup' hook.
	 */
	public function test_should_pass_extra_arguments_for_after_markup_hook() {
		// Setup the tests.
		$args                  = array(
			'beans_close_markup',
			array(
				1 => '',
				2 => 'div',
				4 => 'Beans Rocks!',
				5 => 47,
			),
		);
		$anonymous_action_mock = new Anonymous_Action_Stub( 'extra_args_after_markup', $args, 1 );
		Monkey\Functions\expect( '_beans_add_anonymous_action' )
			->once()
			->with( 'extra_args_after_markup', $args, 1 )
			->andReturn( $anonymous_action_mock );

		// Run the tests.
		$this->assertTrue( beans_wrap_markup( 'extra_args', '', 'div', array( 'class' => 'test-wrap' ), 'Beans Rocks!', 47 ) );
		$this->assertSame( $args, $anonymous_action_mock->callback );

		// Clean up.
		remove_action( 'extra_args_after_markup', array( $anonymous_action_mock, 'callback' ), 1 );
	}
}
