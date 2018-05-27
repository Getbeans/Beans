<?php
/**
 * Test Case for the Beans Widget API integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Widget\Includes
 *
 * @since 1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget\Includes;

use Beans\Framework\Tests\Integration\Test_Case;

/**
 * Abstract Class Beans_Widget_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\Widget\Includes
 */
abstract class Beans_Widget_Test_Case extends Test_Case {

	/**
	 * Fixture to clean up after tests.
	 */
	public function tearDown() {
		unset( $GLOBALS['current_screen'] );
		$this->clean_up_global_scope();

		parent::tearDown();
	}

	/**
	 * Register a test widget into a test sidebar.
	 */
	protected function add_test_widget_to_test_sidebar() {
		global $wp_registered_widgets;

		$widgetObject                         = new \WP_Widget_Text();
		$widgetObject->id                     = 'text-2';
		$widgetObject->name                   = 'Test Widget';
		$widgetObject->widget_options['text'] = 'Test Text Content';
		$widgetObject->widget_options['name'] = 'Test Sidebar';
		$widgetObject->widget_options['id']   = 'test_sidebar';

		$wp_registered_widgets[ $widgetObject->id ] = array(
			'name'      => $widgetObject->name,
			'id'        => $widgetObject->id,
			'callback'  => array( $widgetObject, 'widget' ),
			'params'    => array( $widgetObject->widget_options ),
			'classname' => $widgetObject->widget_options['classname']
		);

		add_filter( 'sidebars_widgets', array( $this, 'add_a_widget' ) );
	}

	/**
	 * Callback to add test widget via 'sidebars_widgets' filter.
	 */
	public function add_a_widget( $sidebars_widgets ) {
		$sidebars_widgets['test_sidebar'] = array( 0 => 'text-2' );

		return $sidebars_widgets;
	}

}
