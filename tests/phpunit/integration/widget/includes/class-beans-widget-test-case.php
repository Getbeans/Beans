<?php
/**
 * Test Case for the Beans Widget API integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Widget\Includes
 *
 * @since 1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget\Includes;

use Beans\Framework\Tests\Integration\Test_Case;

/**
 * Abstract Class Beans_Widget_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\Widget\Includes
 */
abstract class Beans_Widget_Test_Case extends Test_Case {

	/**
	 * Fixture to clean up after tests.
	 */
	public function tearDown() {
		unset( $GLOBALS['current_screen'] );
		$this->clean_up_global_scope();

		parent::tearDown();
	}
}
