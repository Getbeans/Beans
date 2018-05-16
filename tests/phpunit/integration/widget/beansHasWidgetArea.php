<?php
/**
 * Tests for beans_has_widget_area()
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget;

use Beans\Framework\Tests\Integration\API\Widget\Includes\Beans_Widget_Test_Case;

require_once dirname( __FILE__ ) . '/includes/class-beans-widget-test-case.php';

/**
 * Class Tests_BeansHasWidgetArea
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansHasWidgetArea extends Beans_Widget_Test_Case {

	/**
	 * Test beans_has_widget_area() should return false when widget area is not registered.
	 */
	public function testShouldReturnFalseWhenWidgetAreaNotRegistered() {
		$this->assertFalse( beans_has_widget_area( 'unregistered-area' ) );
	}

	/**
	 * Test beans_has_widget_area() should return true when widget area is registered.
	 */
	public function testShouldReturnTrueWhenWidgetAreaRegistered() {
		$this->assertTrue( beans_has_widget_area( 'sidebar_primary' ) );
	}
}