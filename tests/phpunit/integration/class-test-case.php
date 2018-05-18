<?php
/**
 * Test Case for the integration tests.
 *
 * @package Beans\Framework\Tests\Integration
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration;

use function Beans\Framework\Tests\reset_beans;
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
		reset_beans();

		Monkey\tearDown();
		parent::tearDown();
	}
}
