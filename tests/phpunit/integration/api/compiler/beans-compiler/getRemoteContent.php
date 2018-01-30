<?php
/**
 * Tests the get_remote_content method of _Beans_Compiler.
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Compiler;

use _Beans_Compiler;
use Beans\Framework\Tests\Integration\API\Compiler\Includes\Compiler_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-compiler-test-case.php';

/**
 * Class Tests_Beans_Compiler_Get_Remote_Content
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 * @group   integration-tests
 * @group   api
 */
class Tests_Beans_Compiler_Get_Remote_Content extends Compiler_Test_Case {

	/**
	 * Test get_remote_content() should return false when fragment is empty.
	 */
	public function test_should_return_false_when_fragment_is_empty() {
		$compiler = new _Beans_Compiler( array() );
		$this->assertfalse( $compiler->get_remote_content( '' ) );
	}

	/**
	 * Test get_remote_content() should return empty string when the remote site or file does not exist.
	 */
	public function test_should_return_empty_string_when_remote_does_not_exist() {
		$fragment = 'http://beans.local/invalid-file.js';
		$compiler = new _Beans_Compiler( array(
			'fragments' => array( $fragment ),
		) );
		$this->set_current_fragment( $compiler, $fragment );

		$this->assertSame( '', $compiler->get_remote_content( $fragment ) );
	}

	/**
	 * Test get_remote_content() should return the content when fragment is a relative url.
	 */
	public function test_should_return_content_when_relative_url() {
		$fragment = '//fonts.googleapis.com/css?family=Lato';
		$compiler = new _Beans_Compiler( array(
			'fragments' => array( $fragment ),
		) );
		$this->set_current_fragment( $compiler, $fragment );

		$content = $compiler->get_remote_content( $fragment );

		$this->assertContains( "font-family: 'Lato';", $content );
		$this->assertContains( 'font-style: normal;', $content );
		$this->assertContains( 'font-weight: 400;', $content );
		$this->assertContains( "src: local('Lato Regular')", $content );
	}

	/**
	 * Test get_remote_content() should return the content when fragment is http URL.
	 */
	public function test_should_return_content_when_http() {
		$fragment = 'http://fonts.googleapis.com/css?family=Roboto';
		$compiler = new _Beans_Compiler( array(
			'fragments' => array( $fragment ),
		) );
		$this->set_current_fragment( $compiler, $fragment );

		$content = $compiler->get_remote_content( $fragment );

		$this->assertContains( "font-family: 'Roboto';", $content );
		$this->assertContains( 'font-style: normal;', $content );
		$this->assertContains( 'font-weight: 400;', $content );
		$this->assertContains( "src: local('Roboto')", $content );
	}

	/**
	 * Test get_remote_content() should return the content when fragment is https URL.
	 */
	public function test_should_return_content_when_https() {
		$fragment = 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css';
		$compiler = new _Beans_Compiler( array(
			'fragments' => array( $fragment ),
		) );
		$this->set_current_fragment( $compiler, $fragment );

		$content = $compiler->get_remote_content( $fragment );

		$this->assertContains( 'Font Awesome 4.7.0 by @davegandy - http://fontawesome.io - @fontawesome', $content );
		$this->assertContains( "@font-face{font-family:'FontAwesome';", $content );
		$this->assertContains( "src:url('../fonts/fontawesome-webfont.eot?v=4.7.0');", $content );
	}
}
