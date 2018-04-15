<?php
/**
 * Tests for add() method of _Beans_WP_Customize.
 *
 * @package Beans\Framework\Tests\Unit\API\WP-Customize
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\WPCustomize;

use Beans\Framework\Tests\Unit\API\WPCustomize\Includes\WP_Customize_Test_Case;
use Brain\Monkey;
use Mockery;

require_once dirname( __DIR__ ) . '/includes/class-wp-customize-test-case.php';

/**
 * Class Tests_Beans_Options_Actions
 *
 * @package Beans\Framework\Tests\Unit\API\Options
 * @group   api
 * @group   api-wp-customize
 */
class Tests_Beans_WP_Customize_Add extends WP_Customize_Test_Case {

	/**
	 * Test add().
	 */
	public function test_add_adds() {
		$test_data = static::$test_data['single_fields'];

		$mocked_wp_customize = Mockery::mock( 'WP_Customize_Manager' );
		global $wp_customize;
		$wp_customize = $mocked_wp_customize; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited -- Limited to test function scope.
		$mocked_wp_customize->shouldReceive( 'get_section' )->andReturn( true );

		Monkey\Functions\expect( 'beans_get_fields' )
		->with( 'wp_customize', $test_data['section'] )
		->once()
		->ordered()
		->andReturn( array() )
		->andAlsoExpectIt()
		->with( 'wp_customize', $test_data['section'] )
		->once()
		->ordered()
		->andReturn( array() );

		Monkey\Functions\expect( 'beans_add_attribute' )
		->with( 'beans_field_label', 'class', 'customize-control-title' )
		->once()
		->andReturn( true );

		$customizer = new \_Beans_WP_Customize( $test_data['section'], $test_data['args'] );
		$add        = $this->get_reflective_method( 'add' );

		$this->assertNull( $add->invoke( $customizer ) );
	}

	/**
	 * Test add_section().
	 */
	public function test_add_section() {
		$test_data = static::$test_data['single_fields'];

		$mocked_wp_customize = Mockery::mock( 'WP_Customize_Manager' );
		global $wp_customize;
		$wp_customize = $mocked_wp_customize; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited -- Limited to test function scope.
		$mocked_wp_customize->shouldReceive( 'get_section' )->andReturn( true );

		Monkey\Functions\expect( 'beans_get_fields' )
		->with( 'wp_customize', $test_data['section'] )
		->once()
		->ordered()
		->andReturn( array() );

		Monkey\Functions\expect( 'beans_add_attribute' )
		->with( 'beans_field_label', 'class', 'customize-control-title' )
		->once()
		->andReturn( true );

		$customizer  = new \_Beans_WP_Customize( $test_data['section'], $test_data['args'] );
		$add_section = $this->get_reflective_method( 'add_section' );

		$this->assertNull( $add_section->invoke( $customizer, $wp_customize ) );
	}

	/**
	 * Test add_setting() calls beans_get().
	 */
	public function test_add_setting_calls_beans_get() {
		$test_data = static::$test_data['single_fields'];

		$mocked_wp_customize = Mockery::mock( 'WP_Customize_Manager' );
		global $wp_customize;
		$wp_customize = $mocked_wp_customize; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited -- Limited to test function scope.
		$mocked_wp_customize->shouldReceive( 'get_section', 'add_setting', 'add_section' )->andReturn( true );

		Monkey\Functions\expect( 'beans_get_fields' )
		->with( 'wp_customize', $test_data['section'] )
		->once()
		->ordered()
		->andReturn( array() );

		Monkey\Functions\expect( 'beans_get' )
		->once()
		->andReturn( 'default' );

		Monkey\Functions\expect( 'beans_add_attribute' )
		->with( 'beans_field_label', 'class', 'customize-control-title' )
		->once()
		->andReturn( true );

		$customizer  = new \_Beans_WP_Customize( $test_data['section'], $test_data['args'] );
		$add_setting = $this->get_reflective_method( 'add_setting' );

		$this->assertNull( $add_setting->invoke( $customizer, $wp_customize, $test_data['fields'] ) );
	}

	/**
	 * Test add_group_setting() with no grouped fields.
	 */
	public function test_add_group_setting_without_grouped_field() {
		$test_data = static::$test_data['single_fields'];

		$mocked_wp_customize = Mockery::mock( 'WP_Customize_Manager' );
		global $wp_customize;
		$wp_customize = $mocked_wp_customize; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited -- Limited to test function scope.
		$mocked_wp_customize->shouldReceive( 'get_section', 'add_setting', 'add_section' )->andReturn( true );

		Monkey\Functions\expect( 'beans_get_fields' )
		->with( 'wp_customize', $test_data['section'] )
		->once()
		->ordered()
		->andReturn( array() );

		Monkey\Functions\expect( 'beans_add_attribute' )
		->with( 'beans_field_label', 'class', 'customize-control-title' )
		->once()
		->andReturn( true );

		$customizer        = new \_Beans_WP_Customize( $test_data['section'], $test_data['args'] );
		$add_group_setting = $this->get_reflective_method( 'add_group_setting' );

		$this->assertNull( $add_group_setting->invoke( $customizer, $wp_customize, $test_data['fields'] ) );
	}

	/**
	 * Test add_group_setting()_with_group_fields().
	 */
	public function test_add_group_setting_with_grouped_fields() {
		$test_data = static::$test_data['group'];

		$mocked_wp_customize = Mockery::mock( 'WP_Customize_Manager' );
		global $wp_customize;
		$wp_customize = $mocked_wp_customize; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited -- Limited to test function scope.
		$mocked_wp_customize->shouldReceive( 'get_section', 'add_setting', 'add_section' )->andReturn( true );

		Monkey\Functions\expect( 'beans_get_fields' )
		->with( 'wp_customize', $test_data['section'] )
		->once()
		->ordered()
		->andReturn( array() );

		Monkey\Functions\expect( 'beans_get' )
		->times( 3 )
		->andReturn( 'default' );

		Monkey\Functions\expect( 'beans_add_attribute' )
		->with( 'beans_field_label', 'class', 'customize-control-title' )
		->once()
		->andReturn( true );

		$customizer        = new \_Beans_WP_Customize( $test_data['section'], $test_data['args'] );
		$add_group_setting = $this->get_reflective_method( 'add_group_setting' );

		$this->assertNull( $add_group_setting->invoke( $customizer, $wp_customize, $test_data['fields'] ) );
	}


	/**
	 * Test add_control().
	 */
	public function test_add_control() {
		$test_data = static::$test_data['single_fields'];

		$mocked_wp_customize = Mockery::mock( 'WP_Customize_Manager' );
		global $wp_customize;
		$wp_customize = $mocked_wp_customize; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited -- Limited to test function scope.
		$mocked_wp_customize->shouldReceive( 'get_section', 'add_setting', 'add_section', 'add_control' )->andReturn( true );

		$mocked_beans_customize_control = Mockery::mock( '_Beans_WP_Customize_Control' );

		Monkey\Functions\expect( 'beans_get_fields' )
		->with( 'wp_customize', $test_data['section'] )
		->once()
		->ordered()
		->andReturn( array() );

		Monkey\Functions\expect( 'beans_add_attribute' )
		->with( 'beans_field_label', 'class', 'customize-control-title' )
		->once()
		->andReturn( true );

		$customizer  = new \_Beans_WP_Customize( $test_data['section'], $test_data['args'] );
		$add_control = $this->get_reflective_method( 'add_control' );

		$this->assertNull( $add_control->invoke( $customizer, $wp_customize, $test_data['fields'] ) );
	}
}
