<?php
return [
	'target_php_version' => '5.4',

	// A list of directories that should be parsed for class and
	// method information. After excluding the directories
	// defined in exclude_analysis_directory_list, the remaining
	// files will be statically analyzed for errors.
	//
	// Thus, both first-party and third-party code being used by
	// your application should be included in this list.
	'file_list' => [
		'api.php',
		'index.php',
	],
	'directory_list' => [
		'inc/',
		'vendor/ua-parser/',
	],

	'exclude_file_list' => [
	],

	// A directory list that defines files that will be excluded
	// from static analysis, but whose class and method
	// information should be included.
	'exclude_analysis_directory_list' => [
		'vendor/'
	],

	// A list of plugin files to execute.
	//
	// Documentation about available bundled plugins can be found
	// at https://github.com/phan/phan/tree/v3/.phan/plugins
	//
	'plugins' => [
		// Recommended
		'DuplicateArrayKeyPlugin',
		'DuplicateExpressionPlugin',
		'LoopVariableReusePlugin',
		'PregRegexCheckerPlugin',
		'RedundantAssignmentPlugin',
		'SimplifyExpressionPlugin',
		'UnreachableCodePlugin',
		'UnusedSuppressionPlugin',

		// Extra ones
		'AlwaysReturnPlugin',
		'DollarDollarPlugin',
		'EmptyStatementListPlugin',
		'PrintfCheckerPlugin',
		'SleepCheckerPlugin',
		'UseReturnValuePlugin',
	],

	'suppress_issue_types' => [
		// Can't, PHP 7.0+
		'PhanPluginDuplicateConditionalNullCoalescing',
		// Can't, https://github.com/phan/phan/issues/4753
		'PhanPrivateFinalMethod',

		// Ignore
		'PhanPluginDuplicateConditionalTernaryDuplication',
		'PhanAccessOverridesFinalMethodPHPDoc',
	],
];
