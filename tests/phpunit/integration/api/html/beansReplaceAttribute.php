<?php
/**
 * Tests for beans_replace_attribute()
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\HTML;

use Beans\Framework\Tests\Integration\API\HTML\Includes\HTML_Test_Case;

require_once __DIR__ . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansReplaceAttribute
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 * @group   integration-tests
 * @group   api
 */
class Tests_BeansReplaceAttribute extends HTML_Test_Case {

	/**
	 * Test beans_replace_attribute() should return register the "replace" callback to the given ID.
	 */
	public function test_should_register_the_add_callback_to_given_id() {
		$instance = beans_replace_attribute( 'foo', 'class', 'uk-panel-box', 'beans-test' );

		$this->assertInstanceOf( \_Beans_Attribute::class, $instance );
		$this->assertSame( 10, has_filter( 'foo_attributes', array( $instance, 'replace' ), 10 ) );

		// Clean up.
		remove_filter( 'foo_attributes', array( $instance, 'replace' ) );
	}

	/**
	 * Test beans_replace_attribute() should replace an existing attribute value.
	 */
	public function test_should_replace_existing_attribute_value() {
		$attributes = array(
			'id'        => 47,
			'class'     => 'uk-article uk-panel-box category-beans',
			'itemscope' => 'itemscope',
			'itemtype'  => 'http://schema.org/blogPost',
			'itemprop'  => 'beans_post',
		);

		$instance = beans_replace_attribute( 'beans_post', 'class', 'uk-panel-box', 'beans-test' );

		// Check that the attribute does not contain the new value.
		$this->assertNotContains( 'beans-test', $attributes['class'] );

		// Run the full systems test by applying the filter.
		$actual = apply_filters( 'beans_post_attributes', $attributes );

		// Check that the attribute is added with the given value.
		$this->assertContains( 'beans-test', $actual['class'] );
		$this->assertNotContains( 'uk-panel-box', $actual['class'] );

		// Clean up.
		remove_filter( 'beans_post_attributes', array( $instance, 'replace' ), 10 );
	}

	/**
	 * Test replace() should replace (overwrite) all attribute's values with the new value when the target value is
	 * empty (null, empty string, etc.).
	 */
	public function test_should_overwrite_attribute_values_with_new_value() {
		$attributes = array(
			'id'        => 47,
			'class'     => 'uk-article uk-panel-box category-beans',
			'itemscope' => 'itemscope',
			'itemtype'  => 'http://schema.org/blogPost',
			'itemprop'  => 'beans_post',
		);

		foreach ( $attributes as $name => $value ) {
			// Check when both value and new value are null.
			$instance = beans_replace_attribute( 'beans_post', $name, null );
			$actual   = apply_filters( 'beans_post_attributes', $attributes );
			$this->assertNull( $actual[ $name ] );

			// Clean up.
			remove_filter( 'beans_post_attributes', array( $instance, 'replace' ), 10 );

			// Check when the value is null.
			$instance = beans_replace_attribute( 'beans_post', $name, null, '' );
			$actual   = apply_filters( 'beans_post_attributes', $attributes );
			$this->assertSame( '', $actual[ $name ] );

			// Clean up.
			remove_filter( 'beans_post_attributes', array( $instance, 'replace' ), 10 );

			// Check when the value is an empty string.
			$instance = beans_replace_attribute( 'beans_post', $name, '', 'foo' );
			$actual   = apply_filters( 'beans_post_attributes', $attributes );
			$this->assertSame( 'foo', $actual[ $name ] );

			// Clean up.
			remove_filter( 'beans_post_attributes', array( $instance, 'replace' ), 10 );
		}
	}

	/**
	 * Test replace() should add the attribute when it does not exists in the given attributes.
	 */
	public function test_should_add_attribute_when_does_not_exist() {
		$attributes = array(
			'id'        => 47,
			'class'     => 'uk-article uk-panel-box category-beans',
			'itemscope' => 'itemscope',
			'itemtype'  => 'http://schema.org/blogPost',
			'itemprop'  => 'beans_post',
		);

		$instance = beans_replace_attribute( 'beans_post', 'data-test', 'foo', 'beans-test' );

		// Check that the attribute does not exist.
		$this->assertArrayNotHasKey( 'data-test', $attributes );

		// Run the full systems test by applying the filter.
		$actual = apply_filters( 'beans_post_attributes', $attributes );

		// Check that the new attribute was added.
		$this->assertArrayHasKey( 'data-test', $actual );
		$this->assertSame( 'beans-test', $actual['data-test'] );

		// Check that only the data-test attribute has affected.
		$expected              = $attributes;
		$expected['data-test'] = 'beans-test';
		$this->assertSame( $expected, $actual );

		// Clean up.
		remove_filter( 'beans_post_attributes', array( $instance, 'replace' ), 10 );
	}

	/**
	 * Test replace() should add the attribute when an empty array is given.
	 */
	public function test_should_add_attribute_when_an_empty_array_given() {
		$instance = beans_replace_attribute( 'beans_post', 'class', 'foo', 'beans-test' );

		// Run the full systems test by applying the filter.
		$actual = apply_filters( 'beans_post_attributes', array() );

		// Run the tests.
		$this->assertArrayHasKey( 'class', $actual );
		$this->assertSame( 'beans-test', $actual['class'] );

		// Clean up.
		remove_filter( 'beans_post_attributes', array( $instance, 'replace' ), 10 );
	}
}
