<?php
/**
 * Tests for beans_register_wp_customize_options()
 *
 * @package Beans\Framework\Tests\Integration\API\WPCustomize
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\WPCustomize;

use Beans\Framework\Tests\Unit\API\WPCustomize\Includes\WP_Customize_Test_Case;
use Brain\Monkey;
use Mockery;

require_once __DIR__ . '/includes/class-wp-customize-test-case.php';

/**
 * Class Tests_BeansRegisterWPCustomizeOptions
 *
 * @package Beans\Framework\Tests\Unit\API\WPCustomize
 * @group   api
 * @group   api-wp-customize
 */
class Tests_BeansRegisterWPCustomizeOptions extends WP_Customize_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		Monkey\Functions\when( '_beans_pre_standardize_fields' )->alias( function( $fields ) {
			return $fields;
		});
	}

	/**
	 * Test beans_register_wp_customize_options() should return false when not in the WP Customizer .
	 */
	public function test_should_return_null_when_no_customizer() {
		Monkey\Functions\when( 'is_customize_preview' )->justReturn( false );

		$this->assertFalse( beans_register_wp_customize_options( array(), '', array() ) );
		$this->assertFalse( beans_register_wp_customize_options( array(), 'post_meta', array( 1, 2, 3 ) ) );
	}

	/**
	 * Test beans_register_wp_customize_options() should return false when there are no options.
	 */
	public function test_should_return_false_when_no_options() {
		Monkey\Functions\when( 'is_customize_preview' )->justReturn( true );
		Monkey\Functions\when( 'beans_register_fields' )->justReturn( false );

		$this->assertFalse( beans_register_wp_customize_options( array(), '', array() ) );
		$this->assertFalse( beans_register_wp_customize_options( array(), 'post_meta', array( 1, 2, 3 ) ) );
	}

	/**
	 * Test beans_register_wp_customize_options() should register the fields.
	 */
	public function test_should_register_fields() {
		$test_data = static::$test_data['single_fields'];

		$mocked_wp_customize = Mockery::mock( 'WP_Customize_Manager' );
		global $wp_customize;
		$wp_customize = $mocked_wp_customize; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited -- Limited to test function scope.
		$mocked_wp_customize->shouldReceive( 'get_section' )->andReturn( true );

		Monkey\Functions\when( 'is_customize_preview' )->justReturn( true );
		Monkey\Functions\when( 'beans_get_fields' )->justReturn( array() );
		Monkey\Functions\when( 'beans_add_attribute' )->justReturn( array() );
		Monkey\Functions\when( 'beans_register_fields' )->justReturn( true );

		$this->assertNull( beans_register_wp_customize_options( $test_data['fields'], $test_data['section'], $test_data['args'] ) );
	}
}
