<?php
/**
 * Tests for beans_add_attributes().
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\HTML;

use Beans\Framework\Tests\Integration\API\HTML\Includes\HTML_Test_Case;

require_once __DIR__ . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansAddAttributes
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansAddAttributes extends HTML_Test_Case {

	/**
	 * Test beans_add_attributes() should return the built attributes string when an array of attributes is given.
	 */
	public function test_should_return_built_attributes_string_when_array_given() {

		foreach ( static::$test_attributes as $id => $config ) {
			$expected = $this->convert_attributes_into_html( $config['attributes'] );
			$this->assertSame( $expected, beans_add_attributes( $id, $config['attributes'] ) );
		}
	}

	/**
	 * Test beans_add_attributes() should return the built attributes string when a query string of attributes is given.
	 */
	public function test_should_return_built_attributes_string_when_query_string_given() {
		$this->assertSame( 'id="test"', beans_add_attributes( 'foo', 'id=test' ) );
		$this->assertSame( 'class="foo" data-foo="test"', beans_add_attributes( 'foo', 'class=foo&data-foo=test' ) );
		$this->assertSame( 'class="test" style="color:#fff;"', beans_add_attributes( 'beans-test', 'class=test&style=color:#fff;' ) );
	}

	/**
	 * Test beans_add_attributes() should return empty string when no attributes.
	 */
	public function test_should_return_empty_string_when_no_attributes() {
		$this->assertSame( '', beans_add_attributes( 'foo' ) );
		$this->assertSame( '', beans_add_attributes( 'foo', null ) );
		$this->assertSame( '', beans_add_attributes( 'foo', '' ) );
		$this->assertSame( '', beans_add_attributes( 'foo', false ) );
	}
}
