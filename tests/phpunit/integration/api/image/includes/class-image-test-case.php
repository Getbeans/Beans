<?php
/**
 * Test Case for Beans' Image API integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Image\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Image\Includes;

use org\bovigo\vfs\vfsStream;

require_once __DIR__ . '/class-base-test-case.php';

/**
 * Abstract Class Image_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\Image\Includes
 */
abstract class Image_Test_Case extends Base_Test_Case {

	/**
	 * When true, return the given path when doing wp_normalize_path().
	 *
	 * @var bool
	 */
	protected $just_return_path = true;

	/**
	 * Initialize the virtual "edited" image.
	 *
	 * @since 1.5.0
	 *
	 * @param \ReflectionProperty $rebuilt_path Instance of the editor's "rebuilt path" property.
	 * @param _Beans_Image_Editor $editor       Instance of the editor.
	 * @param string|null         $path         Optional. The image's "rebuilt path".
	 *
	 * @return string
	 */
	protected function init_virtual_image( $rebuilt_path, $editor, $path = null ) {

		if ( is_null( $path ) ) {
			$path = $rebuilt_path->getValue( $editor );
		}

		$path = $this->fix_virtual_dir( $path );
		$rebuilt_path->setValue( $editor, $path );
		return $rebuilt_path->getValue( $editor );
	}

	/**
	 * Fix the virtual directory. Modify the root, as wp_normalize_path changes it.
	 *
	 * @since 1.5.0
	 *
	 * @param string $path The path to fix.
	 *
	 * @return string
	 */
	protected function fix_virtual_dir( $path ) {

		if ( substr( $path, 0, 6 ) === 'vfs://' ) {
			return $path;
		}

		return str_replace( 'vfs:/', 'vfs://', $path );
	}

	/**
	 * Removes the vfsStream's root, i.e. vfs:// or vfs:/.
	 *
	 * @since 1.5.0
	 *
	 * @param string $path The path to fix.
	 *
	 * @return string
	 */
	protected function remove_virtual_dir_root( $path ) {
		$pattern = substr( $path, 0, 6 ) === 'vfs://'
			? 'vfs://'
			: 'vfs:/';

		return str_replace( $pattern, '', $path );
	}
}
