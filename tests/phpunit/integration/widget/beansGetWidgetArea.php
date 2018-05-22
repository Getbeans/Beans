<?php
/**
 * Tests for beans_get_widget_area()
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget;

use Beans\Framework\Tests\Integration\API\Widget\Includes\Beans_Widget_Test_Case;

require_once dirname( __FILE__ ) . '/includes/class-beans-widget-test-case.php';

/**
 * Class Tests_BeansGeteWidgetArea
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansGetWidgetArea extends Beans_Widget_Test_Case {

	/**
	 * Test beans_get_widget_area() should return false when widget area data is not found.
	 */
	public function test_should_return_false_when_widget_area_data_not_found() {
		$this->assertFalse( beans_get_widget_area( 'bogus-needle' ) );
		$this->assertFalse( beans_get_widget_area() );
	}

	/**
	 * Test beans_get_widget_area() should return all widget area data when needle is not specified.
	 */
	public function test_should_return_all_data_when_needle_not_specified() {
		global $_beans_widget_area;

		register_sidebar( array( 'id' => 'beans_test_sidebar', 'name' => 'Beans Test Sidebar') );
		_beans_setup_widget_area( 'beans_test_sidebar' );

		$this->assertSame( $this->get_expected_sidebar_data(), beans_get_widget_area() );
	}

	/**
	 * Test beans_get_widget_area() should return specific widget data when a needle is specified.
	 */
	public function test_should_return_specific_widget_data_when_needle_specified() {
		global $_beans_widget_area;

		register_sidebar( array( 'id' => 'beans_test_sidebar', 'name' => 'Beans Test Sidebar') );
		_beans_setup_widget_area( 'beans_test_sidebar' );

		$this->assertEquals( 'beans_test_sidebar', beans_get_widget_area( 'id' ) );
	}

	protected function get_expected_sidebar_data() {
		return array(
			'name'           => 'Beans Test Sidebar',
			'id'             => 'beans_test_sidebar',
			'description'    => '',
			'class'          => '',
			'before_widget'  => '<!--widget-%1$s-->',
			'after_widget'   => '<!--widget-end-->',
			'before_title'   => '<!--title-start-->',
			'after_title'    => '<!--title-end-->',
			'widgets_count'  => 0,
			'current_widget' => 0,
		);
	}
}