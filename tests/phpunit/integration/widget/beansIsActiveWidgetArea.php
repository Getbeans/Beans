<?php
/**
 * Tests for beans_is_active_widget_area()
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget;

use Beans\Framework\Tests\Integration\API\Widget\Includes\Beans_Widget_Test_Case;

require_once dirname( __FILE__ ) . '/includes/class-beans-widget-test-case.php';

/**
 * Class Tests_BeansIsActiveWidgetArea
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansIsActiveWidgetArea extends Beans_Widget_Test_Case {

	/**
	 * Test beans_is_active_widget_area() should return false when widget area is not active.
	 */
	public function testShouldReturnFalseWhenWidgetAreaNotActive() {
		$this->assertFalse( beans_is_active_widget_area( 'inactive-widget-area' ) );
	}

	/**
	 * Test beans_is_active_widget_area() should return true when widget area is active.
	 */
	public function testShouldReturnTrueWhenWidgetAreaIsActive() {
		global $_wp_sidebars_widgets;

		// Clear global widget areas registry so we can start clean.
		$_wp_sidebars_widgets = [];

		// Prime the WP database with an active sidebar.
		update_option( 'sidebars_widgets', array( 'an-active-sidebar' => array( 'text-2' ) ) );

		$this->assertTrue( beans_is_active_widget_area( 'an-active-sidebar' ) );
	}
}