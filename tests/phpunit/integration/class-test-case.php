<?php
/**
 * Test Case for the integration tests.
 *
 * @package Beans\Framework\Tests\Integration
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration;

use Beans\Framework\Tests\Test_Case_Trait;
use Brain\Monkey;
use WP_UnitTestCase;

/**
 * Abstract Class Test_Case
 *
 * @package Beans\Framework\Tests\Integration
 */
abstract class Test_Case extends WP_UnitTestCase {

	use Test_Case_Trait;

	/**
	 * Set up the test before we run the test setups.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		set_current_screen( 'front' );
	}

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();
		Monkey\setUp();
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {
		$this->reset_fields_container();
		$this->reset_actions_container();

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
