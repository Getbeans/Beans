<?php
/**
 * Tests the cache_file_exist method of _Beans_Compiler.
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler;

use Beans\Framework\Tests\Unit\API\Compiler\Includes\Compiler_Test_Case;
use org\bovigo\vfs\vfsStream;

require_once dirname( __DIR__ ) . '/includes/class-compiler-test-case.php';

/**
 * Class Tests_Beans_Compiler_CacheFileExist
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   unit-tests
 * @group   api
 */
class Tests_Beans_Compiler_CacheFileExist extends Compiler_Test_Case {

	/**
	 * Test cache_file_exist() should return false when the filename has not been generated.
	 */
	public function test_should_return_false_when_filename_not_generated() {
		$fragment = vfsStream::url( 'compiled/fixtures/my-game-clock.js' );
		$compiler = new \_Beans_Compiler( array(
			'id'        => 'test',
			'type'      => 'script',
			'fragments' => array( $fragment ),
		) );

		// Check that the filename does not exist.
		$this->assertArrayNotHasKey( 'filename', $compiler->config );

		// Run cache_file_exist().
		$this->assertFalse( $compiler->cache_file_exist() );
	}

	/**
	 * Test cache_file_exist() should return false when the cached file does not exist.
	 */
	public function test_should_return_false_when_file_does_not_exist() {
		$fragment = vfsStream::url( 'compiled/fixtures/my-game-clock.js' );
		$compiler = new \_Beans_Compiler( array(
			'id'        => 'test-script',
			'type'      => 'script',
			'fragments' => array( $fragment ),
		) );

		// Set up the mocks.
		$this->mock_dev_mode( true );
		$this->add_virtual_directory( 'test-script' );

		// Generate the filename.
		$compiler->set_filename();

		// Check that the cached file does not exist in the virtual filesystem.
		$this->assertFileNotExists( vfsStream::url( 'compiled/beans/compiler/test-script/' . $compiler->filename ) );

		// Run cache_file_exist().
		$this->assertFalse( $compiler->cache_file_exist() );
	}

	/**
	 * Test cache_file_exist() should return true when the cached file does exist.
	 */
	public function test_should_return_true_when_file_exists() {
		$fragment = vfsStream::url( 'compiled/fixtures/my-game-clock.js' );
		$compiler = new \_Beans_Compiler( array(
			'id'        => 'test-script',
			'type'      => 'script',
			'fragments' => array( $fragment ),
		) );

		// Set up the mocks.
		$this->mock_dev_mode( true );
		$this->add_virtual_directory( 'test-script' );

		// Add the cached file to the virtual filesystem.
		$compiler->set_filename();
		vfsStream::newFile( $compiler->filename )
			->at( $this->mock_filesystem->getChild( 'compiled/beans/compiler/test-script' ) );

		// Check that the filename exists.
		$this->assertFileExists( vfsStream::url( 'compiled/beans/compiler/test-script/' . $compiler->filename ) );

		// Run cache_file_exist().
		$this->assertTrue( $compiler->cache_file_exist() );
	}
}
