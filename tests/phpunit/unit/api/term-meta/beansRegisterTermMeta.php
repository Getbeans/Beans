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
	public function tests_should_return_false_when_taxonomies_are_empty() {
		Monkey\Functions\expect( '_beans_is_admin_term' )
			->once()
			->with( array() )
			->andReturn( false );
		$this->assertFalse( beans_register_term_meta( static::$test_data, array(), 'tm-beans' ) );
	}

	/**
	 * Tests beans_register_term_meta() should return false when not is_admin().
	 */
	public function tests_should_return_false_when_not_is_admin() {
		Monkey\Functions\expect( '_beans_is_admin_term' )
			->once()
			->with( array( 'sample-taxonomy' ) )
			->andReturn( true );
		Monkey\Functions\expect( 'is_admin' )->once()->andReturn( false );

		$this->assertFalse(
			beans_register_term_meta(
				static::$test_data,
				'sample-taxonomy',
				'tm-beans'
			)
		);
	}

	/**
	 * Tests beans_register_term_meta() should return false when fields cannot be registered.
	 */
	public function test_should_return_false_when_fields_cannot_be_registered() {
		Monkey\Functions\expect( '_beans_is_admin_term' )
			->once()
			->with( array( 'sample-taxonomy' ) )
			->andReturn( true );
		Monkey\Functions\expect( 'is_admin' )->once()->andReturn( true );
		Monkey\Functions\expect( 'beans_register_fields' )
			->once()
			->with( static::$test_data, 'term_meta', 'tm-beans' )
			->andReturn( false );

		$this->assertFalse(
			beans_register_term_meta( static::$test_data,
				'sample-taxonomy',
				'tm-beans'
			)
		);
	}

	/**
	 * Tests beans_register_term_meta() should return true when term meta fields are successfully registered.
	 */
	public function test_should_return_true_when_fields_are_successfully_registered() {
		Monkey\Functions\expect( '_beans_is_admin_term' )
			->once()
			->with( array( 'sample-taxonomy' ) )
			->andReturn( true );
		Monkey\Functions\expect( 'is_admin' )->once()->andReturn( true );
		Monkey\Functions\expect( 'beans_register_fields' )
			->once()
			->with( static::$test_data, 'term_meta', 'tm-beans' )
			->andReturn( true );

		$this->assertTrue(
			beans_register_term_meta(
				static::$test_data,
				'sample-taxonomy',
				'tm-beans'
			)
		);
	}
}
