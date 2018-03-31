<?php
/**
 * Test Case for Beans' WP Customize API integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\WPCustomize\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\WPCustomize\Includes;

use Beans\Framework\Tests\Integration\Test_Case;

/**
 * Abstract Class WP_Customize_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\WPCustomize\Includes
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

		require_once BEANS_THEME_DIR . '/lib/api/wp-customize/class-beans-wp-customize.php';
	}

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
		$reflective = $this->get_reflective_property( $property, '_Beans_Fields' );
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
		$field         = array_merge( array(
			'label'       => false,
			'description' => false,
			'default'     => false,
			'context'     => 'wp_customize',
			'attributes'  => array(
				'data-customize-setting-link' => $field['id'],
			),
			'db_group'    => false,
		), $field );
		$field['name'] = $field['id'];

		if ( 'group' === $field['type'] ) {

			foreach ( $field['fields'] as $index => $_field ) {
				$field['fields'][ $index ] = $this->merge_field_with_default( $_field, $set_value );
			}
		} elseif ( $set_value ) {
			$field['value'] = $field['default'];
		}

		return $field;
	}
}
