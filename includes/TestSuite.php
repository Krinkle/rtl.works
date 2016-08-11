<?php

namespace RTLWORKS;

/**
 * A test suite for RTL support in web pages.
 */
class TestSuite {
	private $contentParser;
	private $cssFiles;
	private $analysis = array();
	private $twig;
	private $testsDetails = array(
		'dir_attr' => array(
			'head' => array(
				'intro' => 'Direction tags in the &lt;html&gt; or &lt;body&gt;',
			),
			'content' => array(
				'intro' => 'Direction tags in content tags',
			),
		),
		'css_float' => array(
			'intro' => 'Elements with floating rules.'
		),
		'css_direction' => array(
			'intro' => 'Explicit direction statements for elements in CSS.'
		),
		'css_pos' => array(
			'intro' => 'Explicit positioning of elements (right or left)'
		),
		'css_pos_absolute' => array(
			'intro' => 'Absolutely positioned elements'
		),
	);

	function __construct( $twig, $url, $parsedContent, $cssFiles = array() ) {
		$this->twig = $twig;
		$this->contentParser = $parsedContent;
		$this->cssFiles = $cssFiles;
		$this->analysis = array(
			'url' => $url,
			'messages' => array(),
		);
	}

	/**
	 * Get the full analysis result object.
	 *
	 * @return array Analysis result
	 */
	public function getAnalysisResult() {
		return $this->analysis;
	}

	/**
	 * Run the requested tests.
	 *
	 * @param [string|array] $testTypes The tests to run.
	 *  If not given, all tests will run.
	 */
	public function runTests( $testTypes ) {
		if ( !is_array( $testTypes ) ) {
			$testTypes = array( $testTypes );
		}

		$errors = array();
		$this->tests = array();

		if ( empty( $testTypes[ 0 ] ) || $testTypes[ 0 ] === 'all' ) {
			$testTypes = array(
				'dir_attr',
				'css_float',
				'css_direction',
				'css_pos',
				'css_pos_absolute'
			);
		}

		foreach ( $testTypes as $type ) {
			switch ( $type ) {
			case 'dir_attr':
				$this->dirAttrTest();
				break;
			case 'css_float':
				$this->cssFloatTest();
				break;
			case 'css_direction':
				$this->cssDirectionTest();
				break;
			case 'css_pos':
				$this->cssPositioningTest();
				break;
			case 'css_pos_absolute':
				$this->cssAbsolutePositioning();
				break;
			default:
				$this->errors[] = 'Test type "' . $type . '" was not recognized.';
				break;
			}
			$tests[] = $type;

			// TODO: Make this an optional parameter
			$this->addTestMessage( $type );
		}

		// Log and output results
		$this->analysis[ 'date' ] = date( 'Y-m-d H:i:s' );
		$this->analysis[ 'test_list' ] = $tests;

		// Errors
		if ( count( $errors ) ) {
			$this->analysis[ 'errors' ] = $errors;
		}
	}

	protected function addTestMessage( $test ) {
		$this->analysis[ 'messages' ][ $test ] = $this->testsDetails[ $test ];
		// Add compiled description
		$this->analysis[ 'messages' ][ $test ][ 'description' ] = $this->twig->render( 'tests/' . $test .'.html' );
	}

	/**
	 * Test whether there are dir attributes set on
	 * the content related nodes in the document.
	 */
	protected function dirAttrTest() {
		$content_tags = array( 'html', 'body', 'div', 'span', 'p', 'ul' );

		// Look for dir tags in the content tags
		foreach ( $content_tags as $tag ) {
			$results = $this->contentParser->getAttributeForTags( $tag, 'dir' );

			$this->analysis[ 'analysis' ][ 'dir_attr' ][ $tag ] = count( $results );

			if ( count( $results ) > 0 ) {
				$this->analysis[ 'raw_results' ][ 'dir_attr' ][ $tag ] = implode( ',', $results );
			}
		}
	}

	/**
	 * Test whether there are float: rules in
	 * the CSS files
	 */
	protected function cssFloatTest() {
		return $this->cssTermExistenceTest( 'float', 'css_float', true );
	}

	/**
	 * Test whether there are direction: rules in
	 * the CSS files
	 */
	protected function cssDirectionTest() {
		return $this->cssTermExistenceTest( 'direction', 'css_direction', true );
	}

	/**
	 * Test whether there are literal positioning values
	 * within the css file.
	 */
	protected function cssPositioningTest() {
		$countRight = $this->cssTermExistenceTest( 'right', 'css_pos_right' );
		$countLeft = $this->cssTermExistenceTest( 'left', 'css_pos_left' );
	}

	/**
	 * Test whether there are absolute positioning values
	 * within the css file.
	 */
	protected function cssAbsolutePositioning() {
		$countRight = $this->cssTermExistenceTest( 'absolute', 'css_pos_absolute' );
	}

	/**
	 * Test whether a term exists in any of the CSS files
	 * and log the results.
	 *
	 * @param string $term Term to look for
	 * @param string [$testName] Test name. If not given, defaults to
	 *  the attribute name prefixed with 'css_'
	 * @param boolean $isAttribute Set the term as an attribute; adds
	 *  a colon after it, and logs the results. If not an attribute, the
	 *  results will log the entire line this was found in
	 */
	private function cssTermExistenceTest( $term, $testName = '', $isAttribute = false ) {
		// Look through all css files
		$count = 0;

		if ( empty( $testName ) ) {
			$testName = 'css_' . $term;
		}

		foreach ( $this->cssFiles as $file => $content ) {
			if ( $isAttribute ) {
				preg_match_all( '/' . $term . ': ?(\w+)/', $content, $results );
			} else {
				preg_match_all( '/(\w+):(\w+)? ?\b' . $term . '\b((\w+)? ?)/m', $content, $results );
			}

			$count += count( $results[ 1 ] );

			$this->analysis[ 'raw_results' ][ 'css' ][ $file ][ $testName ] = array(
				"count" => count( $results[ 1 ] ),
				"values" => implode( ',', $results[ 1 ] ),
			);
		}

		$this->analysis[ 'analysis' ][ $testName ] = $count;

		return $count;
	}
}
