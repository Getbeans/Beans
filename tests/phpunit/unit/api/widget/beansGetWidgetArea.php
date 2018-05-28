<?php
/**
 * Tests for beans_get_widget_area()
 *
 * @package Beans\Framework\Tests\Unit\API\Widget
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Widget;

use Beans\Framework\Tests\Unit\API\Widget\Includes\Beans_Widget_Test_Case;
use Brain\Monkey;

require_once dirname( __FILE__ ) . '/includes/class-beans-widget-test-case.php';

/**
 * Class Tests_BeansGetWidgetArea
 *
 * @package Beans\Framework\Tests\Unit\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansGetWidgetArea extends Beans_Widget_Test_Case {

	/**
	 * Test beans_get_widget_area() should return all widget area data when the needle is unspecified.
	 */
	public function test_should_return_all_widget_area_data_when_needle_unspecified() {
		global $_beans_widget_area;
	}
}
