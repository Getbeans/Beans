<?php
/**
 * Tests for beans_deregister_widget_area()
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget;

use Beans\Framework\Tests\Integration\API\Widget\Includes\Beans_Widget_Test_Case;

require_once dirname( __FILE__ ) . '/includes/class-beans-widget-test-case.php';

/**
 * Class Tests_BeansDeregisterWidgetArea
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansDeregisterWidgetArea extends Beans_Widget_Test_Case {

	public function test_should_unregister_sidebar() {
		global $wp_registered_sidebars;

		$wp_registered_sidebars['unwanted-sidebar'] = array(
			'beans_type'                 => 'stack',
			'beans_show_widget_title'    => true,
			'beans_show_widget_badge'    => false,
			'beans_widget_badge_content' => __( 'Hello', 'tm-beans' ),
			'id'                         => 'unwanted-sidebar',
		);

		beans_deregister_widget_area( 'unwanted-sidebar' );

		$this->assertArrayNotHasKey( 'unwanted-sidebar', $wp_registered_sidebars );
	}


}