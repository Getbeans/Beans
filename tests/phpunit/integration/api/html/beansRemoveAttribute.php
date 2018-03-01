<?php
/**
 * Tests for beans_remove_attribute()
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\HTML;

use Beans\Framework\Tests\Integration\API\HTML\Includes\HTML_Test_Case;

require_once __DIR__ . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansRemoveAttribute
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 * @group   integration-tests
 * @group   api
 */
class Tests_BeansRemoveAttribute extends HTML_Test_Case {

	/**
	 * Test beans_remove_attribute() should return the instance and register the "remove" callback to the given ID.
	 */
	public function test_should_return_instance_and_register_callback_to_given_id() {

		foreach ( static::$test_attributes as $beans_id => $markup ) {
			$instance = beans_remove_attribute( $beans_id, 'data-test', 'test' );

			// Check that it returns an instance of _Beans_Attribute.
			$this->assertInstanceOf( \_Beans_Attribute::class, $instance );

			// Check that the object's "remove" method is registered to the filter event for the given ID.
			$this->assertSame( 10, has_filter( "{$beans_id}_attributes", array( $instance, 'remove' ), 10 ) );

			// Clean up.
			remove_filter( "{$beans_id}_attributes", array( $instance, 'remove' ) );
		}
	}

	/**
	 * Test beans_remove_attribute() should return the original attributes when the target attribute does not exist,
	 * meaning there's nothing to remove in the given attributes.
	 */
	public function test_should_return_original_attributes_when_target_attribute_does_not_exist() {
		$attributes = array(
			'href'  => 'http://example.com/image.png',
			'title' => 'Some cool image',
		);

		$instance = beans_remove_attribute( 'beans_post', 'itemprop' );

		// Check that the new attribute does not exist yet.
		$this->assertArrayNotHasKey( 'itemprop', $attributes );

		// Run the full systems test by applying the filter.
		$this->assertSame( $attributes, apply_filters( 'beans_post_attributes', $attributes ) );

		// Clean up.
		remove_filter( 'beans_post_attributes', array( $instance, 'remove' ), 10 );
	}

	/**
	 * Test beans_remove_attribute() should remove the attribute when the given value is null.
	 */
	public function test_should_remove_attribute_when_value_is_null() {
		$attributes = array(
			'id'        => 47,
			'class'     => 'uk-article uk-panel-box category-beans',
			'itemscope' => 'itemscope',
			'itemtype'  => 'http://schema.org/blogPost',
			'itemprop'  => 'beans_post',
		);

		foreach ( $attributes as $name => $value ) {
			$instance = beans_remove_attribute( 'beans_test_post', $name, null );

			// Run the full systems test by applying the filter.
			$this->assertArrayNotHasKey( $name, apply_filters( 'beans_test_post_attributes', $attributes ) );

			// Clean up.
			remove_filter( 'beans_test_post_attributes', array( $instance, 'remove' ), 10 );
		}
	}

	/**
	 * Test beans_remove_attribute() should remove the given value from the attribute.
	 */
	public function test_should_remove_the_given_value_from_attribute() {
		$attributes = array(
			'id'        => 47,
			'class'     => 'uk-article uk-panel-box category-beans',
			'itemscope' => 'itemscope',
			'itemtype'  => 'http://schema.org/blogPost',
			'itemprop'  => 'beans_post',
		);

		$instance = beans_remove_attribute( 'beans_test', 'class', 'uk-panel-box' );

		// Run the full systems test by applying the filter.
		$actual = apply_filters( 'beans_test_attributes', $attributes );

		// Check that it removed only that attribute.
		$this->assertNotContains( 'uk-panel-box', $actual['class'] );
		$this->assertSame( 'uk-article  category-beans', $actual['class'] );

		// Check that only the class attribute is affected.
		$expected          = $attributes;
		$expected['class'] = 'uk-article  category-beans';
		$this->assertSame( $expected, $actual );

		// Clean up.
		remove_filter( 'beans_test_attributes', array( $instance, 'remove' ), 10 );
	}

	/**
	 * Test beans_remove_attribute() should the empty array.
	 */
	public function test_should_return_original_empty_array() {
		$instance = beans_remove_attribute( 'beans_test', 'class', 'foo', 'beans-test' );

		// Run the full systems test by applying the filter.
		$this->assertSame( array(), apply_filters( 'beans_test_attributes', array() ) );

		// Clean up.
		remove_filter( 'beans_test_attributes', array( $instance, 'remove' ), 10 );
	}
}
