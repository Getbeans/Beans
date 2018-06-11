<?php
/**
 * Tests for beans_uikit_enqueue_components().
 *
 * @package Beans\Framework\Tests\Integration\API\UIkit
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\UIkit;

use Beans\Framework\Tests\Integration\API\UIkit\Includes\UIkit_Test_Case;

require_once __DIR__ . '/includes/class-uikit-test-case.php';

/**
 * Class Tests_BeansUikitEnqueueComponents
 *
 * @package Beans\Framework\Tests\Integration\API\UIkit
 * @group   api
 * @group   api-uikit
 */
class Tests_BeansUikitEnqueueComponents extends UIkit_Test_Case {

	/**
	 * Test beans_uikit_enqueue_components() should add the given core components and each (autoload) dependency into
	 * registry.
	 */
	public function test_should_add_given_core_components_and_each_dependency_into_registry() {
		global $_beans_uikit_enqueued_items;
		$components = [
			'alert',
			'button',
			'overlay',
			'tab',
		];

		$this->assertEmpty( $_beans_uikit_enqueued_items['components']['core'] );
		beans_uikit_enqueue_components( $components, 'core', true );
		$expected = array_merge( [ 'flex', 'switcher' ], $components );
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components']['core'] );

		$expected[] = 'close';
		$expected[] = 'modal';
		beans_uikit_enqueue_components( 'modal', 'core', true );
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components']['core'] );

		$expected[] = 'animation';
		$expected[] = 'scrollspy';
		beans_uikit_enqueue_components( 'scrollspy', 'core', true );
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components']['core'] );
	}

	/**
	 * Test beans_uikit_enqueue_components() should add the given addons components and each (autoload) dependency into
	 * registry.
	 */
	public function test_should_add_given_addons_components_and_each_dependency_into_registry() {
		global $_beans_uikit_enqueued_items;

		$this->assertEmpty( $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertEmpty( $_beans_uikit_enqueued_items['components']['add-ons'] );
		beans_uikit_enqueue_components( 'lightbox', 'add-ons', true );
		$expected = [
			'core'    => [
				'animation',
				'flex',
				'close',
				'modal',
				'overlay',
			],
			'add-ons' => [
				'slidenav',
				'lightbox',
			],
		];
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components'] );

		beans_uikit_enqueue_components( 'slider', 'add-ons', true );
		$expected['add-ons'][] = 'slidenav';
		$expected['add-ons'][] = 'slider';
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components'] );

		beans_uikit_enqueue_components( [ 'accordion', 'autocomplete', 'slideset' ], 'add-ons', true );
		$expected['core']  = array_merge( $expected['core'], [ 'animation', 'flex' ] );
		$expected['add-ons'] = array_merge( $expected['add-ons'], [ 'dotnav', 'slidenav', 'accordion', 'autocomplete', 'slideset' ] );
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components'] );
	}
}
