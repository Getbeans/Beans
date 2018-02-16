<?php
/**
 * Tests for get_image_info() method of _Beans_Image_Editor.
 *
 * @package Beans\Framework\Tests\Unit\API\Image
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Image;

use _Beans_Image_Editor;
use Beans\Framework\Tests\Unit\API\Image\Includes\Image_Test_Case;
use Brain\Monkey;
use Mockery;

require_once dirname( __DIR__ ) . '/includes/class-image-test-case.php';
require_once BEANS_TESTS_LIB_DIR . 'api/image/class-beans-image-editor.php';

/**
 * Class Tests_Beans_Edit_Image_GetImageInfo
 *
 * @package Beans\Framework\Tests\Unit\API\Image
 * @group   unit-tests
 * @group   api
 */
class Tests_Beans_Edit_Image_CreateEditedImage extends Image_Test_Case {

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
	}

	/**
	 * Test get_image_info() should edit the given image and then create a new "edited image", which is stored in the
	 * "rebuilt path".
	 */
	public function test_should_edit_create_and_store_image() {
		$get_image_info = $this->get_reflective_method( 'create_edited_image' );
		$rebuilt_path   = $this->get_reflective_property();
		$image_sources  = array(
			static::$fixtures_dir . '/image1.jpg',
			static::$fixtures_dir . '/image2.jpg',
		);
		$args           = array( 'resize' => array( 800, false ) );

		foreach ( $image_sources as $src ) {
			$editor           = new _Beans_Image_Editor( $src, $args );
			$edited_image_src = $this->init_virtual_image( $rebuilt_path, $editor );

			// Simulate the WordPress' Editor.
			$wp_editor = Mockery::mock( 'WP_Image_Editor' );
			$wp_editor->shouldReceive( 'resize' )->once()->with( $args['resize'][0], $args['resize'][1] );
			$wp_editor->shouldReceive( 'save' )
				->once()
				->with( $edited_image_src )
				->andReturnUsing( function( $edited_image_src ) use ( $src ) {
					imagejpeg( imagecreatefromjpeg( $src ), $edited_image_src );
				} );
			Monkey\Functions\expect( 'wp_get_image_editor' )->with( $src )->once()->andReturn( $wp_editor );
			Monkey\Functions\when( 'is_wp_error' )->justReturn( false );

			// Run the tests.
			$this->assertFileNotExists( $edited_image_src );
			$this->assertTrue( $get_image_info->invoke( $editor ) );
			$this->assertFileExists( $edited_image_src );
		}
	}

	/**
	 * Test get_image_info() should return false when the image does not exist.
	 */
	public function test_should_return_false_when_no_image() {
		$get_image_info = $this->get_reflective_method( 'create_edited_image' );
		$rebuilt_path   = $this->get_reflective_property();
		$src            = 'path/does/not/exist/image.jpg';
		$args           = array( 'resize' => array( 800, false ) );

		$editor           = new _Beans_Image_Editor( $src, $args );
		$edited_image_src = $this->init_virtual_image( $rebuilt_path, $editor );

		// Simulate the WordPress' Editor.
		Monkey\Functions\expect( 'wp_get_image_editor' )->with( $src )->once();
		Monkey\Functions\expect( 'is_wp_error' )->once()->andReturn( true );

		// Run the tests.
		$this->assertFileNotExists( $src );
		$this->assertFalse( $get_image_info->invoke( $editor ) );
		$this->assertFileNotExists( $edited_image_src );
	}
}
