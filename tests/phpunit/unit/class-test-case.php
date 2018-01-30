<?php
/**
 * Test Case for the unit tests.
 *
 * @package Beans\Framework\Tests\Unit
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * Abstract Class Test_Case
 *
 * @package Beans\Framework\Tests\Unit
 */
abstract class Test_Case extends TestCase {

	/**
	 * When true, return the given path when doing wp_normalize_path().
	 *
	 * @var bool
	 */
	protected $just_return_path = false;

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
	 * Cleans up the test environment after each test.
	 */
	protected function tearDown() {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Load the original Beans' functions into memory before we start.
	 *
	 * Then in our tests, we monkey patch via Brain Monkey, which redefines the original function.
	 * At tear down, the original function is restored in Brain Monkey, by calling Patchwork\restoreAll().
	 *
	 * @since 1.5.0
	 *
	 * @param array $files Array of files to load into memory.
	 *
	 * @return void
	 */
	protected function load_original_functions( array $files ) {

		foreach ( $files as $file ) {
			require_once BEANS_TESTS_LIB_DIR . $file;
		}
	}
}
