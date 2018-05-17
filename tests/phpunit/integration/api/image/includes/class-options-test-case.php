<?php
/**
 * Test Case for Beans Image API Options integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Image\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Image\Includes;

require_once __DIR__ . '/class-base-test-case.php';

/**
 * Abstract Class Options_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\Image\Includes
 */
abstract class Options_Test_Case extends Base_Test_Case {

	/**
	 * Flag is in admin area (back-end).
	 *
	 * @var bool
	 */
	protected $is_admin = true;

	/**
	 * Set up the test before we run the test setups.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		set_current_screen( 'themes.php' );

		require_once BEANS_THEME_DIR . '/lib/api/image/class-beans-image-options.php';
	}

	/**
	 * Set up the test before we run the test setups.
	 */
	public static function setUpAfterClass() {
		parent::setUpAfterClass();

		set_current_screen( 'front' );
	}

	/**
	 * Go to the Settings Page.
	 */
	protected function go_to_settings_page() {
		set_current_screen( 'themes.php?page=beans_settings' );
		$_GET['page'] = 'beans_settings';

		$this->assertTrue( is_admin() );
	}
}
