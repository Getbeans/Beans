<?php
/**
 * Test Case for Beans Compiler API Options integration tests.
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler\Includes;

use Beans\Framework\Tests\Unit\Test_Case;

/**
 * Abstract Class Page_Compiler_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler\Includes
 */
abstract class Page_Compiler_Test_Case extends Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->load_original_functions( array(
			'api/compiler/class-beans-page-compiler.php',
			'api/options/functions.php',
		) );
	}
}
