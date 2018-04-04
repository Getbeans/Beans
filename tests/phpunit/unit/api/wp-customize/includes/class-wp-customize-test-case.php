<?php
/**
 * Test Case for Beans' WP Customize API unit tests.
 *
 * @package Beans\Framework\Tests\Unit\API\WPCustomize\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\WPCustomize\Includes;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey;

/**
 * Abstract Class WP_Customize_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\WPCustomize\Includes
 */
abstract class WP_Customize_Test_Case extends Test_Case {

	/**
	 * An array of test data.
	 *
	 * @var array
	 */
	protected static $test_data;

	/**
	 * Setup the test before we run the test setups.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		static::$test_data = require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fixtures/test-fields.php';
	}

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		$this->load_original_functions( array(
			'api/wp-customize/functions.php',
			'api/wp-customize/class-beans-wp-customize.php',
			'api/wp-customize/class-beans-wp-customize-control.php',
			'api/fields/class-beans-fields.php',
		) );

		$this->setup_common_wp_stubs();
	}

	// phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod.Found -- It's actually needed.
	/**
	 * Get reflective access to the private method.
	 *
	 * @since 1.5.0
	 *
	 * @param string $method_name Method name for which to gain access.
	 * @param string $class_name  Optional. Name of the target class.
	 *
	 * @return \ReflectionMethod
	 * @throws \ReflectionException Throws an exception if method does not exist.
	 */
	protected function get_reflective_method( $method_name, $class_name = '_Beans_Fields' ) {
		return parent::get_reflective_method( $method_name, $class_name );
	}

	/**
	 * Get reflective access to the private property.
	 *
	 * @since 1.5.0
	 *
	 * @param string $property   Property name for which to gain access.
	 * @param string $class_name Optional. Name of the target class.
	 *
	 * @return \ReflectionProperty|string
	 * @throws \ReflectionException Throws an exception if property does not exist.
	 */
	protected function get_reflective_property( $property, $class_name = '_Beans_Fields' ) {
		return parent::get_reflective_property( $property, $class_name );
	}
	// phpcs:enable Generic.CodeAnalysis.UselessOverridingMethod.Found

	/**
	 * Get the value of the private or protected property.
	 *
	 * @since 1.5.0
	 *
	 * @param string $property Property name for which to gain access.
	 *
	 * @return mixed
	 * @throws \ReflectionException Throws an exception if property does not exist.
	 */
	protected function get_reflective_property_value( $property ) {
		$reflective = $this->get_reflective_property( $property );

		return $reflective->getValue( new \_Beans_Fields() );
	}

	/**
	 * Merge the given field with the default structure.
	 *
	 * @since 1.5.0
	 *
	 * @param array $field     The given field to merge.
	 * @param bool  $set_value Optional. When true, sets the "value" to the "default".
	 *
	 * @return array
	 */
	protected function merge_field_with_default( array $field, $set_value = true ) {
		$merged_field         = array_merge( array(
			'label'       => false,
			'description' => false,
			'default'     => false,
			'context'     => 'wp_customize',
			'attributes'  => array(
				'data-customize-setting-link' => $field['id'],
			),
			'db_group'    => false,
		), $field );
		$merged_field['name'] = $field['id'];

		if ( $set_value ) {
			$merged_field['value'] = $field['default'];
		}

		return $merged_field;
	}
}
