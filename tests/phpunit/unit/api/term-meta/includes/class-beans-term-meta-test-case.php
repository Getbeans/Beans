<?php
/**
 * Test Case for Beans' Term_Meta API unit tests.
 *
 * @package Beans\Framework\Tests\Unit\API\Term_Meta\Includes
 *
 * @since 1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Term_Meta\Includes;

use Beans\Framework\Tests\Unit\Test_Case;

/**
 * Abstract Class Beans_term_Meta_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\Term_Meta\Includes
 */
abstract class Beans_Term_Meta_Test_Case extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		$this->load_original_functions( array(
			'api/term-meta/class-beans-term-meta.php',
			'api/term-meta/functions.php',
			'api/term-meta/functions-admin.php',
			'api/fields/functions.php',
			'api/utilities/functions.php',
		) );

		$this->setup_common_wp_stubs();
	}

}
