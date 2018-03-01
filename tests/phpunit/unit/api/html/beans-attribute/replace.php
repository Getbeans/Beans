<?php
/**
 * Tests for replace() method of the _Beans_Attribute.
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\HTML;

use _Beans_Attribute;
use Beans\Framework\Tests\Unit\API\HTML\Includes\HTML_Test_Case;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-html-test-case.php';

/**
 * Class Tests_Beans_Attribute_Replace
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 * @group   unit-tests
 * @group   api
 */
class Tests_Beans_Attribute_Replace extends HTML_Test_Case {

	/**
	 * Test replace() should replace an existing attribute value.
	 */
	public function test_should_replace_existing_attribute_value() {
		$instance   = new _Beans_Attribute( 'beans_post', 'class', 'uk-panel-box', 'beans-test' );
		$attributes = array(
			'id'        => 47,
			'class'     => 'uk-article uk-panel-box category-beans',
			'itemscope' => 'itemscope',
			'itemtype'  => 'http://schema.org/blogPost',
			'itemprop'  => 'beans_post',
		);

		// Check that the attribute does not contain the new value.
		$this->assertNotContains( 'beans-test', $attributes['class'] );

		// Run the replace.
		$actual = $instance->replace( $attributes );

		// Check that the attribute is added with the given value.
		$this->assertContains( 'beans-test', $actual['class'] );
		$this->assertNotContains( 'uk-panel-box', $actual['class'] );
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
			$instance = new _Beans_Attribute( 'beans_post', $name );
			$actual   = $instance->replace( $attributes );
			$this->assertNull( $actual[ $name ] );

			// Check when the value is null.
			$instance = new _Beans_Attribute( 'beans_post', $name, null, '' );
			$actual   = $instance->replace( $attributes );
			$this->assertSame( '', $actual[ $name ] );

			// Check when the value is an empty string.
			$instance = new _Beans_Attribute( 'beans_post', $name, '', 'foo' );
			$actual   = $instance->replace( $attributes );
			$this->assertSame( 'foo', $actual[ $name ] );
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

		$instance = new _Beans_Attribute( 'beans_post', 'data-test', 'foo', 'beans-test' );

		// Check that the attribute does not exist.
		$this->assertArrayNotHasKey( 'data-test', $attributes );

		// Run the replace.
		$actual = $instance->replace( $attributes );

		// Check that the new attribute was added.
		$this->assertArrayHasKey( 'data-test', $actual );
		$this->assertSame( 'beans-test', $actual['data-test'] );

		// Check that only the data-test attribute has affected.
		$expected              = $attributes;
		$expected['data-test'] = 'beans-test';
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test replace() should add the attribute when an empty array is given.
	 */
	public function test_should_add_attribute_when_an_empty_array_given() {
		$instance = new _Beans_Attribute( 'beans_post', 'class', 'foo', 'beans-test' );
		$actual   = $instance->replace( array() );

		$this->assertArrayHasKey( 'class', $actual );
		$this->assertSame( 'beans-test', $actual['class'] );
	}
}
