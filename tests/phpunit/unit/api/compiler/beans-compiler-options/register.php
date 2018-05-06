<?php
/**
 * Tests the register() method of _Beans_Compiler_Options.
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler;

use _Beans_Compiler_Options;
use Beans\Framework\Tests\Unit\API\Compiler\Includes\Compiler_Options_Test_Case;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-compiler-options-test-case.php';

/**
 * Class Tests_BeansCompilerOptions_Register
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompilerOptions_Register extends Compiler_Options_Test_Case {

	/**
	 * Test _Beans_Compiler_Options::register() should register only flush button when the styles and scripts are not
	 * supported.
	 */
	public function test_should_only_register_flush_button_when_styles_scripts_not_supported() {
		$fields = [
			[
				'id'          => 'beans_compiler_items',
				'type'        => 'flush_cache',
				'description' => 'Clear CSS and Javascript cached files. New cached versions will be compiled on page load.',
			],
		];

		Monkey\Functions\expect( 'beans_get_component_support' )
			->once()
			->with( 'wp_styles_compiler' )
			->andReturn( false )
			->andAlsoExpectIt()
			->once()
			->with( 'wp_scripts_compiler' )
			->andReturn( false );
		Monkey\Functions\expect( 'beans_register_options' )
			->once()
			->with(
				$fields,
				'beans_settings',
				'compiler_options',
				[
					'title'   => 'Compiler options',
					'context' => 'normal',
				]
			)
			->andReturn( true );

		$this->assertTrue( ( new _Beans_Compiler_Options() )->register() );
	}

	/**
	 * Test _Beans_Compiler_Options::register() should not register the styles options when not supported.
	 */
	public function test_should_not_register_styles_options_when_not_supported() {
		$fields = [
			[
				'id'          => 'beans_compiler_items',
				'type'        => 'flush_cache',
				'description' => 'Clear CSS and Javascript cached files. New cached versions will be compiled on page load.',
			],
			[
				'id'          => 'beans_compile_all_scripts_group',
				'label'       => 'Compile all WordPress scripts',
				'type'        => 'group',
				'fields'      => [
					[
						'id'      => 'beans_compile_all_scripts',
						'type'    => 'activation',
						'label'   => 'Select to compile scripts.',
						'default' => false,
					],
					[
						'id'      => 'beans_compile_all_scripts_mode',
						'type'    => 'select',
						'label'   => 'Choose the level of compilation.',
						'default' => 'aggressive',
						'options' => [
							'aggressive' => 'Aggressive',
							'standard'   => 'Standard',
						],
					],
				],
				'description' => 'Compile and cache all the JavaScript files that have been enqueued to the WordPress head. <br/> JavaScript is outputted in the footer if the level is set to <strong>Aggressive</strong> and might conflict with some third-party plugins which are not following WordPress standards.',
			],
		];

		Monkey\Functions\expect( 'beans_get_component_support' )
			->once()
			->with( 'wp_styles_compiler' )
			->andReturn( false )
			->andAlsoExpectIt()
			->once()
			->with( 'wp_scripts_compiler' )
			->andReturn( true );
		Monkey\Functions\expect( 'beans_register_options' )
			->once()
			->with(
				$fields,
				'beans_settings',
				'compiler_options',
				[
					'title'   => 'Compiler options',
					'context' => 'normal',
				]
			)
			->andReturn( true );

		$this->assertTrue( ( new _Beans_Compiler_Options() )->register() );
	}

	/**
	 * Test _Beans_Compiler_Options::register() should not register the scripts options when not supported.
	 */
	public function test_should_not_register_scripts_options_when_not_supported() {
		$fields = [
			[
				'id'          => 'beans_compiler_items',
				'type'        => 'flush_cache',
				'description' => 'Clear CSS and Javascript cached files. New cached versions will be compiled on page load.',
			],
			[
				'id'             => 'beans_compile_all_styles',
				'label'          => 'Compile all WordPress styles',
				'checkbox_label' => 'Select to compile styles.',
				'type'           => 'checkbox',
				'default'        => false,
				'description'    => 'Compile and cache all the CSS files that have been enqueued to the WordPress head.',
			],
		];

		Monkey\Functions\expect( 'beans_get_component_support' )
			->once()
			->with( 'wp_styles_compiler' )
			->andReturn( true )
			->andAlsoExpectIt()
			->once()
			->with( 'wp_scripts_compiler' )
			->andReturn( false );
		Monkey\Functions\expect( 'beans_register_options' )
			->once()
			->with(
				$fields,
				'beans_settings',
				'compiler_options',
				[
					'title'   => 'Compiler options',
					'context' => 'normal',
				]
			)
			->andReturn( true );

		$this->assertTrue( ( new _Beans_Compiler_Options() )->register() );
	}

	/**
	 * Test _Beans_Compiler_Options::register() should register all options when styles and scripts are supported.
	 */
	public function test_should_register_all_options_when_styles_scripts_supported() {
		$fields = [
			[
				'id'          => 'beans_compiler_items',
				'type'        => 'flush_cache',
				'description' => 'Clear CSS and Javascript cached files. New cached versions will be compiled on page load.',
			],
			[
				'id'             => 'beans_compile_all_styles',
				'label'          => 'Compile all WordPress styles',
				'checkbox_label' => 'Select to compile styles.',
				'type'           => 'checkbox',
				'default'        => false,
				'description'    => 'Compile and cache all the CSS files that have been enqueued to the WordPress head.',
			],
			[
				'id'          => 'beans_compile_all_scripts_group',
				'label'       => 'Compile all WordPress scripts',
				'type'        => 'group',
				'fields'      => [
					[
						'id'      => 'beans_compile_all_scripts',
						'type'    => 'activation',
						'label'   => 'Select to compile scripts.',
						'default' => false,
					],
					[
						'id'      => 'beans_compile_all_scripts_mode',
						'type'    => 'select',
						'label'   => 'Choose the level of compilation.',
						'default' => 'aggressive',
						'options' => [
							'aggressive' => 'Aggressive',
							'standard'   => 'Standard',
						],
					],
				],
				'description' => 'Compile and cache all the JavaScript files that have been enqueued to the WordPress head. <br/> JavaScript is outputted in the footer if the level is set to <strong>Aggressive</strong> and might conflict with some third-party plugins which are not following WordPress standards.',
			],
		];

		Monkey\Functions\expect( 'beans_get_component_support' )
			->once()
			->with( 'wp_styles_compiler' )
			->andReturn( true )
			->andAlsoExpectIt()
			->once()
			->with( 'wp_scripts_compiler' )
			->andReturn( true );
		Monkey\Functions\expect( 'beans_register_options' )
			->once()
			->with(
				$fields,
				'beans_settings',
				'compiler_options',
				[
					'title'   => 'Compiler options',
					'context' => 'normal',
				]
			)
			->andReturn( true );

		$this->assertTrue( ( new _Beans_Compiler_Options() )->register() );
	}
}
