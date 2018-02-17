<?php
/**
 * Test Case for Beans' Template API unit tests.
 *
 * @package Beans\Framework\Tests\Unit\API\Template\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Template\Includes;

use Beans\Framework\Tests\Unit\Test_Case;
use org\bovigo\vfs\vfsStream;

/**
 * Abstract Class Template_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\Template\Includes
 */
abstract class Template_Test_Case extends Test_Case {

	/**
	 * When true, return the given path when doing wp_normalize_path().
	 *
	 * @var bool
	 */
	protected $just_return_path = true;

	/**
	 * Instance of vfsStreamDirectory to mock the filesystem.
	 *
	 * @var vfsStreamDirectory
	 */
	protected $mock_filesystem;

	/**
	 * Flag is in admin area (back-end).
	 *
	 * @var bool
	 */
	protected $is_admin = false;

	/**
	 * Set up the test before we run the test setups.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		require_once BEANS_TESTS_LIB_DIR . 'api/template/functions.php';
	}

	/**
	 * Set up the test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		$this->set_up_virtual_filesystem();

		if ( ! defined( 'BEANS_STRUCTURE_PATH' ) ) {
			define( 'BEANS_STRUCTURE_PATH', vfsStream::url( 'templates/structure/' ) );
		}

		if ( ! defined( 'BEANS_FRAGMENTS_PATH' ) ) {
			define( 'BEANS_FRAGMENTS_PATH', vfsStream::url( 'templates/fragments/' ) );
		}
	}

	/**
	 * Set up the virtual filesystem.
	 */
	private function set_up_virtual_filesystem() {
		// Create the file structure and load each file's content.
		$file_structure = array(
			'fragments' => array(
				'branding.php'  => '<div class="tm-site-branding"><a href="http://example.com">Beans Tests</a></div>',
				'post-body.php' => '<div class="tm-article-content"><p>Nulla in orci condimentum, facilisis ex et, blandit augue.</p></div>',
			),
			'structure' => array(
				'content.php' => '',
				'header.php'  => '',
			),
		);
		$file_structure['structure']['content.php'] = '<article>' . $file_structure['fragments']['post-body.php'] . '</article>';
		$file_structure['structure']['header.php']  = '<header>' . $file_structure['fragments']['branding.php'] . '</header>';

		// Set up the "templates" directory's virtual filesystem.
		$this->mock_filesystem = vfsStream::setup( 'templates', 0755, $file_structure );
	}

	/**
	 * Load the fragment into the virtual filesystem.  Loads from the fixtures/fragments/ folder.
	 *
	 * @since 1.0.0
	 *
	 * @param string $fragment Fragment's path or filename.
	 *
	 * @return void
	 */
	protected function load_fragment_into_virtual_filesystem( $fragment ) {
		$structure['fragments'][ $fragment ] = file_get_contents( BEANS_THEME_DIR . '/lib/templates/fragments/' . basename( $fragment ) ); // @codingStandardsIgnoreLine - WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents - valid edge case.
	}

	/**
	 * Load the structure into the virtual filesystem.  Loads from the fixtures/structure/ folder.
	 *
	 * @since 1.0.0
	 *
	 * @param string $structure Structure's path or filename.
	 *
	 * @return void
	 */
	protected function load_structure_into_virtual_filesystem( $structure ) {
		$structure['structure'][ $structure ] = file_get_contents( BEANS_THEME_DIR . '/lib/templates/structure/' . basename( $structure ) ); // @codingStandardsIgnoreLine - WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents - valid edge case.
	}
}
