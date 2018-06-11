<?php
/**
 * Tests for beans_uikit_enqueue_components().
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\UIkit;

use _Beans_Uikit;
use Beans\Framework\Tests\Unit\API\UIkit\Includes\UIkit_Test_Case;

require_once __DIR__ . '/includes/class-uikit-test-case.php';

/**
 * Class Tests_BeansUikitEnqueueComponents
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 * @group   api
 * @group   api-uikit
 */
class Tests_BeansUikitEnqueueComponents extends UIkit_Test_Case {

	/**
	 * Test beans_uikit_enqueue_components() should add then given core component into registry when given a string.
	 */
	public function test_should_add_given_core_component_into_registry_when_given_string() {
		global $_beans_uikit_enqueued_items;

		// Test when the registry is empty.
		$this->assertEmpty( $_beans_uikit_enqueued_items['components']['core'] );
		beans_uikit_enqueue_components( 'alert', 'core', false );
		$expected = [ 'alert' ];
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components']['core'] );

		// Test when components are already in the registry.
		beans_uikit_enqueue_components( 'button', 'core', false );
		$expected[] = 'button';
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components']['core'] );
		beans_uikit_enqueue_components( 'overlay', 'core', false );
		$expected[] = 'overlay';
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components']['core'] );
	}

	/**
	 * Test beans_uikit_enqueue_components() should add then given add-ons component into registry when given a string.
	 */
	public function test_should_add_given_addons_component_into_registry_when_given_string() {
		global $_beans_uikit_enqueued_items;

		// Test when the registry is empty.
		$this->assertEmpty( $_beans_uikit_enqueued_items['components']['add-ons'] );
		beans_uikit_enqueue_components( 'accordion', 'add-ons', false );
		$expected = [ 'accordion' ];
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components']['add-ons'] );

		// Test when components are already in the registry.
		beans_uikit_enqueue_components( 'datepicker', 'add-ons', false );
		$expected[] = 'datepicker';
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components']['add-ons'] );
		beans_uikit_enqueue_components( 'sticky', 'add-ons', false );
		$expected[] = 'sticky';
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components']['add-ons'] );
	}

	/**
	 * Test beans_uikit_enqueue_components() should add the given core components into registry.
	 */
	public function test_should_add_given_core_components_into_registry() {
		global $_beans_uikit_enqueued_items;
		$components = [
			'alert',
			'button',
			'overlay',
		];

		// Test when the registry is empty.
		$this->assertEmpty( $_beans_uikit_enqueued_items['components']['core'] );
		beans_uikit_enqueue_components( $components, 'core', false );
		$this->assertSame( $components, $_beans_uikit_enqueued_items['components']['core'] );

		// Test when components are already in the registry.
		beans_uikit_enqueue_components( [ 'tab' ], 'core', false );
		$expected = array_merge( $components, [ 'tab' ] );
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components']['core'] );
	}

	/**
	 * Test beans_uikit_enqueue_components() should add the given add-ons components into registry.
	 */
	public function test_should_add_given_addons_components_into_registry() {
		global $_beans_uikit_enqueued_items;
		$components = [
			'accordion',
			'autocomplete',
			'datepicker',
			'sticky',
			'tooltip',
		];

		// Test when the registry is empty.
		$this->assertEmpty( $_beans_uikit_enqueued_items['components']['add-ons'] );
		beans_uikit_enqueue_components( $components, 'add-ons', false );
		$this->assertSame( $components, $_beans_uikit_enqueued_items['components']['add-ons'] );

		// Test when components are already in the registry.
		beans_uikit_enqueue_components( [ 'notify' ], 'add-ons', false );
		$expected = array_merge( $components, [ 'notify' ] );
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components']['add-ons'] );
	}
}
