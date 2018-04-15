<?php
/**
 * Tests for add() method of _Beans_WP_Customize.
 *
 * @package Beans\Framework\Tests\Integration\API\WP-Customize
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\WPCustomize;

use Beans\Framework\Tests\Integration\API\WPCustomize\Includes\WP_Customize_Test_Case;
use _Beans_WP_Customize;
use WP_Customize_Manager;


require_once __DIR__ . '/includes/class-wp-customize-test-case.php';
require_once dirname( dirname( dirname( getcwd() ) ) ) . '/wp-includes/class-wp-customize-manager.php';

/**
 * Class Tests_Beans_Options_Actions
 *
 * @package Beans\Framework\Tests\Integration\API\Options
 * @group   api
 * @group   api-wp-customize
 */
class Tests_Beans_WP_Customize_Add extends WP_Customize_Test_Case {


}
