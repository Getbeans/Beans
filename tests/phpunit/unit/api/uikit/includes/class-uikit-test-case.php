<?php
/**
 * Tests Case for Beans' UIkit API unit tests.
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\UIkit\Includes;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey;
use org\bovigo\vfs\vfsStream;

/**
 * Abstract Class UIkit_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit\Includes
 */
abstract class UIkit_Test_Case extends Test_Case {

	/**
	 * When true, return the given path when doing wp_normalize_path().
	 *
	 * @var bool
	 */
	protected $just_return_path = true;

	/**
	 * Flag is in admin area (back-end).
	 *
	 * @var bool
	 */
	protected $is_admin = false;

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
		$this->set_up_mocked_functions();

		$this->load_original_functions( array(
			'api/utilities/functions.php',
			'api/compiler/functions.php',
			'api/uikit/functions.php',
			'api/uikit/class-beans-uikit.php',
		) );

		$this->reset_globals();
	}

	/**
	 * Set up the virtual filesystem.
	 */
	protected function set_up_virtual_filesystem() {
		$this->mock_filesystem = vfsStream::setup(
			'themes',
			0755,
			$this->get_virtual_structure()
		);
	}

	/**
	 * Get the virtual filesystem's structure.
	 */
	protected function get_virtual_structure() {
		return [
			'beans-child' => [
				'assets'        => [
					'js' => [
						'alert.min.js' => '',
					],
					'less' => [
						'theme' => [
							'alert.less'     => '',
							'article.less'   => '',
							'panel.less'     => '',
							'variables.less' => '',
							'style.less'     => '',
							'variables.less' => '',
						],
					],
				],
				'functions.php' => '',
				'style.css'     => '',
			],
		];
	}

	/**
	 * Sets up the mocked functions.
	 */
	protected function set_up_mocked_functions() {
		Monkey\Functions\when( 'trailingslashit' )->alias( function( $file ) {
			return $file . '/';
		} );
	}

	/**
	 * Reset the global containers.
	 */
	protected function reset_globals() {
		global $_beans_uikit_enqueued_items, $_beans_uikit_registered_items;

		$_beans_uikit_enqueued_items = array(
			'components' => array(
				'core'    => array(),
				'add-ons' => array(),
			),
			'themes'     => array(),
		);

		$_beans_uikit_registered_items = array(
			'themes' => array(
				'default'         => BEANS_API_PATH . 'uikit/src/themes/default',
				'almost-flat'     => BEANS_API_PATH . 'uikit/src/themes/almost-flat',
				'gradient'        => BEANS_API_PATH . 'uikit/src/themes/gradient',
				'wordpress-admin' => BEANS_API_PATH . 'uikit/themes/wordpress-admin',
			),
		);
	}
}
