<?php
/**
 * Tests the get_all_components() method of _Beans_Uikit.
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\UIkit;

use _Beans_Uikit;
use Beans\Framework\Tests\Unit\API\UIkit\Includes\UIkit_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-uikit-test-case.php';

/**
 * Class Tests_BeansUikit_GetAllComponents
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 * @group   api
 * @group   api-uikit
 */
class Tests_BeansUikit_GetAllComponents extends UIkit_Test_Case {

	/**
	 * Number of core LESS files.
	 *
	 * @var int
	 */
	protected static $number_core_less_files = 0;

	/**
	 * Number of core JavaScript files.
	 *
	 * @var int
	 */
	protected static $number_core_js_files = 0;

	/**
	 * Number of component (add-ons) LESS files.
	 *
	 * @var int
	 */
	protected static $number_components_less_files = 0;

	/**
	 * Number of component (add-ons) JavaScript files.
	 *
	 * @var int
	 */
	protected static $number_components_js_files = 0;

	/**
	 * This method is called before the first test of this test class is run.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		static::$number_core_less_files       = static::count_files_in_dir( BEANS_API_PATH . 'uikit/src/less/core' );
		static::$number_core_js_files         = static::count_files_in_dir( BEANS_API_PATH . 'uikit/src/js/core' );
		static::$number_components_less_files = static::count_files_in_dir( BEANS_API_PATH . 'uikit/src/less/components' );
		static::$number_components_js_files   = static::count_files_in_dir( BEANS_API_PATH . 'uikit/src/js/components' );
	}

	/**
	 * Test _Beans_Uikit::get_all_components() should return all core components.
	 */
	public function test_should_return_all_core_components() {
		$beans_uikit = new _Beans_Uikit();

		$actual = $beans_uikit->get_all_components( 'core' );
		$this->assertCount( static::$number_core_less_files + static::$number_core_js_files, $actual );

		// Check common components.
		$this->assertContains( 'alert', $actual );
		$this->assertContains( 'button', $actual );
		$this->assertContains( 'cover', $actual );
		$this->assertContains( 'grid', $actual );
		$this->assertContains( 'nav', $actual );
		$this->assertContains( 'offcanvas', $actual );
		$this->assertContains( 'tab', $actual );
		$this->assertContains( 'utility', $actual );

		// Spot check the unique LESS components.
		$this->assertContains( 'base', $actual );
		$this->assertContains( 'close', $actual );
		$this->assertContains( 'column', $actual );
		$this->assertContains( 'description-list', $actual );
		$this->assertContains( 'thumbnail', $actual );
		$this->assertContains( 'variables', $actual );

		// Spot check the unique JS components.
		$this->assertContains( 'core', $actual );
		$this->assertContains( 'scrollspy', $actual );
		$this->assertContains( 'smooth-scroll', $actual );
		$this->assertContains( 'toggle', $actual );
		$this->assertContains( 'touch', $actual );

		// Check the components do not contain add-ons.
		$this->assertNotContains( 'accordion', $actual );
		$this->assertNotContains( 'datepicker', $actual );
		$this->assertNotContains( 'notify', $actual );
		$this->assertNotContains( 'progress', $actual );
	}

	/**
	 * Test _Beans_Uikit::get_all_components() should return all add-ons components.
	 */
	public function test_should_return_all_add_ons_components() {
		$beans_uikit = new _Beans_Uikit();

		$actual = $beans_uikit->get_all_components( 'add-ons' );
		$this->assertCount( static::$number_components_less_files + static::$number_components_js_files, $actual );

		// Check common components.
		$this->assertContains( 'accordion', $actual );
		$this->assertContains( 'autocomplete', $actual );
		$this->assertContains( 'datepicker', $actual );
		$this->assertContains( 'form-password', $actual );
		$this->assertContains( 'search', $actual );
		$this->assertContains( 'sticky', $actual );
		$this->assertContains( 'tooltip', $actual );
		$this->assertContains( 'upload', $actual );

		// Spot check the unique LESS components.
		$this->assertContains( 'dotnav', $actual );
		$this->assertContains( 'form-advanced', $actual );
		$this->assertContains( 'htmleditor', $actual );

		// Spot check the unique JS components.
		$this->assertContains( 'parallax', $actual );
		$this->assertContains( 'lightbox', $actual );
		$this->assertContains( 'slideshow-fx', $actual );
		$this->assertContains( 'timepicker', $actual );

		// Check the components do not contain add-ons.
		$this->assertNotContains( 'alert', $actual );
		$this->assertNotContains( 'badge', $actual );
		$this->assertNotContains( 'base', $actual );
		$this->assertNotContains( 'close', $actual );
	}

	/**
	 * Counts the files in the given source directory.
	 *
	 * @since 1.5.0
	 *
	 * @param string $dir Given directory to scan.
	 *
	 * @return int
	 */
	private static function count_files_in_dir( $dir ) {
		$files = scandir( $dir );

		if ( '.' === $files[0] ) {
			unset( $files[0], $files[1] );
		}

		return count( $files );
	}
}
