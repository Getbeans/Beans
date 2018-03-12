<?php
/**
 * Tests for beans_add_attributes().
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\HTML;

use Beans\Framework\Tests\Unit\API\HTML\Includes\HTML_Test_Case;
use Brain\Monkey;

require_once __DIR__ . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansAddAttributes
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansAddAttributes extends HTML_Test_Case {

	/**
	 * Setup dependency function mocks.
	 */
	protected function setup_mocks() {
		parent::setup_mocks();

		Monkey\Functions\when( 'wp_parse_args' )->alias( function ( $args ) {

			if ( is_array( $args ) ) {
				return $args;
			}

			if ( is_object( $args ) ) {
				return get_object_vars( $args );
			}

			if ( is_string( $args ) ) {
				parse_str( $args, $result );

				return $result;
			}
		} );
	}

	/**
	 * Test beans_add_attributes() should return false when the option does not exist.
	 */
	public function test_should_register_attributes() {
		Monkey\Filters\expectApplied( 'foo_attribute' )->once()->with( array(
			'foo_attrbutes',
			array( 'data-foo' => 'beans' ),
		) );

		$this->assertSame( 'data-foo="beans"', beans_add_attributes( 'foo', array( 'data-foo' => 'beans' ) ) );
	}
}
