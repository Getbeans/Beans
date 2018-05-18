<?php
/**
 * Test Case for the unit tests.
 *
 * @package Beans\Framework\Tests\Unit
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit;

use Beans\Framework\Tests\Test_Case_Trait;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * Abstract Class Test_Case
 *
 * @package Beans\Framework\Tests\Unit
 */
abstract class Test_Case extends TestCase {

	use Test_Case_Trait;

	/**
	 * When true, return the given path when doing wp_normalize_path().
	 *
	 * @var bool
	 */
	protected $just_return_path = false;

	/**
	 * Reset flag.
	 *
	 * @var bool
	 */
	protected $was_reset = false;

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();
		Monkey\setUp();

		Functions\when( 'wp_normalize_path' )->alias( function( $path ) {

			if ( true === $this->just_return_path ) {
				return $path;
			}

			$path = str_replace( '\\', '/', $path );
			$path = preg_replace( '|(?<=.)/+|', '/', $path );

			if ( ':' === substr( $path, 1, 1 ) ) {
				$path = ucfirst( $path );
			}

			return $path;
		} );

		Functions\when( 'wp_json_encode' )->alias( function( $array ) {
			return json_encode( $array ); // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode -- Required as part of our mock.
		} );
	}

	/**
	 * Setup the stubs for the common WordPress escaping and internationalization functions.
	 */
	protected function setup_common_wp_stubs() {
		// Common escaping functions.
		Monkey\Functions\stubs( array(
			'esc_attr',
			'esc_html',
			'esc_textarea',
			'esc_url',
			'wp_kses_post',
		) );

		// Common internationalization functions.
		Monkey\Functions\stubs( array(
			'__',
			'esc_html__',
			'esc_html_x',
			'esc_attr_x',
		) );

		foreach ( array( 'esc_attr_e', 'esc_html_e', '_e' ) as $wp_function ) {
			Monkey\Functions\when( $wp_function )->echoArg();
		}

		if ( ! $this->was_reset ) {
			$this->reset_actions_container();
			$this->reset_fields_container();
			$this->was_reset = true;
		}
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	protected function tearDown() {
		$this->reset_actions_container();
		$this->reset_fields_container();

		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Reset the Actions API container.
	 */
	protected function reset_actions_container() {
		global $_beans_registered_actions;
		$_beans_registered_actions = array(
			'added'    => array(),
			'modified' => array(),
			'removed'  => array(),
			'replaced' => array(),
		);
	}

	/**
	 * Reset the Fields API container, i.e. static memories.
	 */
	protected function reset_fields_container() {

		if ( ! class_exists( '_Beans_Fields' ) ) {
			return;
		}

		// Reset the "registered" container.
		$registered = $this->get_reflective_property( 'registered', '_Beans_Fields' );
		$registered->setValue( new \_Beans_Fields(), array(
			'option'       => array(),
			'post_meta'    => array(),
			'term_meta'    => array(),
			'wp_customize' => array(),
		) );

		// Reset the other static properties.
		foreach ( array( 'field_types_loaded', 'field_assets_hook_loaded' ) as $property_name ) {
			$property = $this->get_reflective_property( $property_name, '_Beans_Fields' );
			$property->setValue( new \_Beans_Fields(), array() );
		}
	}
}
