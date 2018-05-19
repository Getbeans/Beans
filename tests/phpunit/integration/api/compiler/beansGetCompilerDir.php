<?php
/**
 * Tests for beans_get_compiler_dir()
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Compiler;

use Beans\Framework\Tests\Integration\API\Compiler\Includes\Compiler_Test_Case;
use org\bovigo\vfs\vfsStream;
use Brain\Monkey;

require_once __DIR__ . '/includes/class-compiler-test-case.php';

/**
 * Class Test_BeansGetCompilerDir
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Test_BeansGetCompilerDir extends Compiler_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		// Return the virtual filesystem's path to avoid wp_normalize_path converting its prefix from vfs::// to vfs:/.
		Monkey\Functions\when( 'wp_normalize_path' )->returnArg();
	}

	/**
	 * Test beans_get_compiler_dir() should return the absolute path to the Beans' uploads compiler folder.
	 */
	public function test_should_return_absolute_path_to_compiler_folder() {
		$this->assertSame(
			vfsStream::url( 'compiled/beans/compiler/' ),
			beans_get_compiler_dir()
		);
	}

	/**
	 * Test beans_get_compiler_dir() should return the absolute path to the Beans' uploads admin compiler folder.
	 */
	public function test_should_return_absolute_path_to_compiler_admin_folder() {
		$this->assertSame(
			vfsStream::url( 'compiled/beans/admin-compiler/' ),
			beans_get_compiler_dir( true )
		);
	}
}
