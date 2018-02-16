<?php
/**
 * Tests for beans_edit_image()
 *
 * @package Beans\Framework\Tests\Unit\API\Image
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Image;

use Beans\Framework\Tests\Unit\API\Image\Includes\Image_Test_Case;
use Brain\Monkey;

require_once __DIR__ . '/includes/class-image-test-case.php';

/**
 * Class Tests_BeansEditImage
 *
 * @package Beans\Framework\Tests\Unit\API\Image
 * @group   unit-tests
 * @group   api
 */
class Tests_Beans_Edit_Image extends Image_Test_Case {

	/**
	 * Path of the fixtures directory.
	 *
	 * @var string
	 */
	protected static $fixtures_dir;

	/**
	 * Set up the test fixture before we start.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		static::$fixtures_dir = realpath( __DIR__ . '/../fixtures' );

		if ( ! defined( 'BEANS_API_PATH' ) ) {
			define( 'BEANS_API_PATH', BEANS_TESTS_LIB_DIR . 'api/' );
		}
	}

	/**
	 * Test init() should return original src when the image does not exist.
	 */
	public function test_should_return_original_src_when_no_image() {
		$src = 'path/does/not/exist/image.jpg';

		// Simulate the WordPress' Editor.
		Monkey\Functions\expect( 'wp_get_image_editor' )->with( $src )->once();
		Monkey\Functions\expect( 'is_wp_error' )->once()->andReturn( true );

		// Run the tests.
		$this->assertFileNotExists( $src );
		$this->assertSame( $src, beans_edit_image( $src, array( 'resize' => array( 800, false ) ) ) );
	}

	/**
	 * Test beans_edit_image() should return an indexed array with the original src when the image does not exist.
	 */
	public function test_should_return_indexed_array_with_original_src_when_no_image() {
		$src = 'path/does/not/exist/image.jpg';

		// Simulate the WordPress' Editor.
		Monkey\Functions\expect( 'wp_get_image_editor' )->with( $src )->once();
		Monkey\Functions\expect( 'is_wp_error' )->once()->andReturn( true );

		// Run the tests.
		$this->assertFileNotExists( $src );
		$this->assertSame(
			array( $src, null, null ),
			beans_edit_image( $src, array( 'resize' => array( 800, false ) ), ARRAY_N )
		);
	}

	/**
	 * Test beans_edit_image() should return an object with the original src when the image does not exist.
	 */
	public function test_should_return_object_with_original_src_when_no_image() {
		$src = 'path/does/not/exist/image.jpg';

		// Simulate the WordPress' Editor.
		Monkey\Functions\expect( 'wp_get_image_editor' )->with( $src )->once();
		Monkey\Functions\expect( 'is_wp_error' )->once()->andReturn( true );

		// Run the tests.
		$this->assertFileNotExists( $src );
		$image_info = beans_edit_image( $src, array( 'resize' => array( 800, false ) ), OBJECT );
		$this->assertInstanceOf( 'stdClass', $image_info );
		$this->assertSame( $src, $image_info->src );
		$this->assertNull( $image_info->width );
		$this->assertNull( $image_info->height );
	}

	/**
	 * Test beans_edit_image() should return an associative array with the original src when the image does not exist.
	 */
	public function test_should_return_associative_array_with_original_src_when_no_image() {
		$src = 'path/does/not/exist/image.jpg';

		// Simulate the WordPress' Editor.
		Monkey\Functions\expect( 'wp_get_image_editor' )->with( $src )->once();
		Monkey\Functions\expect( 'is_wp_error' )->once()->andReturn( true );

		// Run the tests.
		$this->assertFileNotExists( $src );
		$this->assertSame(
			array(
				'src'    => $src,
				'width'  => null,
				'height' => null,
			),
			beans_edit_image( $src, array( 'resize' => array( 800, false ) ), ARRAY_A )
		);
	}
}
