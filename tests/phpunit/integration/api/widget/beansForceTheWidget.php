<?php
/**
 * Tests for _beans_force_the_widget()
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget;

use Beans\Framework\Tests\Integration\API\Widget\Includes\Beans_Widget_Test_Case;
use \WP_Widget_Text;

require_once dirname( __FILE__ ) . '/includes/class-beans-widget-test-case.php';

/**
 * Class Tests_BeansForceTheWidget
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansForceTheWidget extends Beans_Widget_Test_Case {

	public function test_should_do_nothing_when_widget_not_instance_of_WP_Widget() {
		global $wp_widget_factory;

		$wp_widget_factory->widgets['unorthodox'] = new \stdClass();

		ob_start();
		_beans_force_the_widget( 'unorthodox', array(), array( 'before_widget' => '<div class="unorthodox"' ) );
		$output = ob_get_clean();

		$this->assertEmpty( $output );
	}

	public function test_should_do_nothing_when_widget_has_id_registered() {
		add_action( 'cg', [$this, 'check']);
		ob_start();
		_beans_force_the_widget( 'WP_Widget_Text', array(), array( 'before_widget' => '<div class="widget text-1"' ) );
		$output = ob_get_clean();

		$this->assertEmpty( $output );
	}


	public function test_should_output_widget_id_html_when_widget_registered_without_id_arg() {
		ob_start();
		_beans_force_the_widget( 'WP_Widget_Text', '', array() );
		$output = ob_get_clean();

		$this->assertEquals( '<!--widget-text-1-->', $output );
	}

	public function check( $data ) {
		var_dump( $data );
	}

}
