<?php
/**
 * Tests for beans_load_default_template()
 *
 * @package Beans\Framework\Tests\Unit\API\Template
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Template;

use Beans\Framework\Tests\Unit\API\Template\Includes\Template_Test_Case;
use org\bovigo\vfs\vfsStream;

require_once __DIR__ . '/includes/class-template-test-case.php';

/**
 * Class Tests_BeansLoadDefaultTemplate
 *
 * @package Beans\Framework\Tests\Unit\API\Template
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansLoadDefaultTemplate extends Template_Test_Case {

	/**
	 * Test beans_load_default_template() should return false when the template does not exist.
	 */
	public function test_should_return_false_when_template_does_not_exist() {
		$file = vfsStream::url( 'templates/structure/does-not-exist.php' );

		$this->assertFileNotExists( $file );
		$this->assertFileNotExists( BEANS_STRUCTURE_PATH . 'does-not-exist.php' );
		$this->assertFalse( beans_load_default_template( $file ) );

		$this->assertFileNotExists( BEANS_STRUCTURE_PATH . basename( __FILE__ ) );
		$this->assertFalse( beans_load_default_template( __FILE__ ) );
	}

	/**
	 * Test beans_load_default_template() should return true after loading the given structure.
	 */
	public function test_should_return_true_after_loading_structure() {
		// Check with an invalid path to the structure.
		$file = '/path/to/structure/content.php';
		$this->assertFileExists( BEANS_STRUCTURE_PATH . basename( $file ) );
		ob_start();
		$this->assertTrue( beans_load_default_template( $file ) );
		$this->assertSame( $this->mock_filesystem->getChild( 'structure/content.php' )->getContent(), ob_get_clean() );

		// Check with an absolute path to the structure.
		$file = __DIR__ . 'fixtures/structure/header.php';
		$this->assertFileExists( BEANS_STRUCTURE_PATH . basename( $file ) );
		ob_start();
		$this->assertTrue( beans_load_default_template( $file ) );
		$this->assertSame( $this->mock_filesystem->getChild( 'structure/header.php' )->getContent(), ob_get_clean() );
	}
}
