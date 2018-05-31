<?php
/**
 * Tests for beans_setup_widget()
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
 * Class Tests_BeansSetupWidget
 *
 * @package Beans\Framework\Tests\Unit\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansSetupWidget extends Beans_Widget_Test_Case {

	/**
	 * Test beans_setup_widget() should return false when the widget ID can't be found.
	 */
	public function test_should_return_false_when_widget_id_not_found() {
		global $_beans_widget_area;

		$_beans_widget_area = array(
			'widgets'        => array(
				'text-1' => array( 'id' => 'text-1' ),
				'text-2' => array( 'id' => 'text-2' ),
			),
			'current_widget' => 2,
		);

		Monkey\Functions\expect( 'beans_get' )
			->once()
			->with( $_beans_widget_area['current_widget'], array( 'text-1', 'text-2' ) )
			->andReturn( false );

		$this->assertFalse( beans_setup_widget() );
	}

	/**
	 * Test beans_setup_widget() should advance widget pointer, prepare widget data, and return true when a widget ID is found.
	 */
	public function test_should_return_advance_widget_pointer_prepare_widget_data_and_return_true_when_widget_id_is_found() {
		global $_beans_widget_area;

		$_beans_widget_area = array(
			'widgets'        => array(
				'text-1' => array( 'id' => 'text-1' ),
				'text-2' => array( 'id' => 'text-2' ),
			),
			'current_widget' => 1,
		);

		Monkey\Functions\expect( 'beans_get' )
			->once()
			->with( $_beans_widget_area['current_widget'], array( 'text-1', 'text-2' ) )
			->andReturn( 'text-2' );

		Monkey\Functions\expect( '_beans_prepare_widget_data' )
			->once()
			->with( 'text-2' );

		// Run test.
		$this->assertTrue( beans_setup_widget() );

		// Verify widget pointer has advanced.
		$this->assertEquals( 2, $_beans_widget_area['current_widget'] );
	}
}
