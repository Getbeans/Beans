<?php
/**
 * Tests for beans_register_wp_customize_options()
 *
 * @package Beans\Framework\Tests\Integration\API\WPCustomize
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\WPCustomize;

use Beans\Framework\Tests\Integration\API\WPCustomize\Includes\WP_Customize_Test_Case;
use WP_Customize_Manager;

require_once __DIR__ . '/includes/class-wp-customize-test-case.php';
require_once dirname( dirname( dirname( getcwd() ) ) ) . '/wp-includes/class-wp-customize-manager.php';

/**
 * Class Tests_BeansRegisterWPCustomizeOptions
 *
 * @package Beans\Framework\Tests\Integration\API\WPCustomize
 * @group   api
 * @group   api-wp-customize
 */
class Tests_BeansRegisterWPCustomizeOptions extends WP_Customize_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		global $wp_customize;
		$this->wp_customize = new WP_Customize_Manager();
		$wp_customize       = $this->wp_customize; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited -- Limited to test function scope.
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {
		global $wp_customize;
		$wp_customize = null; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited -- Limited to test function scope.

		parent::tearDown();
	}

	/**
	 * Test beans_register_wp_customize_options() should return false when not in the WP Customizer .
	 */
	public function test_should_return_null_when_no_customizer() {
		$test_data = static::$test_data['single_fields'];

		$this->assertFalse( beans_register_wp_customize_options( array(), '', array() ) );
		$this->assertFalse( beans_register_wp_customize_options( array(), 'post_meta', array( 1, 2, 3 ) ) );
		$this->assertFalse( beans_register_wp_customize_options( $test_data['fields'], $test_data['section'], $test_data['args'] ) );
	}

	/**
	 * Test beans_register_wp_customize_options() should return false when there are no options.
	 */
	public function test_should_return_false_when_no_options() {
		$this->wp_customize->start_previewing_theme();

		$this->assertFalse( beans_register_wp_customize_options( array(), '', array() ) );
		$this->assertFalse( beans_register_wp_customize_options( array(), 'post_meta', array( 1, 2, 3 ) ) );
	}

	/**
	 * Test beans_register_wp_customize_options() should register the fields.
	 */
	public function test_should_register_fields() {
		$this->wp_customize->start_previewing_theme();

		$test_data = static::$test_data['single_fields'];

		beans_register_wp_customize_options( $test_data['fields'], $test_data['section'], $test_data['args'] );

		// Check what was registered.
		$registered = $this->get_reflective_property_value( 'registered' );
		$this->assertArrayHasKey( $test_data['section'], $registered['wp_customize'] );

		foreach ( $test_data['fields'] as $index => $field ) {
			$expected = $this->merge_field_with_default( $field );
			$this->assertSame( $expected, $registered['wp_customize']['tm-beans-customizer'][ $index ] );
		}
	}
}
