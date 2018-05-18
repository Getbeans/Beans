<?php
/**
 * Beans' Test Suites' common functionality.
 *
 * @package Beans\Framework\Tests
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests;

/**
 * Resets Beans for the test suite.
 *
 * @since 1.5.0
 *
 * @return void
 */
function reset_beans() {
	get_beans_resetter()->reset();
}

/**
 * Gets the Bean's Resetter.
 *
 * @since 1.5.0
 *
 * @return Beans_Resetter
 */
function get_beans_resetter() {
	static $resetter;

	if ( is_null( $resetter ) ) {
		require_once __DIR__ . '/class-beans-resetter.php';
		$resetter = new Beans_Resetter();
	}

	return $resetter;
}
