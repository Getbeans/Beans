<?php
/**
 * The base Test Case for Beans' Compiler API integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Compiler\Includes;

use Mockery;
use WP_UnitTestCase;
use org\bovigo\vfs\vfsStream;

/**
 * Abstract Class Base_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler\Includes
 */
abstract class Base_Test_Case extends WP_UnitTestCase {

	/**
	 * Path to the compiled files' directory.
	 *
	 * @var string
	 */
	protected $compiled_dir;

	/**
	 * Instance of vfsStreamDirectory to mock the filesystem.
	 *
	 * @var vfsStreamDirectory
	 */
	protected $mock_filesystem;

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		$this->set_up_virtual_filesystem();
		$this->compiled_dir = vfsStream::url( 'compiled' );

		// Set the Uploads directory to our virtual filesystem.
		add_filter( 'upload_dir', function( array $uploads_dir ) {
			$uploads_dir['path']    = $this->compiled_dir . $uploads_dir['subdir'];
			$uploads_dir['url']     = str_replace( 'wp-content/uploads', 'compiled', $uploads_dir['url'] );
			$uploads_dir['basedir'] = str_replace( 'vfs://', 'vfs:///', $this->compiled_dir );
			$uploads_dir['baseurl'] = str_replace( 'wp-content/uploads', 'compiled', $uploads_dir['baseurl'] );

			return $uploads_dir;
		} );
	}

	/**
	 * Tear down the test fixture.
	 */
	public function tearDown() {
		Mockery::close();
		parent::tearDown();
	}

	/**
	 * Set up the virtual filesystem.
	 */
	protected function set_up_virtual_filesystem() {
		$this->mock_filesystem = vfsStream::setup(
			'compiled',
			0755,
			$this->get_virtual_structure()
		);
	}

	/**
	 * Get the virtual filesystem's structure.
	 */
	protected function get_virtual_structure() {
		return array(
			'beans' => array(
				'compiler'       => array(
					'index.php' => '',
				),
				'admin-compiler' => array(
					'index.php' => '',
				),
			),
		);
	}
}
