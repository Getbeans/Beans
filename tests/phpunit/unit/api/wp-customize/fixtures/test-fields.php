<?php
/**
 * Test data for the WP Customize unit tests.
 *
 * @package Beans\Framework\Tests\Integration\API\WP-Customize\Fixtures
 *
 * @since   1.5.0
 */

return array(
	// Single fields.
	'single_fields' => array(
		'fields'  => array(
			'name'  => 'beans-test',
			'type'  => 'radio',
			'label' => 'Layout',
			array(
				'id'      => 'beans_customizer_layout',
				'name'    => 'beans-test',
				'label'   => 'Layout',
				'type'    => 'radio',
				'default' => 'default_fallback',
				'options' => array(
					'default_fallback' => 'Use Default Layout',
					'c'                => BEANS_THEME_DIR . 'lib/admin/assets/images/layouts/c.png',
					'c_sp'             => BEANS_THEME_DIR . 'lib/admin/assets/images/layouts/c_sp.png',
					'sp_c'             => BEANS_THEME_DIR . 'lib/admin/assets/images/layouts/sp_c.png',
				),
			),
			array(
				'id'             => 'beans_customizer_checkbox',
				'name'           => 'beans-test',
				'label'          => false,
				'checkbox_label' => 'Enable the checkbox test',
				'type'           => 'checkbox',
				'default'        => false,
			),
			array(
				'id'      => 'beans_customizer_text',
				'name'    => 'beans-test',
				'type'    => 'text',
				'default' => 'Testing the text field.',
			),
		),
		'context' => 'beans-test',
		'section' => 'tm-beans-customizer',
		'args'    => array(
			'title'       => 'Beans Customizer Section',
			'priority'    => 250,
			'description' => 'Customizer Beans Section',
		),
	),

	// Group of fields.
	'group'         => array(
		'fields'  => array(
			'name'   => 'beans-test',
			'type'   => 'group',
			'fields' => array(
				array(
					'id'      => 'beans_compile_all_scripts',
					'name'    => 'beans-test',
					'type'    => 'activation',
					'default' => false,
				),
				array(
					'id'         => 'beans_compile_all_scripts_mode',
					'name'       => 'beans-test',
					'type'       => 'select',
					'default'    => 'aggressive',
					'attributes' => array( 'style' => 'margin: -3px 0 0 -8px;' ),
					'options'    => array(
						'aggressive' => 'Aggressive',
						'standard'   => 'Standard',
					),
				),
				array(
					'id'             => 'beans_checkbox_test',
					'name'           => 'beans-test',
					'label'          => false,
					'checkbox_label' => 'Enable the checkbox test',
					'type'           => 'checkbox',
					'default'        => false,
				),
			),
		),
		'context' => 'group_tests',
		'section' => 'tm-beans-customizer',
		'name'    => 'beans-test',
		'args'    => array(
			'title'       => 'Beans Customizer Section',
			'priority'    => 250,
			'description' => 'Customizer Beans Section',
		),
	),
);
