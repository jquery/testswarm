/**
 * JavaScript file to included by the test suite page that it loaded
 * inside the iframe on the "run" pages. This injection must be done
 * by the guest page, it can't be loaded by TestSwarm.
 * Example:
 * - https://github.com/jquery/jquery/blob/master/test/data/testrunner.js
 * - https://github.com/jquery/jquery/blob/master/test/index.html
 *
 * @author John Resig, 2008-2011
 * @author Timo Tijhof, 2012
 * @since 0.1.0
 * @package TestSwarm
 */
/*global jQuery, $, QUnit, Test, JSSpec, JsUnitTestManager, SeleniumTestResult, LOG, doh, Screw*/
/*jshint forin:false, strict:false, loopfunc:true, browser:true, jquery:true*/
(function (undefined) {
	var	DEBUG, doPost, search, url, index, submitTimeout, curHeartbeat,
		beatRate, testFrameworks, onErrorFnPrev;

	DEBUG = false;

	doPost = false;
	search = window.location.search;
	index = search.indexOf( 'swarmURL=' );
	submitTimeout = 5;
	beatRate = 20;

	try {
		doPost = !!window.parent.postMessage;
	} catch ( e ) {}

	if ( index !== -1 ) {
		url = decodeURIComponent( search.slice( index + 9 ) );
	}

	if ( !DEBUG && ( !url || url.indexOf( 'http' ) !== 0 ) ) {
		return;
	}

	// Prevent blocking things from executing
	if ( !DEBUG ) {
		window.print = window.confirm = window.alert = window.open = function () {};
	}

	/** Utility functions **/

	function debugObj( obj ) {
		var i, str = '';
		for ( i in obj ) {
			str += ( str ? '\n' : '' ) + i + ':\n\t ' + obj[i];
		}
		return str;
	}

	function remove( elem ) {
		if ( typeof elem === 'string' ) {
			elem = document.getElementById( elem );
		}

		if ( elem ) {
			elem.parentNode.removeChild( elem );
		}
	}

	function trimSerialize( doc ) {
		var scripts, root, cur, links, i, href;
		doc = doc || document;

		scripts = doc.getElementsByTagName( 'script' );
		while ( scripts.length ) {
			remove( scripts[0] );
		}

		root = window.location.href.replace( /(https?:\/\/.*?)\/.*/, '$1' );
		cur = window.location.href.replace( /[^\/]*$/, '' );

		links = doc.getElementsByTagName( 'link' );
		for ( i = 0; i < links.length; i += 1 ) {
			href = links[i].href;
			if ( href.indexOf( '/' ) === 0 ) {
				href = root + href;
			} else if ( !/^https?:\/\//.test( href ) ) {
				href = cur + href;
			}
			links[i].href = href;
		}

		return ( '<html>' + doc.documentElement.innerHTML + '</html>' )
			.replace( /\s+/g, ' ' );
	}

	function submit( params ) {
		var form, i, input, key, paramItems, parts, query;

		if ( curHeartbeat ) {
			clearTimeout( curHeartbeat );
		}

		paramItems = (url.split( '?' )[1] || '' ).split( '&' );

		for ( i = 0; i < paramItems.length; i += 1 ) {
			if ( paramItems[i] ) {
				parts = paramItems[i].split( '=' );
				if ( !params[ parts[0] ] ) {
					params[ parts[0] ] = parts[1];
				}
			}
		}

		if ( !params.action ) {
			params.action = 'saverun';
		}

		if ( !params.report_html ) {
			params.report_html = window.TestSwarm.serialize();
		}

		if ( DEBUG ) {
			alert( debugObj( params ) ) ;
		}

		if ( doPost ) {
			// Build Query String
			query = '';

			for ( key in params ) {
				query += ( query ? '&' : '' ) + key + '=' + encodeURIComponent( params[key] );
			}

			if ( !DEBUG ) {
				window.parent.postMessage( query, '*' );
			}

		} else {
			form = document.createElement( 'form' );
			form.action = url;
			form.method = 'POST';

			for ( i in params ) {
				input = document.createElement( 'input' );
				input.type = 'hidden';
				input.name = i;
				input.value = params[i];
				form.appendChild( input );
			}

			if ( DEBUG ) {
				alert( url );

			} else {
				// Watch for the result submission timing out
				setTimeout(function () {
					submit( params );
				}, submitTimeout * 1000);

				document.body.appendChild( form );
				form.submit();
			}
		}
	}

	function detectAndInstall() {
		var key;
		for ( key in testFrameworks ) {
			if ( testFrameworks[key].detect() ) {
				testFrameworks[key].install();
				return key;
			}
		}
		return false;
	}

	// Preserve other handlers
	onErrorFnPrev = window.onerror;

	// Cover uncaught exceptions
	// Returning true will surpress the default browser handler,
	// returning false will let it run.
	window.onerror = function ( error, filePath, linerNr ) {
		var ret = false;
		if ( onErrorFnPrev ) {
			ret = onErrorFnPrev( error, filePath, linerNr );
		}

		// Treat return value as window.onerror itself does,
		// Only do our handling if not surpressed.
		if ( ret !== true ) {
			document.body.appendChild( document.createTextNode( '[TestSwarm] window.onerror: ' + error ) );
			submit({ fail: 0, error: 1, total: 1 });

			return false;
		}

		return ret;
	};

	// Expose the TestSwarm API
	window.TestSwarm = {
		submit: submit,
		heartbeat: function () {
			if ( curHeartbeat ) {
				clearTimeout( curHeartbeat );
			}

			curHeartbeat = setTimeout(function () {
				submit({ fail: -1, total: -1 });
			}, beatRate * 1000);
		},
		serialize: function () {
			return trimSerialize();
		}
	};

	testFrameworks = {
		// QUnit (by jQuery)
		// http://docs.jquery.com/QUnit
		'QUnit': {
			detect: function () {
				return typeof QUnit !== 'undefined';
			},
			install: function () {
				var moduleCount = 0, testCount = 0, testTime, moduleTime, results = [];
				
				QUnit.moduleStart = function(module) {
					moduleTime = +(new Date());
					
					results.push({
						name: module.name,
						tests: []	
					});
				};
				
				QUnit.testStart = function(test) {
					testTime = +(new Date());
					
					results[moduleCount].tests.push({
						name: test.name,
						assertions: []
					});
				};
				
				QUnit.log = function(test) {
					results[moduleCount].tests[testCount].assertions.push({
						actual: test.actual,
						expected: test.expected,
						message: test.message,
						result: test.result ? 1 : 0, // number is more convenient to handle
						source: test.source || ''
					});
				
					window.TestSwarm.heartbeat();
				};
				
				QUnit.testDone = function(test) {
					var alias = results[moduleCount].tests[testCount];
					alias['duration'] = +(new Date()) - testTime;
					alias['failed'] = test.failed;
					alias['passed'] = test.passed;
					alias['total'] = test.total;
					
					testCount++;
				};
				
				QUnit.moduleDone = function(module) {
					var alias = results[moduleCount];
					alias['duration'] = +(new Date()) - moduleTime;
					alias['failed'] = module.failed;
					alias['passed'] = module.passed;
					alias['total'] = module.total;
					
					moduleCount++;
					testCount = 0;
				};
				
				QUnit.done = function (suite) {
					var report = {
						suite: document.title || location.href.replace(/^.+?\/([^\?\/]+)\/?\??[^\?]*$/, '$1'),
						failed: suite.failed,
						passed: suite.passed,
						total: suite.total,
						duration: suite.runtime,
						modules: results
					};
				
					submit({
						fail: suite.failed,
						error: 0,
						total: suite.total,
						report_json: JSON.stringify(report)
					});
				};
				
				window.TestSwarm.heartbeat();

				window.TestSwarm.serialize = function () {
					var ol, i;

					// Clean up the HTML (remove any un-needed test markup)
					remove( 'nothiddendiv' );
					remove( 'loadediframe' );
					remove( 'dl' );
					remove( 'main' );

					// Show any collapsed results
					ol = document.getElementsByTagName( 'ol' );
					for ( i = 0; i < ol.length; i += 1 ) {
						ol[i].style.display = 'block';
					}

					return trimSerialize();
				};
			}
		},

		// UnitTestJS (Prototype, Scriptaculous)
		// https://github.com/tobie/unittest_js
		'UnitTestJS': {
			detect: function () {
				return typeof Test !== 'undefined' && Test && Test.Unit && Test.Unit.runners;
			},
			install: function () {
				var	total_runners = Test.Unit.runners.length,
					cur_runners = 0,
					total = 0,
					fail = 0,
					error = 0,
					i;

				for ( i = 0; i < Test.Unit.runners.length; i += 1 ) {
					// Need to proxy the i variable into a local scope,
					// otherwise all the finish-functions created in this loop
					// will refer to the same i variable..
					(function ( i ) {
						var finish, results;

						finish = Test.Unit.runners[i].finish;
						Test.Unit.runners[i].finish = function () {
							finish.call( this );

							results = this.getResult();
							total += results.assertions;
							fail += results.failures;
							error += results.errors;

							cur_runners += 1;
							if ( cur_runners === total_runners ) {
								submit({
									fail: fail,
									error: error,
									total: total
								});
							}
						};
					}( i ) );
				}
			}
		},

		// JSSpec (MooTools)
		// http://jania.pe.kr/aw/moin.cgi/JSSpec
		// https://code.google.com/p/jsspec/
		'JSSpec': {
			detect: function () {
				return typeof JSSpec !== 'undefined' && JSSpec && JSSpec.Logger;
			},
			install: function () {
				var onRunnerEnd = JSSpec.Logger.prototype.onRunnerEnd;
				JSSpec.Logger.prototype.onRunnerEnd = function () {
					var ul, i;
					onRunnerEnd.call( this );

					// Show any collapsed results
					ul = document.getElementsByTagName( 'ul' );
					for ( i = 0; i < ul.length; i += 1 ) {
						ul[i].style.display = 'block';
					}

					submit({
						fail: JSSpec.runner.getTotalFailures(),
						error: JSSpec.runner.getTotalErrors(),
						total: JSSpec.runner.totalExamples
					});
				};

				window.TestSwarm.serialize = function () {
					var ul, i;
					// Show any collapsed results
					ul = document.getElementsByTagName( 'ul' );
					for ( i = 0; i < ul.length; i += 1 ) {
						ul[i].style.display = 'block';
					}

					return trimSerialize();
				};
			}
		},

		// JSUnit
		// http://www.jsunit.net/
		// Note: Injection file must be included before the frames
		// are document.write()d into the page.
		'JSUnit': {
			detect: function () {
				return typeof JsUnitTestManager !== 'undefined';
			},
			install: function () {
				var _done = JsUnitTestManager.prototype._done;
				JsUnitTestManager.prototype._done = function () {
					_done.call( this );

					submit({
						fail: this.failureCount,
						error: this.errorCount,
						total: this.totalCount
					});
				};

				window.TestSwarm.serialize = function () {
					return '<pre>' + this.log.join( '\n' ) + '</pre>';
				};
			}
		},

		// Selenium Core
		// http://seleniumhq.org/projects/core/
		'Selenium': {
			detect: function () {
				return typeof SeleniumTestResult !== 'undefined' && typeof LOG !== 'undefined';
			},
			install: function () {
				// Completely overwrite the postback
				SeleniumTestResult.prototype.post = function () {
					submit({
						fail: this.metrics.numCommandFailures,
						error: this.metrics.numCommandErrors,
						total: this.metrics.numCommandPasses + this.metrics.numCommandFailures + this.metrics.numCommandErrors
					});
				};

				window.TestSwarm.serialize = function () {
					var results = [], msg;
					while ( LOG.pendingMessages.length ) {
						msg = LOG.pendingMessages.shift();
						results.push( msg.type + ': ' + msg.msg );
					}

					return '<pre>' + results.join( '\n' ) + '</pre>';
				};
			}
		},

		// Dojo Objective Harness
		// http://docs.dojocampus.org/quickstart/doh
		'DOH': {
			detect: function () {
				return typeof doh !== 'undefined' && doh._report;
			},
			install: function () {
				var _report = doh._report;
				doh._report = function () {
					_report.apply( this, arguments );

					submit({
						fail: doh._failureCount,
						error: doh._errorCount,
						total: doh._testCount
					});
				};

				window.TestSwarm.serialize = function () {
					return '<pre>' + document.getElementById( 'logBody' ).innerHTML + '</pre>';
				};
			}
		},

		// Screw.Unit
		// https://github.com/nathansobo/screw-unit
		'Screw.Unit': {
			detect: function () {
				return typeof Screw !== 'undefined' && typeof jQuery !== 'undefined' && Screw && Screw.Unit;
			},
			install: function () {
				$(Screw).bind( 'after', function () {
					var	passed = $( '.passed' ).length,
						failed = $( '.failed' ).length;
					submit({
						fail: failed,
						error: 0,
						total: failed + passed
					});
				});

				$( Screw ).bind( 'loaded', function () {
					$( '.it' )
						.bind( 'passed', window.TestSwarm.heartbeat )
						.bind( 'failed', window.TestSwarm.heartbeat );
					window.TestSwarm.heartbeat();
				});

				window.TestSwarm.serialize = function () {
					return trimSerialize();
				};
			}
		}
	};

	detectAndInstall();

}() );


/*
    json2.js
    2011-10-19

    Public Domain.

    NO WARRANTY EXPRESSED OR IMPLIED. USE AT YOUR OWN RISK.

    See http://www.JSON.org/js.html


    This code should be minified before deployment.
    See http://javascript.crockford.com/jsmin.html

    USE YOUR OWN COPY. IT IS EXTREMELY UNWISE TO LOAD CODE FROM SERVERS YOU DO
    NOT CONTROL.


    This file creates a global JSON object containing two methods: stringify
    and parse.

        JSON.stringify(value, replacer, space)
            value       any JavaScript value, usually an object or array.

            replacer    an optional parameter that determines how object
                        values are stringified for objects. It can be a
                        function or an array of strings.

            space       an optional parameter that specifies the indentation
                        of nested structures. If it is omitted, the text will
                        be packed without extra whitespace. If it is a number,
                        it will specify the number of spaces to indent at each
                        level. If it is a string (such as '\t' or '&nbsp;'),
                        it contains the characters used to indent at each level.

            This method produces a JSON text from a JavaScript value.

            When an object value is found, if the object contains a toJSON
            method, its toJSON method will be called and the result will be
            stringified. A toJSON method does not serialize: it returns the
            value represented by the name/value pair that should be serialized,
            or undefined if nothing should be serialized. The toJSON method
            will be passed the key associated with the value, and this will be
            bound to the value

            For example, this would serialize Dates as ISO strings.

                Date.prototype.toJSON = function (key) {
                    function f(n) {
                        // Format integers to have at least two digits.
                        return n < 10 ? '0' + n : n;
                    }

                    return this.getUTCFullYear()   + '-' +
                         f(this.getUTCMonth() + 1) + '-' +
                         f(this.getUTCDate())      + 'T' +
                         f(this.getUTCHours())     + ':' +
                         f(this.getUTCMinutes())   + ':' +
                         f(this.getUTCSeconds())   + 'Z';
                };

            You can provide an optional replacer method. It will be passed the
            key and value of each member, with this bound to the containing
            object. The value that is returned from your method will be
            serialized. If your method returns undefined, then the member will
            be excluded from the serialization.

            If the replacer parameter is an array of strings, then it will be
            used to select the members to be serialized. It filters the results
            such that only members with keys listed in the replacer array are
            stringified.

            Values that do not have JSON representations, such as undefined or
            functions, will not be serialized. Such values in objects will be
            dropped; in arrays they will be replaced with null. You can use
            a replacer function to replace those with JSON values.
            JSON.stringify(undefined) returns undefined.

            The optional space parameter produces a stringification of the
            value that is filled with line breaks and indentation to make it
            easier to read.

            If the space parameter is a non-empty string, then that string will
            be used for indentation. If the space parameter is a number, then
            the indentation will be that many spaces.

            Example:

            text = JSON.stringify(['e', {pluribus: 'unum'}]);
            // text is '["e",{"pluribus":"unum"}]'


            text = JSON.stringify(['e', {pluribus: 'unum'}], null, '\t');
            // text is '[\n\t"e",\n\t{\n\t\t"pluribus": "unum"\n\t}\n]'

            text = JSON.stringify([new Date()], function (key, value) {
                return this[key] instanceof Date ?
                    'Date(' + this[key] + ')' : value;
            });
            // text is '["Date(---current time---)"]'


        JSON.parse(text, reviver)
            This method parses a JSON text to produce an object or array.
            It can throw a SyntaxError exception.

            The optional reviver parameter is a function that can filter and
            transform the results. It receives each of the keys and values,
            and its return value is used instead of the original value.
            If it returns what it received, then the structure is not modified.
            If it returns undefined then the member is deleted.

            Example:

            // Parse the text. Values that look like ISO date strings will
            // be converted to Date objects.

            myData = JSON.parse(text, function (key, value) {
                var a;
                if (typeof value === 'string') {
                    a =
/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2}(?:\.\d*)?)Z$/.exec(value);
                    if (a) {
                        return new Date(Date.UTC(+a[1], +a[2] - 1, +a[3], +a[4],
                            +a[5], +a[6]));
                    }
                }
                return value;
            });

            myData = JSON.parse('["Date(09/09/2001)"]', function (key, value) {
                var d;
                if (typeof value === 'string' &&
                        value.slice(0, 5) === 'Date(' &&
                        value.slice(-1) === ')') {
                    d = new Date(value.slice(5, -1));
                    if (d) {
                        return d;
                    }
                }
                return value;
            });


    This is a reference implementation. You are free to copy, modify, or
    redistribute.
*/

/*jslint evil: true, regexp: true */

/*members "", "\b", "\t", "\n", "\f", "\r", "\"", JSON, "\\", apply,
    call, charCodeAt, getUTCDate, getUTCFullYear, getUTCHours,
    getUTCMinutes, getUTCMonth, getUTCSeconds, hasOwnProperty, join,
    lastIndex, length, parse, prototype, push, replace, slice, stringify,
    test, toJSON, toString, valueOf
*/


// Create a JSON object only if one does not already exist. We create the
// methods in a closure to avoid creating global variables.

var JSON;
if (!JSON) {
    JSON = {};
}

(function () {
    'use strict';

    function f(n) {
        // Format integers to have at least two digits.
        return n < 10 ? '0' + n : n;
    }

    if (typeof Date.prototype.toJSON !== 'function') {

        Date.prototype.toJSON = function (key) {

            return isFinite(this.valueOf())
                ? this.getUTCFullYear()     + '-' +
                    f(this.getUTCMonth() + 1) + '-' +
                    f(this.getUTCDate())      + 'T' +
                    f(this.getUTCHours())     + ':' +
                    f(this.getUTCMinutes())   + ':' +
                    f(this.getUTCSeconds())   + 'Z'
                : null;
        };

        String.prototype.toJSON      =
            Number.prototype.toJSON  =
            Boolean.prototype.toJSON = function (key) {
                return this.valueOf();
            };
    }

    var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        gap,
        indent,
        meta = {    // table of character substitutions
            '\b': '\\b',
            '\t': '\\t',
            '\n': '\\n',
            '\f': '\\f',
            '\r': '\\r',
            '"' : '\\"',
            '\\': '\\\\'
        },
        rep;


    function quote(string) {

// If the string contains no control characters, no quote characters, and no
// backslash characters, then we can safely slap some quotes around it.
// Otherwise we must also replace the offending characters with safe escape
// sequences.

        escapable.lastIndex = 0;
        return escapable.test(string) ? '"' + string.replace(escapable, function (a) {
            var c = meta[a];
            return typeof c === 'string'
                ? c
                : '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
        }) + '"' : '"' + string + '"';
    }


    function str(key, holder) {

// Produce a string from holder[key].

        var i,          // The loop counter.
            k,          // The member key.
            v,          // The member value.
            length,
            mind = gap,
            partial,
            value = holder[key];

// If the value has a toJSON method, call it to obtain a replacement value.

        if (value && typeof value === 'object' &&
                typeof value.toJSON === 'function') {
            value = value.toJSON(key);
        }

// If we were called with a replacer function, then call the replacer to
// obtain a replacement value.

        if (typeof rep === 'function') {
            value = rep.call(holder, key, value);
        }

// What happens next depends on the value's type.

        switch (typeof value) {
        case 'string':
            return quote(value);

        case 'number':

// JSON numbers must be finite. Encode non-finite numbers as null.

            return isFinite(value) ? String(value) : 'null';

        case 'boolean':
        case 'null':

// If the value is a boolean or null, convert it to a string. Note:
// typeof null does not produce 'null'. The case is included here in
// the remote chance that this gets fixed someday.

            return String(value);

// If the type is 'object', we might be dealing with an object or an array or
// null.

        case 'object':

// Due to a specification blunder in ECMAScript, typeof null is 'object',
// so watch out for that case.

            if (!value) {
                return 'null';
            }

// Make an array to hold the partial results of stringifying this object value.

            gap += indent;
            partial = [];

// Is the value an array?

            if (Object.prototype.toString.apply(value) === '[object Array]') {

// The value is an array. Stringify every element. Use null as a placeholder
// for non-JSON values.

                length = value.length;
                for (i = 0; i < length; i += 1) {
                    partial[i] = str(i, value) || 'null';
                }

// Join all of the elements together, separated with commas, and wrap them in
// brackets.

                v = partial.length === 0
                    ? '[]'
                    : gap
                    ? '[\n' + gap + partial.join(',\n' + gap) + '\n' + mind + ']'
                    : '[' + partial.join(',') + ']';
                gap = mind;
                return v;
            }

// If the replacer is an array, use it to select the members to be stringified.

            if (rep && typeof rep === 'object') {
                length = rep.length;
                for (i = 0; i < length; i += 1) {
                    if (typeof rep[i] === 'string') {
                        k = rep[i];
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
            } else {

// Otherwise, iterate through all of the keys in the object.

                for (k in value) {
                    if (Object.prototype.hasOwnProperty.call(value, k)) {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
            }

// Join all of the member texts together, separated with commas,
// and wrap them in braces.

            v = partial.length === 0
                ? '{}'
                : gap
                ? '{\n' + gap + partial.join(',\n' + gap) + '\n' + mind + '}'
                : '{' + partial.join(',') + '}';
            gap = mind;
            return v;
        }
    }

// If the JSON object does not yet have a stringify method, give it one.

    if (typeof JSON.stringify !== 'function') {
        JSON.stringify = function (value, replacer, space) {

// The stringify method takes a value and an optional replacer, and an optional
// space parameter, and returns a JSON text. The replacer can be a function
// that can replace values, or an array of strings that will select the keys.
// A default replacer method can be provided. Use of the space parameter can
// produce text that is more easily readable.

            var i;
            gap = '';
            indent = '';

// If the space parameter is a number, make an indent string containing that
// many spaces.

            if (typeof space === 'number') {
                for (i = 0; i < space; i += 1) {
                    indent += ' ';
                }

// If the space parameter is a string, it will be used as the indent string.

            } else if (typeof space === 'string') {
                indent = space;
            }

// If there is a replacer, it must be a function or an array.
// Otherwise, throw an error.

            rep = replacer;
            if (replacer && typeof replacer !== 'function' &&
                    (typeof replacer !== 'object' ||
                    typeof replacer.length !== 'number')) {
                throw new Error('JSON.stringify');
            }

// Make a fake root object containing our value under the key of ''.
// Return the result of stringifying the value.

            return str('', {'': value});
        };
    }


// If the JSON object does not yet have a parse method, give it one.

    if (typeof JSON.parse !== 'function') {
        JSON.parse = function (text, reviver) {

// The parse method takes a text and an optional reviver function, and returns
// a JavaScript value if the text is a valid JSON text.

            var j;

            function walk(holder, key) {

// The walk method is used to recursively walk the resulting structure so
// that modifications can be made.

                var k, v, value = holder[key];
                if (value && typeof value === 'object') {
                    for (k in value) {
                        if (Object.prototype.hasOwnProperty.call(value, k)) {
                            v = walk(value, k);
                            if (v !== undefined) {
                                value[k] = v;
                            } else {
                                delete value[k];
                            }
                        }
                    }
                }
                return reviver.call(holder, key, value);
            }


// Parsing happens in four stages. In the first stage, we replace certain
// Unicode characters with escape sequences. JavaScript handles many characters
// incorrectly, either silently deleting them, or treating them as line endings.

            text = String(text);
            cx.lastIndex = 0;
            if (cx.test(text)) {
                text = text.replace(cx, function (a) {
                    return '\\u' +
                        ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
                });
            }

// In the second stage, we run the text against regular expressions that look
// for non-JSON patterns. We are especially concerned with '()' and 'new'
// because they can cause invocation, and '=' because it can cause mutation.
// But just to be safe, we want to reject all unexpected forms.

// We split the second stage into 4 regexp operations in order to work around
// crippling inefficiencies in IE's and Safari's regexp engines. First we
// replace the JSON backslash pairs with '@' (a non-JSON character). Second, we
// replace all simple value tokens with ']' characters. Third, we delete all
// open brackets that follow a colon or comma or that begin the text. Finally,
// we look to see that the remaining characters are only whitespace or ']' or
// ',' or ':' or '{' or '}'. If that is so, then the text is safe for eval.

            if (/^[\],:{}\s]*$/
                    .test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@')
                        .replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']')
                        .replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {

// In the third stage we use the eval function to compile the text into a
// JavaScript structure. The '{' operator is subject to a syntactic ambiguity
// in JavaScript: it can begin a block or an object literal. We wrap the text
// in parens to eliminate the ambiguity.

                j = eval('(' + text + ')');

// In the optional fourth stage, we recursively walk the new structure, passing
// each name/value pair to a reviver function for possible transformation.

                return typeof reviver === 'function'
                    ? walk({'': j}, '')
                    : j;
            }

// If the text is not JSON parseable, then a SyntaxError is thrown.

            throw new SyntaxError('JSON.parse');
        };
    }
}());


