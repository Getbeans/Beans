<?php
/**
 * Tests for register() method of the _Beans_Options.
 *
 * @package Beans\Framework\Tests\Unit\API\Options
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Options;

use _Beans_Options;
use Beans\Framework\Tests\Unit\API\Options\Includes\Options_Test_Case;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-options-test-case.php';

/**
 * Class Tests_Beans_Options_Register
 *
 * @package Beans\Framework\Tests\Unit\API\Options
 * @group   api
 * @group   api-options
 */
class Tests_Beans_Options_Register extends Options_Test_Case {

	/**
	 * Test register() should merge default arguments and set the property.
	 */
	public function test_should_merge_default_args_and_set_property() {
		$property = $this->get_reflective_property( 'args', '_Beans_Options' );

		$instance = new _Beans_Options();
		$instance->register( 'foo', array() );
		$this->assertSame( array(
			'title'   => 'Undefined',
			'context' => 'normal',
		), $property->getValue( $instance ) );

		$instance = new _Beans_Options();
		$instance->register( 'beans_test', array( 'title' => 'Beans Tests' ) );
		$this->assertSame( array(
			'title'   => 'Beans Tests',
			'context' => 'normal',
		), $property->getValue( $instance ) );
	}

	/**
	 * Test register() should register callback to the 'admin_enqueue_scripts' hook.
	 */
	public function test_should_register_callback_to_admin_enqueue_scripts_hook() {
		$instance = new _Beans_Options();
		$instance->register( 'beans_test', array( 'title' => 'Beans Tests' ) );

		$this->assertEquals( 10, has_action( 'admin_enqueue_scripts', array( $instance, 'enqueue_assets' ) ) );
	}

	/**
	 * Test register() should register meta box with WordPress.
	 */
	public function test_should_register_meta_box_with_wp() {
		$instance = new _Beans_Options();

		foreach ( static::$test_data as $option ) {
			Monkey\Functions\expect( 'beans_get' )->with( 'page' )->once()->andReturn( 'beans_settings' );

			// Register the option.
			$instance->register( $option['section'], $option['args'] );

			// Check that the metabox is registered with WordPress.
			global $wp_meta_boxes;
			$this->assertArrayHasKey( 'beans_settings', $wp_meta_boxes );
			$this->assertArrayHasKey( $option['args']['context'], $wp_meta_boxes['beans_settings'] );
			$this->assertArrayHasKey( 'default', $wp_meta_boxes['beans_settings'][ $option['args']['context'] ] );
			$this->assertArrayHasKey( $option['section'], $wp_meta_boxes['beans_settings'][ $option['args']['context'] ]['default'] );
			$this->assertSame( array(
				'id'       => $option['section'],
				'title'    => $option['args']['title'],
				'callback' => array( $instance, 'render_metabox' ),
				'args'     => null,
			), $wp_meta_boxes['beans_settings'][ $option['args']['context'] ]['default'][ $option['section'] ] );

			// Clean up.
			remove_action( 'admin_enqueue_scripts', array( $instance, 'enqueue_assets' ) );
		}
	}
}
