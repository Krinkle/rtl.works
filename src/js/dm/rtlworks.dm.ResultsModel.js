/**
 * Model storing and analyzing the results
 *
 * @param {Object} results Results object
 */
rtlworks.dm.ResultsModel = function ( results ) {
	var num, explanations;

	this.tests = results.test_list;
	this.url = results.url;

	this.results = {};
	// Analysis
	if ( this.hasTest( 'dir_attr' ) ) {
		// A) <html> and <body>
		this.addTestResults(
			// Name
			'dir_attr_head',
			// Status type
			'warning',
			// Status
			Number( results.analysis.dir_attr.html ) || Number( results.analysis.dir_attr.body ),
			// Messages
			results.messages.dir_attr.head,
			// Results
			{
				html: results.analysis.dir_attr.html,
				body: results.analysis.dir_attr.body
			}
		);

		// B) Directionality in other tags
		countTotal = 0;
		explanations = [];
		Object.keys( results.analysis.dir_attr ).forEach( function ( key ) {
			var num;
			if ( key !== 'html' && key !== 'body' ) {
				num = Number( results.analysis.dir_attr[ key ] );
				countTotal += num;

				if ( num > 0 ) {
					explanations.push( num + ' ' + key + '\'s' );
				}
			}
		} );

		this.addTestResults(
			// Name
			'dir_attr_content',
			// Status type
			'warning',
			// Status
			countTotal > 0,
			// Messages
			results.messages.dir_attr.content,
			// Results
			explanations.join( ', ' )
		);
	}

	// CSS: Float
	if ( this.hasTest( 'css_float' ) ) {
		this.addTestResults(
			// Name
			'css_float',
			// Status type
			'warning',
			// Status
			( results.analysis.css_float === 0 ),
			// Messages
			results.messages.css_float,
			// Results
			results.analysis.css_float + ' elements'
		);
	}

	// CSS: Absolute positioning
	if ( this.hasTest( 'css_pos_absolute' ) ) {
		this.addTestResults(
			// Name
			'css_pos_absolute',
			// Status type
			'warning',
			// Status
			( results.analysis.css_pos_absolute === 0 ),
			// Messages
			results.messages.css_pos,
			// Results
			results.analysis.css_pos_absolute + ' rules'
		);
	}

	// CSS: Explicit positioning (left/right)
	if ( this.hasTest( 'css_pos' ) ) {
		this.addTestResults(
			// Name
			'css_pos',
			// Status type
			'warning',
			// Status
			( results.analysis.css_pos_left === 0 && results.analysis.css_pos_right === 0 ),
			// Messages
			results.messages.css_pos,
			// Results
			{
				left: results.analysis.css_pos_left,
				right: results.analysis.css_pos_right
			}
		);
	}
};

/**
 * Add a test result to the object
 *
 * @param {[type]} name Test name
 * @param {[type]} statusType Status type, defining whether the test
 *  failure is 'warning' or 'danger'.
 * @param {boolean} status Test status: Pass or fail
 * @param {[type]} messages intro and description messages
 * @param {[type]} results [description]
 */
rtlworks.dm.ResultsModel.prototype.addTestResults = function ( name, statusType, status, messages, results ) {
	this.results[ name ] = {
		status: !!status ? 'success' : statusType,
		messages: messages,
		results: results
	};
};

/**
 * Get test results
 *
 * @param {string} name Test name
 * @return {Object} Results object
 */
rtlworks.dm.ResultsModel.prototype.getTestResults = function ( name ) {
	return this.results[ name ];
};

rtlworks.dm.ResultsModel.prototype.getAllResults = function () {
	return this.results;
};

/**
 * Get the test list for this analysis
 *
 * @return {string[]} Test names
 */
rtlworks.dm.ResultsModel.prototype.getTests = function () {
	return this.tests;
};

rtlworks.dm.ResultsModel.prototype.getUrl = function () {
	return this.url;
};

/**
 * Check if a test exists in the list
 *
 * @param {string} testName Test name
 * @return {boolean} Test exists
 */
rtlworks.dm.ResultsModel.prototype.hasTest = function ( testName ) {
	return this.tests.indexOf( testName ) > -1;
};
