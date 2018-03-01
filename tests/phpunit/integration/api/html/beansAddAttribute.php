<?php
/**
 * Tests for beans_add_attribute()
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\HTML;

use Beans\Framework\Tests\Integration\API\HTML\Includes\HTML_Test_Case;

require_once __DIR__ . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansAddAttribute
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 * @group   integration-tests
 * @group   api
 */
class Tests_BeansAddAttribute extends HTML_Test_Case {

	/**
	 * Test beans_add_attribute() should return register the "add" callback to the given ID.
	 */
	public function test_should_register_the_add_callback_to_given_id() {
		$instance = beans_add_attribute( 'foo', 'data-test', 'test' );

		$this->assertInstanceOf( \_Beans_Attribute::class, $instance );
		$this->assertSame( 10, has_filter( 'foo_attributes', array( $instance, 'add' ), 10 ) );

		// Clean up.
		remove_filter( 'foo_attributes', array( $instance, 'add' ) );
	}

	/**
	 * Test beans_add_attribute() should add the attribute when it does not exist in the given attributes.
	 */
	public function test_should_add_the_attribute_when_does_not_exist() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$hook     = $beans_id . '_attributes';
			$instance = beans_add_attribute( $beans_id, 'data-test', 'foo' );

			// Run the full systems test by applying the filter.
			$expected              = $markup['attributes'];
			$expected['data-test'] = 'foo';
			$this->assertSame( $expected, apply_filters( $hook, $markup['attributes'] ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- The hook's name is in the value.

			// Clean up.
			remove_filter( $hook, array( $instance, 'add' ), 10 );
		}
	}

	/**
	 * Test beans_add_attribute() should add the value to an existing attribute's values.
	 */
	public function test_should_add_value_to_existing_attribute_values() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$hook     = $beans_id . '_attributes';
			$name     = key( $markup['attributes'] );
			$instance = beans_add_attribute( $beans_id, $name, 'beans-test' );

			// Run the full systems test by applying the filter.
			$expected           = $markup['attributes'];
			$expected[ $name ] .= ' beans-test';
			$this->assertSame( $expected, apply_filters( $hook, $markup['attributes'] ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- The hook's name is in the value.

			// Clean up.
			remove_filter( $hook, array( $instance, 'add' ), 10 );
		}
	}
}
