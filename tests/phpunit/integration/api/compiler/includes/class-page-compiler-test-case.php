<?php
/**
 * Test Case for Beans Page Compiler integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Compiler\Includes;

require_once __DIR__ . '/class-base-test-case.php';

/**
 * Abstract Class Page_Compiler_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler\Includes
 */
abstract class Page_Compiler_Test_Case extends Base_Test_Case {

	/**
	 * Set up the test before we run the test setups.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		set_current_screen( 'front' );

		require_once BEANS_THEME_DIR . '/lib/api/compiler/class-beans-page-compiler.php';
	}
}
