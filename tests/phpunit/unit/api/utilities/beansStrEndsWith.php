<?php
/**
 * Tests for beans_str_ends_with()
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Utilities;

use Beans\Framework\Tests\Unit\Test_Case;

/**
 * Class Tests_BeansEndsWith
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 * @group   api
 * @group   api-utilities
 */
class Tests_BeansEndsWith extends Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . 'api/utilities/functions.php';
	}

	/**
	 * Test beans_str_ends_with() should return false when case is wrong.
	 */
	public function test_should_return_false_when_case_is_wrong() {
		$this->assertFalse( beans_str_ends_with( 'Foo', 'O' ) );
		$this->assertFalse( beans_str_ends_with( 'Checking the Case?', 'case?' ) );
	}

	/**
	 * Test beans_str_ends_with() should return true when the case is correct.
	 */
	public function test_should_return_true_when_case_is_correct() {
		$this->assertTrue( beans_str_ends_with( 'Foo', 'o' ) );
		$this->assertTrue( beans_str_ends_with( 'Checking the Case?', 'Case?' ) );
	}

	/**
	 * Test beans_str_ends_with() should correctly identify ending string pattern.
	 */
	public function test_should_correctly_identify_ending_string_pattern() {
		$this->assertTrue( beans_str_ends_with( 'This is a string test.', '.' ) );
		$this->assertFalse( beans_str_ends_with( 'This is a string test.', 'test' ) );
		$this->assertTrue( beans_str_ends_with( 'This is a string test.', 'test.' ) );
		$this->assertFalse( beans_str_ends_with( 'Really dig the Beans framework! ', 'framework!' ) );
		$this->assertTrue( beans_str_ends_with( 'Really dig the Beans framework! ', 'framework! ' ) );
		$this->assertTrue( beans_str_ends_with( 'The WordPress Community Rocks!', 'Rocks!' ) );
		$this->assertFalse( beans_str_ends_with( 'The WordPress Community Rocks!', 'rocks!' ) );
		$this->assertTrue( beans_str_ends_with( 'This is a string test ', ' ' ) );
		$this->assertFalse( beans_str_ends_with( 'This is a string test', ' ' ) );
		$this->assertTrue( beans_str_ends_with( 'SomeClass::someMethod', 'd' ) );
		$this->assertFalse( beans_str_ends_with( 'Fulcrum\Extender\Str\StrChecker::endsWith', '/' ) );
	}

	/**
	 * Test beans_str_ends_with() should correctly identify when different data type is given.
	 */
	public function test_should_correctly_identify_when_different_data_type_given() {
		$this->assertTrue( beans_str_ends_with( 104, '04' ) );
		$this->assertFalse( beans_str_ends_with( 1247.86, '5' ) );
		$this->assertTrue( beans_str_ends_with( 1247.86, '.86' ) );

		foreach ( [ 85.3002, 97.002 ] as $number ) {
			$this->assertTrue( beans_str_ends_with( $number, '002' ) );
			$this->assertFalse( beans_str_ends_with( $number, '.0' ) );
		}

		$this->assertFalse( beans_str_ends_with( false, 'ls' ) );
		$this->assertTrue( beans_str_ends_with( true, '1' ) );
		$this->assertFalse( beans_str_ends_with( false, '0' ) );
		$this->assertTrue( beans_str_ends_with( false, '' ) );
	}

	/**
	 * Test beans_str_ends_with() should correctly identify when given an array of needles.
	 */
	public function test_should_correctly_identify_when_given_an_array_of_needles() {
		$this->assertTrue( beans_str_ends_with( 'This is a string test', [ 'Test', 'test' ] ) );
		$this->assertFalse( beans_str_ends_with( 'This is a string test', [ 'string', 'are' ] ) );
		$this->assertTrue( beans_str_ends_with( 'This is a string test', [ 'tests', 'test!', 'Test', 'test' ] ) );
		$this->assertFalse( beans_str_ends_with( 'This is a string test', [ 'tests', 'Tests', 'tests!', 'Tests' ] ) );
		$this->assertTrue( beans_str_ends_with( 'Hello from Tonya', [ 'From', 'Tonya' ] ) );
		$this->assertFalse( beans_str_ends_with( 'Hello from Tonya', [ 'hello', 'From' ] ) );
		$this->assertFalse( beans_str_ends_with(
			'The WordPress Community Rocks!',
			[ 'wordpress', 'wordPress', 'WordPress', 'WORDPRESS' ]
		) );
		$this->assertFalse( beans_str_ends_with(
			'The WordPress Community Rocks!',
			[ 'rocks!', 'community', 'WordPress', 'the' ]
		) );
		$this->assertTrue( beans_str_ends_with(
			'The WordPress Community Rocks!',
			[ 'community rocks!', 'unity Rocks!' ]
		) );
	}

	/**
	 * Test beans_str_ends_with() should correctly identify when non-Latin given.
	 */
	public function test_should_correctly_identify_when_non_latin_given() {
		$string = 'Τάχιστη αλώπηξ βαφής ψημένη γη, δρασκελίζει υπέρ νωθρού κυνός';

		$this->assertFalse( beans_str_ends_with( $string, ' ' ) );
		$this->assertFalse( beans_str_ends_with( $string, ' ' ) );
		$this->assertTrue( beans_str_ends_with( $string, 'ς' ) );
		$this->assertFalse( beans_str_ends_with( $string, 'Δρασκελίζει' ) );
		$this->assertTrue( beans_str_ends_with( $string, 'ρού κυνός' ) );
		$this->assertTrue( beans_str_ends_with( $string, 'δρασκελίζει υπέρ νωθρού κυνός' ) );
		$this->assertFalse( beans_str_ends_with( $string, [ 'tάχιστη', 'υπέρ' ] ) );
		$this->assertFalse( beans_str_ends_with( $string, [ 'Υπέρ', 'ψημένη' ] ) );
		$this->assertTrue( beans_str_ends_with( $string, [ 'tάχιστη', 'υπέρ', 'κυνός' ] ) );
		$this->assertTrue( beans_str_ends_with( $string, [ 'Υπέρ', 'ψημένη', 'ρού κυνός' ] ) );
	}
}
