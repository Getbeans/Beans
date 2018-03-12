<?php
/**
 * Test Case for Beans' HTML API unit tests.
 *
 * @package Beans\Framework\Tests\Unit\API\HTML\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\HTML\Includes;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey;

/**
 * Abstract Class HTML_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\HTML\Includes
 */
abstract class HTML_Test_Case extends Test_Case {

	/**
	 * An array of markup to test.
	 *
	 * @var array
	 */
	protected static $test_markup;

	/**
	 * An array of attributes to test.
	 *
	 * @var array
	 */
	protected static $test_attributes;

	/**
	 * Setup the test before we run the test setups.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		static::$test_markup     = require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fixtures/test-markup.php';
		static::$test_attributes = array_filter( static::$test_markup, function( $markup ) {
			return isset( $markup['attributes'] );
		} );
	}

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->load_original_functions( array(
			'api/html/class-beans-attribute.php',
			'api/html/functions.php',
			'api/filters/functions.php',
		) );

		$this->setup_mocks();
	}

	/**
	 * Setup dependency function mocks.
	 */
	protected function setup_mocks() {
		Monkey\Functions\when( 'beans_esc_attributes' )->alias( function( array $attributes ) {
			$string = '';

			foreach ( (array) $attributes as $attribute => $value ) {

				if ( null === $value ) {
					continue;
				}

				$string .= $attribute . '="' . $value . '" ';
			}

			return trim( $string );
		} );
	}
}
