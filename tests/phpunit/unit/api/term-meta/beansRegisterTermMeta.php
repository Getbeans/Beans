<?php
/** Tests for beans_register_term_meta()
 *
 * @package Beans\Framework\Tests\Unit\API\Term_Meta
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Term_Meta;

use Beans\Framework\Tests\Unit\API\Term_Meta\Includes\Beans_Term_Meta_Test_Case;
use Brain\Monkey;

require_once dirname( __FILE__ ) . '/includes/class-beans-term-meta-test-case.php';

/**
 * Class Tests_BeansRegisterPostMeta
 *
 * @package Beans\Framework\Tests\Unit\API\Term_Meta
 * @group   api
 * @group   api-term-meta
 */
class Tests_BeansRegisterTermMeta extends Beans_Term_Meta_Test_Case {

	/**
	 * Tests beans_register_term_meta() should return false when taxonomies are empty.
	 */
	public function test_should_return_false_when_taxonomise_are_empty() {
		Monkey\Functions\expect( '_beans_is_admin_term' )->once()->andReturn( false );
		$this->assertFalse( beans_register_term_meta(
				array(
					array(
						'id'    => 'field_id',
						'type'  => 'radio',
						'label' => 'Field Label',
					),
				),
				array(),
				'tm-beans' )
		);
	}
}
