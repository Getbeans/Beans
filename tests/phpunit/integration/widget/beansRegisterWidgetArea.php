<?php
/**
 * Tests for beans_register_widget_area()
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget;

use Beans\Framework\Tests\Integration\API\Widget\Includes\Beans_Widget_Test_Case;

require_once dirname( __FILE__ ) . '/includes/class-beans-widget-test-case.php';

/**
 * Class Tests_BeansRegisterWidgetArea
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansRegisterWidgetArea extends Beans_Widget_Test_Case {

	/**
	 * Test beans_register_widget_area() should return an empty string when the id is not set.
	 */
	public function test_should_return_empty_string_when_ID_not_set() {
		$this->assertSame( '', beans_register_widget_area( [] ) );
	}

	/**
	 * Test beans_register_widget_area() should return the widget area id when the widget area is registered.
	 */
	public function test_should_return_widget_area_ID_when_widget_area_registered() {
		$this->assertEquals(
			'new-widget-area',
			beans_register_widget_area( array(
				'id'         => 'new-widget-area',
				'beans_type' => 'grid',
			) ) );
	}

}