<?php
/**
 * Tests for beans_get_widget_area_output()
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget;

use Beans\Framework\Tests\Integration\API\Widget\Includes\Beans_Widget_Test_Case;

require_once dirname( __FILE__ ) . '/includes/class-beans-widget-test-case.php';

/**
 * Class Tests_BeansGeteWidgetAreaOutput
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansGetWidgetAreaOutput extends Beans_Widget_Test_Case {

	/**
	 * Test beans_get_widget_area_output should return false when the widget area is not registered.
	 */
	public function test_should_return_false_when_widget_area_not_registered() {
		$this->assertFalse( beans_get_widget_area_output( 'unregistered-widget-area' ) );
	}

	/**
	 * Test beans_get_widget_area_output() should return output when a widget area is registered.
	 */
	public function test_should_return_widget_output_when_widget_area_is_registered() {
		$this->assertEquals( $this->get_expected_output(), beans_get_widget_area_output( 'sidebar_primary' ) );
	}

	/**
	 * Get the expected output (html) of the beans default primary sidebar.
	 */
	protected function get_expected_output() {
		return <<<OUTPUT
<div class="tm-widget uk-panel widget_search search-2"><div ><form class="uk-form uk-form-icon uk-form-icon-flip uk-width-1-1" method="get" action="http://example.org/" role="search"><input class="uk-width-1-1" type="search" placeholder="Search" value="" name="s"/><span class="uk-icon-search" aria-hidden="true"></span></form></div></div><div class="tm-widget uk-panel widget_recent-comments recent-comments-2"><h3 class="uk-panel-title">Recent Comments</h3><div class="uk-list"><ul id="recentcomments"></ul></div></div><div class="tm-widget uk-panel widget_archives archives-2"><h3 class="uk-panel-title">Archives</h3><div class="uk-list">		<ul>
				</ul>
		</div></div><div class="tm-widget uk-panel widget_categories categories-2"><h3 class="uk-panel-title">Categories</h3><div class="uk-list">		<ul>
<li class="cat-item-none">No categories</li>		</ul>
</div></div><div class="tm-widget uk-panel widget_meta meta-2"><h3 class="uk-panel-title">Meta</h3><div class="uk-list">			<ul>
						<li><a href="http://example.org/wp-login.php">Log in</a></li>
			<li><a href="http://example.org/?feed=rss2">Entries <abbr title="Really Simple Syndication">RSS</abbr></a></li>
			<li><a href="http://example.org/?feed=comments-rss2">Comments <abbr title="Really Simple Syndication">RSS</abbr></a></li>
			<li><a href="https://wordpress.org/" title="Powered by WordPress, state-of-the-art semantic personal publishing platform.">WordPress.org</a></li>			</ul>
			</div></div>
OUTPUT;
	}
}
