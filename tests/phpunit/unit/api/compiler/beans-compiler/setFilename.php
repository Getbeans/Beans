<?php
/**
 * Tests the set_filename method of _Beans_Compiler.
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
 * Class Tests_Beans_Compiler_Set_Filename
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   unit-tests
 * @group   api
 */
class Tests_Beans_Compiler_Set_Filename extends Compiler_Test_Case {

//	/**
//	 * Test set_filename() should return the base hash when not in development mode.
//	 */
//	public function test_should_return_base_hash_when_not_in_dev_mode() {
//		$fragment = vfsStream::url( 'compiled/fixtures/jquery.test.js' );
//		$config   = array(
//			'id'           => 'test-script',
//			'type'         => 'script',
//			'format'       => false,
//			'fragments'    => array( $fragment ),
//			'dependencies' => false,
//			'in_footer'    => true,
//			'minify_js'    => true,
//			'version'      => null,
//		);
//		$compiler = new \_Beans_Compiler( $config );
//
//		// Test that we are not in development mode.
//		$this->mock_dev_mode( false );
//		$this->assertFalse( _beans_is_compiler_dev_mode() );
//
//		// Set the filename.
//		$compiler->set_filename();
//		$filename = $compiler->config['filename'];
//		$pathinfo = pathinfo( $filename );
//		$expected = $compiler->hash( $config );
//
//		// Run the tests.
//		$this->assertSame( 'js', $pathinfo['extension'] );
//		$this->assertEquals( 7, strlen( $pathinfo['filename'] ) );
//		$this->assertSame( $expected, $pathinfo['filename'] );
//		$this->assertSame( $expected . '.js', $filename );
//	}

//	/**
//	 * Test set_filename() should return the base hash when not in development mode.
//	 */
//	public function test_should_return_base_hash_when_dir_does_not_exist() {
//		$fragment = vfsStream::url( 'compiled/fixtures/my-game-clock.js' );
//		$config   = array(
//			'id'           => 'test-script',
//			'type'         => 'script',
//			'format'       => false,
//			'fragments'    => array( $fragment ),
//			'dependencies' => false,
//			'in_footer'    => true,
//			'minify_js'    => true,
//			'version'      => null,
//		);
//		$compiler = new \_Beans_Compiler( $config );
//
//		// Test that we are in dev mode & the cache directory does not exist.
//		$this->mock_dev_mode( true );
//		$this->assertTrue( _beans_is_compiler_dev_mode() );
//		$this->assertFalse( is_dir( vfsStream::url( 'compiled/beans/compiler/test-script' ) ) );
//		$this->assertFalse( is_dir( $compiler->dir ) );
//
//		// Set the filename.
//		$compiler->set_filename();
//		$filename = $compiler->config['filename'];
//		$pathinfo = pathinfo( $filename );
//		$expected = $compiler->hash( $config );
//
//		// Run the tests.
//		$this->assertSame( 'js', $pathinfo['extension'] );
//		$this->assertSame( $expected, $pathinfo['filename'] );
//		$this->assertSame( $expected . '.js', $filename );
//	}

	/**
	 * Test set_filename() should return the hash created with the modification time from each of the fragments.
	 */
	public function test_should_return_hash_created_with_fragments_filemtime() {
		$fragment = vfsStream::url( 'compiled/fixtures/jquery.test.js' );
		$config   = array(
			'id'           => 'test-script',
			'type'         => 'script',
			'format'       => false,
			'fragments'    => array( $fragment ),
			'dependencies' => false,
			'in_footer'    => true,
			'minify_js'    => true,
			'version'      => null,
		);
		$compiler = new \_Beans_Compiler( $config );

		// Set up the mocks.
		$this->mock_dev_mode( true );
		$this->add_virtual_directory( 'test-script' );

		// Set the filename. Test.
		$compiler->set_filename();
		$expected = $this->get_filename( $compiler, $config, filemtime( $fragment ) );
		$this->assertSame( $expected, $compiler->config['filename'] );
	}

	/**
	 * Test set_filename() should return the hash created with the modification time from each of the fragments.
	 */
	public function test_should_exclude_external_fragments() {
		$fragment = vfsStream::url( 'compiled/fixtures/my-game-clock.js' );
		$config   = array(
			'id'           => 'test-script',
			'type'         => 'script',
			'format'       => false,
			'fragments'    => array(
				$fragment,
				'http://foo.com/my-script.js', // Should skip this one.
			),
			'dependencies' => false,
			'in_footer'    => true,
			'minify_js'    => true,
			'version'      => null,
		);
		$compiler = new \_Beans_Compiler( $config );

		// Set up the mocks.
		$this->mock_dev_mode( true );
		$this->add_virtual_directory( 'test-script' );

		// Test that we are in dev mode & the directory does exist.
		$this->assertTrue( _beans_is_compiler_dev_mode() );
		$this->directoryExists( vfsStream::url( 'compiled/beans/compiler/test-script' ) );
		$this->assertTrue( is_dir( $compiler->dir ) );

		// Set the filename. Test.
		$compiler->set_filename();
		$expected = $this->get_filename( $compiler, $config, filemtime( $fragment ) );
		$this->assertSame( $expected, $compiler->config['filename'] );
	}

	/**
	 * Test set_filename() should remove the old compiled file.
	 */
	public function test_should_remove_old_file() {
		$fragment = vfsStream::url( 'compiled/fixtures/jquery.test.js' );
		$config   = array(
			'id'           => 'test',
			'type'         => 'script',
			'format'       => 'js',
			'fragments'    => array( $fragment ),
			'dependencies' => array( 'jquery' ),
			'in_footer'    => true,
			'minify_js'    => true,
			'version'      => null,
		);
		$compiler = new \_Beans_Compiler( $config );

		// Set up the mocks.
		$this->mock_dev_mode( true );
		$this->add_virtual_directory( $config['id'] );

		/**
		 * Set up the original "compiled" file. This is the file that should get removed during this
		 * test.  We add the file into the virtual filesystem.
		 */
		$original_filemtime = filemtime( $fragment );
		$original_filename  = $this->get_filename( $compiler, $config, $original_filemtime );
		vfsStream::newFile( $original_filename )
			->at( $this->mock_filesystem->getChild( 'compiled/beans/compiler/' . $config['id'] ) )
			->setContent( $this->get_compiled_jquery() );
		$original_hashes = explode( '-', pathinfo( $original_filename, PATHINFO_FILENAME ) );
		$this->assertFileExists( vfsStream::url( 'compiled/beans/compiler/test/' . $original_filename ) );

		/**
		 * Next step is to modify the fragment, which will change it's modification time.  Let's add an opening
		 * comment to the fragment's content and then set up the modified file for our tests.
		 */
		$file = $this->mock_filesystem->getChild( 'fixtures' )->getChild( 'jquery.test.js' );
		$file->write( "// changed \n" . $file->getContent() );
		$modified_filemtime = filemtime( $fragment );
		$modified_filename  = $this->get_filename( $compiler, $config, $modified_filemtime );
		$modified_hashes    = explode( '-', pathinfo( $modified_filename, PATHINFO_FILENAME ) );

		/**
		 * We've now completed the set up process.  Let's test that the original fragment has changed by
		 * testing that it is not equal to the modification time, filename, and hashes.
		 */
		$this->assertNotEquals( $modified_filemtime, $original_filemtime );
		$this->assertNotEquals( $modified_filename, $original_filename );
		$this->assertSame( $modified_hashes[0], $original_hashes[0] );
		$this->assertNotEquals( $modified_hashes[1], $original_hashes[1] );

		/**
		 * Now let's run the compiler's set_filename() and test that:
		 *
		 * 1. The returned filename matches our modified filename.
		 * 2. The original "compiled" file was removed.
		 */
		$compiler->set_filename();
		$this->assertSame( $modified_filename, $compiler->config['filename'] );
		$this->assertFileNotExists( vfsStream::url( 'compiled/beans/compiler/test/' . $original_filename ) );
	}
}
