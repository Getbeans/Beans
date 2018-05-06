<?php
/**
 * The base Test Case for Beans' Compiler API unit tests.
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler\Includes;

use Beans\Framework\Tests\Unit\Test_Case;
use org\bovigo\vfs\vfsStream;

/**
 * Abstract Class Base_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler\Includes
 */
abstract class Base_Test_Case extends Test_Case {

	/**
	 * Path to the compiled files' directory.
	 *
	 * @var string
	 */
	protected $compiled_dir;

	/**
	 * Path to the compiled files directory's URL.
	 *
	 * @var string
	 */
	protected $compiled_url;

	/**
	 * Instance of vfsStreamDirectory to mock the filesystem.
	 *
	 * @var vfsStreamDirectory
	 */
	protected $mock_filesystem;

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->set_up_virtual_filesystem();
		$this->compiled_dir = vfsStream::url( 'compiled' );
		$this->compiled_url = 'http:://beans.test/compiled/';

		$this->load_original_functions( array(
			'api/utilities/functions.php',
			'api/compiler/functions.php',
		) );

		$this->setup_common_wp_stubs();
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
