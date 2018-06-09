<?php
/**
 * Tests for beans_build_skip_links().
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\HTML;

use Beans\Framework\Tests\Unit\API\HTML\Includes\HTML_Test_Case;
use Brain\Monkey;

require_once __DIR__ . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansBuildSkipLinks
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansBuildSkipLinks extends HTML_Test_Case {

	/**
	 * Test beans_build_skip_links() should call beans_is_active_widget_area() once when the layout includes a primary-sidebar.
	 */
	public function test_should_call_beans_is_active_widget_area_once_when_primary_sidebar_layout() {
		Monkey\Functions\expect( 'beans_get_layout' )->once()->andReturn( 'c_sp' );
		Monkey\Functions\expect( 'has_nav_menu' )
			->once()
			->with( 'primary' )
			->andReturn( 'true' );
		Monkey\Functions\expect( 'beans_is_active_widget_area' )
			->once()
			->ordered()
			->with( 'sidebar_primary' )
			->andReturn( 'true' )
			->andAlsoExpectIt()
			->never()
			->ordered()
			->with( 'sidebar_secondary' )
			->andReturn( 'true' );
		Monkey\Functions\expect( 'beans_output_skip_links' )
		->once()
		->andReturn();

		$this->assertNull( beans_build_skip_links() );
	}

	/**
	 * Test beans_build_skip_links() should not call beans_is_active_widget_area() when the layout is full-width.
	 */
	public function test_should_not_call_beans_is_active_widget_area_when_fullwith_layout() {
		Monkey\Functions\expect( 'beans_get_layout' )->once()->andReturn( 'c' );
		Monkey\Functions\expect( 'has_nav_menu' )
			->once()
			->with( 'primary' )
			->andReturn( 'true' );
		Monkey\Functions\expect( 'beans_is_active_widget_area' )
			->never()
			->ordered()
			->with( 'sidebar_primary' )
			->andReturn( 'true' )
			->andAlsoExpectIt()
			->never()
			->ordered()
			->with( 'sidebar_secondary' )
			->andReturn( 'true' );
		Monkey\Functions\expect( 'beans_output_skip_links' )
		->once()
		->andReturn();

		$this->assertNull( beans_build_skip_links() );
	}

	/**
	 * Test beans_build_skip_links() should call beans_is_active_widget_area() twice when the layout includes a secondary sidebar.
	 */
	public function test_should_call_beans_is_active_widget_area_twice_when_secondary_sidebar_layout() {
		Monkey\Functions\expect( 'beans_get_layout' )->once()->andReturn( 'sp_c_ss' );
		Monkey\Functions\expect( 'has_nav_menu' )
			->once()
			->with( 'primary' )
			->andReturn( 'true' );
		Monkey\Functions\expect( 'beans_is_active_widget_area' )
			->once()
			->ordered()
			->with( 'sidebar_primary' )
			->andReturn( 'true' )
			->andAlsoExpectIt()
			->once()
			->ordered()
			->with( 'sidebar_secondary' )
			->andReturn( 'true' );
		Monkey\Functions\expect( 'beans_output_skip_links' )
		->once()
		->andReturn();

		$this->assertNull( beans_build_skip_links() );
	}
}
