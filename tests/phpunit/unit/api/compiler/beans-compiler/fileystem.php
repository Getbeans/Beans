<?php
/**
 * Tests the filesystem method of _Beans_Compiler.
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler;

use Beans\Framework\Tests\Unit\API\Compiler\Includes\Compiler_Test_Case;
use Brain\Monkey\Functions;
use Mockery;

require_once dirname( __DIR__ ) . '/includes/class-compiler-test-case.php';

/**
 * Class Tests_Beans_Compiler_Filesystem
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   unit-tests
 * @group   api
 */
class Tests_Beans_Compiler_Filesystem extends Compiler_Test_Case {

	/**
	 * Test filesystem() should return true when WP Filesystem is initialized to WP_Filesystem_Direct.
	 */
	public function test_should_return_true_when_wp_filesystem_is_init() {
		// Initialize the wp_filesystem global variable.
		Mockery::mock( 'WP_Filesystem_Direct' );
		$GLOBALS['wp_filesystem'] = new \WP_Filesystem_Direct(); // @codingStandardsIgnoreLine - WordPress.Variables.GlobalVariables.OverrideProhibited  This is a valid use case as we are mocking the filesystem.

		Functions\when( 'WP_Filesystem' )->justReturn( true );

		// Test that WP Filesystem is not loaded yet.
		$compiler = new \_Beans_Compiler( array() );
		$this->assertTrue( $compiler->filesystem() );

		unset( $GLOBALS['wp_filesystem'] );
	}
}
