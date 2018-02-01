<?php
/**
 * Tests the get_extension method of _Beans_Compiler.
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler;

use Beans\Framework\Tests\Unit\API\Compiler\Includes\Compiler_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-compiler-test-case.php';

/**
 * Class Tests_Beans_Compiler_Get_Extension
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   unit-tests
 * @group   api
 */
class Tests_Beans_Compiler_Get_Extension extends Compiler_Test_Case {

	/**
	 * Test get_extension() should return "css" when the type is "style".
	 */
	public function test_should_return_css_when_style() {
		$compiler = new \_Beans_Compiler( array( 'type' => 'style' ) );

		$this->assertSame( 'css', $compiler->get_extension() );
	}

	/**
	 * Test get_extension() should return "js" when the type is "script".
	 */
	public function test_should_return_js_when_script() {
		$compiler = new \_Beans_Compiler( array( 'type' => 'script' ) );

		$this->assertSame( 'js', $compiler->get_extension() );
	}

	/**
	 * Test get_extension() should return null when the type is invalid.
	 */
	public function test_should_return_null_when_invalid_type() {
		$compiler = new \_Beans_Compiler( array( 'type' => 'invalid' ) );
		$this->assertNull( $compiler->get_extension() );

		$compiler = new \_Beans_Compiler( array( 'type' => null ) );
		$this->assertNull( $compiler->get_extension() );

		$compiler = new \_Beans_Compiler( array( 'type' => false ) );
		$this->assertNull( $compiler->get_extension() );

		$compiler = new \_Beans_Compiler( array() );
		$this->assertNull( $compiler->get_extension() );
	}
}
