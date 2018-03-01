<?php
/**
 * Tests for remove() method of the _Beans_Attribute.
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
 * Class Tests_Beans_Attribute_Remove
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 * @group   unit-tests
 * @group   api
 */
class Tests_Beans_Attribute_Remove extends HTML_Test_Case {

	/**
	 * Test remove() should return the original attributes when the target attribute does not exist, meaning there's
	 * nothing to remove in the given attributes.
	 */
	public function test_should_return_original_attributes_when_target_attribute_does_not_exist() {
		$attributes = array(
			'href'  => 'http://example.com/image.png',
			'title' => 'Some cool image',
		);

		$instance = new _Beans_Attribute( 'beans_post', 'itemprop' );
		$this->assertArrayNotHasKey( 'itemprop', $attributes );
		$this->assertSame( $attributes, $instance->remove( $attributes ) );

		$attributes = array(
			'id'        => 47,
			'class'     => 'uk-article uk-panel-box category-beans',
			'itemscope' => 'itemscope',
			'itemtype'  => 'http://schema.org/blogPost',
			'itemprop'  => 'beans_post',
		);
		$instance   = new _Beans_Attribute( 'beans_post', 'data-test', 'uk-panel-box', 'beans-test' );
		$this->assertArrayNotHasKey( 'data-test', $attributes );
		$this->assertSame( $attributes, $instance->remove( $attributes ) );
	}

	/**
	 * Test remove() should remove the attribute when the given value is null.
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
			$instance = new _Beans_Attribute( 'beans_post', $name, null );
			$this->assertArrayNotHasKey( $name, $instance->remove( $attributes ) );
		}
	}

	/**
	 * Test remote() should remove the given value from the attribute.
	 */
	public function test_should_remove_the_given_value_from_attribute() {
		$attributes = array(
			'id'        => 47,
			'class'     => 'uk-article uk-panel-box category-beans',
			'itemscope' => 'itemscope',
			'itemtype'  => 'http://schema.org/blogPost',
			'itemprop'  => 'beans_post',
		);

		$instance = new _Beans_Attribute( 'beans_post', 'class', 'uk-panel-box' );

		// Check that it removed only that attribute.
		$actual = $instance->remove( $attributes );
		$this->assertNotContains( 'uk-panel-box', $actual['class'] );
		$this->assertSame( 'uk-article  category-beans', $actual['class'] );

		// Check that only the class attribute is affected.
		$expected          = $attributes;
		$expected['class'] = 'uk-article  category-beans';
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test remove() should the empty array.
	 */
	public function test_should_return_original_empty_array() {
		$instance = new _Beans_Attribute( 'beans_post', 'class', 'foo', 'beans-test' );
		$actual   = $instance->remove( array() );

		$this->assertSame( array(), $actual );
	}
}
