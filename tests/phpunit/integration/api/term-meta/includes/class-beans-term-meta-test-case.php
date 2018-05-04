<?php
/**
 * Test Case for Beans Term_Meta API integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta\Includes
 *
 * @since 1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Term_Meta\Includes;

use Beans\Framework\Tests\Integration\Test_Case;
use WP_UnitTestCase;

/**
 * Abstract Class Beans_Term_Meta_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\Term_Meta\Includes
 */
abstract class Beans_Term_Meta_Test_Case extends WP_UnitTestCase {

	/**
	 * An array of test data.
	 *
	 * @var array
	 */
	protected static $test_data;

	/**
	 * Setup the test before we run the test setups.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		static::$test_data = require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fixtures/test-fields.php';
	}

	/**
	 * Fixture to clean up after tests.
	 */
	public function tearDown() {
		unset( $GLOBALS['current_screen'] );
		$this->clean_up_global_scope();

		parent::tearDown();
	}
}
