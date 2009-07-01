(function(){

	var DEBUG = false;

	var doPost = false;

	try {
		doPost = !!window.top.postMessage;
	} catch(e){}

	var url = window.location.search;
	url = decodeURIComponent( url.slice( url.indexOf("swarmURL=") + 9 ) );

	if ( !DEBUG && (!url || url.indexOf("http") !== 0) ) {
		return;
	}

	// QUnit (jQuery)
	// http://docs.jquery.com/QUnit
	if ( typeof QUnit !== "undefined" ) {
		QUnit.done = function(fail, total){
			// Clean up the HTML (remove any un-needed test markup)
			remove("nothiddendiv");
			remove("loadediframe");
			remove("dl");
			remove("main");
			
			// Show any collapsed results
			var ol = document.getElementsByTagName("ol");
			for ( var i = 0; i < ol.length; i++ ) {
				ol[i].style.display = "block";
			}

			submit({
				fail: fail,
				total: total,
				results: trimSerialize( document )
			});
		};

	// UnitTestJS (Prototype, Scriptaculous)
	// http://github.com/tobie/unittest_js/tree/master
	} else if ( typeof Test !== "undefined" && Test && Test.Unit && Test.Unit.runners ) {
		var total_runners = Test.Unit.runners.length, cur_runners = 0;
		var total = 0, fail = 0;

		for (var i = 0; i < Test.Unit.runners.length; i++) (function(i){
			var finish = Test.Unit.runners[i].finish;
			Test.Unit.runners[i].finish = function(){
				finish.call( this );

				var results = this.getResult();
				total += results.assertions;
				fail += results.failures + results.errors;

				if ( ++cur_runners === total_runners ) {
					submit({
						fail: fail,
						total: total,
						results: trimSerialize( document )
					});
				}
			};
		})(i);

	// JSSpec (MooTools)
	// http://jania.pe.kr/aw/moin.cgi/JSSpec
	} else if ( typeof JSSpec !== "undefined" && JSSpec && JSSpec.Logger ) {
		var onRunnerEnd = JSSpec.Logger.prototype.onRunnerEnd;
		JSSpec.Logger.prototype.onRunnerEnd = function(){
			onRunnerEnd.call(this);

			// Show any collapsed results
			var ul = document.getElementsByTagName("ul");
			for ( var i = 0; i < ul.length; i++ ) {
				ul[i].style.display = "block";
			}

			submit({
				fail: JSSpec.runner.getTotalFailures() + JSSpec.runner.getTotalErrors(),
				total: JSSpec.runner.totalExamples,
				results: trimSerialize( document )
			});
		};

	// JSUnit
	// http://www.jsunit.net/
	} else if (  typeof JsUnitTestMananger !== "undefined" ) {
		var _done = JsUnitTestMananger.prototype._done;
		JsUnitTestMananger.prototype._done = function(){
			_done.call(this);

			submit({
				fail: this.failureCount + this.errorCount,
				total: this.totalCount,
				results: "<pre>" + this.log.join("\n") + "</pre>"
			});
		};
		
	}

	function trimSerialize(doc) {
		var scripts = doc.getElementsByTagName("script");
		while ( scripts.length ) {
			remove( scripts[0] );
		}

		var links = doc.getElementsByTagName("link");
		for ( var i = 0; i < links.length; i++ ) {
			links[i].setAttribute("href", links[i].href);
		}

		return ("<html>" + doc.documentElement.innerHTML + "</html>")
			.replace(/\s+/g, " ");
	}

	function remove(elem){
		if ( typeof elem === "string" ) {
			elem = document.getElementById( elem );
		}

		if ( elem ) {
			elem.parentNode.removeChild( elem );
		}
	}

	function submit(params){
		var paramItems = (url.split("?")[1] || "").split("&");

		for ( var i = 0; i < paramItems.length; i++ ) {
			if ( paramItems[i] ) {
				var parts = paramItems[i].split("=");
				params[ parts[0] ] = parts[1];
			}
		}

		if ( doPost ) {
			// Build Query String
			var query = "";

			for ( var i in params ) {
				query += ( query ? "&" : "" ) + i + "=" +
					encodeURIComponent(params[i]);
			}

			if ( DEBUG ) {
				alert( query );
			} else {
				window.top.postMessage( query, "*" );
			}

		} else {
			var form = document.createElement("form");
			form.action = url;
			form.method = "POST";

			for ( var i in params ) {
				var input = document.createElement("input");
				input.type = "hidden";
				input.name = i;
				input.value = params[i];
				form.appendChild( input );
			}

			if ( DEBUG ) {
				alert( form.innerHTML );
			} else {
				document.body.appendChild( form );
				form.submit();
			}
		}
	}

})();
