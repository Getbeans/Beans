<?php
/**
 * Test Case for the integration tests.
 *
 * @package Beans\Framework\Tests\Integration
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration;

use Brain\Monkey;
use WP_UnitTestCase;

/**
 * Abstract Class Test_Case
 *
 * @package Beans\Framework\Tests\Integration
 */
abstract class Test_Case extends WP_UnitTestCase {

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

	/**
	 * Format the HTML by stripping out the whitespace between the HTML tags and then putting each tag on a separate
	 * line.
	 *
	 * Why? We can then compare the actual vs. expected HTML patterns without worrying about tabs, new lines, and extra
	 * spaces.
	 *
	 * @since 1.5.0
	 *
	 * @param string $html HTML to strip.
	 *
	 * @return string
	 */
	protected function format_the_html( $html ) {
		$html = trim( $html );

		// Strip whitespace between the tags.
		$html = preg_replace( '/(\>)\s*(\<)/m', '$1$2', $html );

		// Strip whitespace at the end of a tag.
		$html = preg_replace( '/(\>)\s*/m', '$1$2', $html );

		// Strip whitespace at the start of a tag.
		$html = preg_replace( '/\s*(\<)/m', '$1$2', $html );

		return str_replace( '>', ">\n", $html );
	}
}
