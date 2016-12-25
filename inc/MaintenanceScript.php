<?php
/**
 * This is the central file for maintenance scripts.
 * It is not the entry point however.
 *
 * @author Timo Tijhof
 * @since 1.0.0
 * @package TestSwarm
 */
abstract class MaintenanceScript {
	private $context, $name, $description;
	private $flags = array();
	private $options = array();
	private $generalArgKeys = array();
	private $parsed = array();

	abstract protected function init();

	abstract protected function execute();

	final protected function setDescription( $description ) {
		$this->description = $description;
	}

	/**
	 * Register a flag, usage any of these 4:
	 * - php script.php -a -b value -c "value" -d="value"
	 * @param string $key: one single character.
	 * @param string $type: one of "boolean", "value"
	 * @param string $description
	 */
	protected function registerFlag( $key, $type, $description ) {
		static $types = array( 'boolean', 'value' );
		if ( !is_string( $key ) || strlen( $key ) !== 1 || !in_array( $type, $types ) ) {
			$this->error( 'Illegal flag registration' );
		}
		$this->flags[$key] = array(
			'type' => $type,
			'description' => $description,
		);
	}

	/**
	 * Register an option, usage any of these 4:
	 * - php script.php --foo --bar=value --quux="value"
	 * @param string $name: at least 2 characters
	 * @param string $type: one of "boolean", "value"
	 * @param string $description
	 */
	protected function registerOption( $name, $type, $description ) {
		static $types = array( 'boolean', 'value' );
		if ( !is_string( $name ) || strlen( $name ) < 2 || !in_array( $type, $types ) ) {
			$this->error( 'Illegal option registration' );
		}
		$this->options[$name] = array(
			'type' => $type,
			'description' => $description,
		);
	}

	protected function parseCliArguments() {
		// Prepare for getopt(). Note that it supports "require value" for cli args,
		// but we use our own format instead (see also php.net/getopt).
		$getoptShort = '';
		$getoptLong = array();
		foreach ( $this->flags as $flagKey => $flagInfo ) {
			switch ( $flagInfo['type'] ) {
			case 'value':
				$getoptShort .= $flagKey . '::';
				break;
			case 'boolean':
				$getoptShort .= $flagKey;
				break;
			}
		}
		foreach ( $this->options as $optionName => $optionInfo ) {
			switch ( $optionInfo['type'] ) {
			case 'value':
				$getoptLong[] = $optionName . '::';
				break;
			case 'boolean':
				$getoptLong[] = $optionName;
				break;
			}
		}
		$parsed = getopt( $getoptShort, $getoptLong );
		if ( !is_array( $parsed ) ) {
			$this->error( 'Parsing command line arguments failed.' );
		}
		$this->parsed = $parsed;
	}

	protected function getFlag( $key ) {
		if ( !isset( $this->flags[$key] ) || !isset( $this->parsed[$key] ) ) {
			return false;
		}
		return $this->flags[$key]['type'] === 'boolean' ? true : $this->parsed[$key];
	}

	protected function getOption( $name ) {
		if ( !isset( $this->options[$name] ) || !isset( $this->parsed[$name] ) ) {
			return false;
		}
		return $this->options[$name]['type'] === 'boolean' ? true :$this->parsed[$name];
	}

	public function run() {
		if ( !defined( 'SWARM_ENTRY' ) || SWARM_ENTRY !== 'SCRIPT' ) {
			$this->error( 'MaintenanceScript instances may only be run as part of a maintenace script.' );
		}
		if ( php_sapi_name() !== 'cli' ) {
			$this->error( 'Maintenance scripts may only be run from the command-line interface.' );
		}
		if ( !ini_get( 'register_argc_argv' ) || ini_get( 'register_argc_argv' ) == '0' ) {
			$this->error( 'Maintenance scripts require `register_argc_argv` to be enabled in php.ini.' );
		}

		// General options for all scripts
		$this->registerOption( 'help', 'boolean', 'Display this message' );
		// E.g. to allow puppet to run a script without it facing "(Y/N)" or something.
		$this->registerOption( 'quiet', 'boolean', 'Surpress standard output and requests for cli input' );

		$this->generalArgKeys = array_merge( array_keys( $this->options ), array_keys( $this->flags ) );

		$this->init();
		$name = get_class( $this );
		// "class FooBarScript extends .."
		if ( substr( $name, -6 ) === 'Script' ) {
			$name = substr( $name, 0, -6 );
		}
		$this->name = $name;
		if ( !isset( $this->description ) ) {
			$this->error( "{$this->name} is missing a description." );
		}
		$this->parseCliArguments();

		// Generate header
		$version = $this->getContext()->getVersionInfo( 'bypass-cache' );
		$versionText = $version['TestSwarm'];
		if ( $version['devInfo'] ) {
			$versionText .= ' (' . $version['devInfo']['branch'] . ' ' . substr( $version['devInfo']['SHA1'], 0, 7 ) . ')';
		}
		$description = wordwrap( $this->description, 72, "\n", true );
		$description = explode( "\n", $description );
		$description[] = str_repeat( '-', 72 );
		$label = "{$this->name}: ";
		$prefix = str_repeat( ' ', strlen( $label ) );
		$description = $label . implode( "\n" . $prefix, $description );
		$this->out("[TestSwarm $versionText] Maintenance script

$description
");

		// Help or continue
		if ( $this->getOption( 'help' ) ) {
			$this->displayHelp();
		} else {
			try {
				$this->execute();
			} catch ( Exception $e ) {
				$this->error( $e );
			}
		}
		$this->out( '' );
		exit;
	}

	protected function displayHelp() {
		$helpScript = '';
		$helpGeneral = '';
		$placeholder = array(
			'boolean' => '',
			'value' => ' <value>',
		);
		foreach ( $this->flags as $flagKey => $flagInfo ) {
			$help = "\n  -{$flagKey}" . $placeholder[$flagInfo['type']] . ": {$flagInfo['description']}";
			if ( in_array( $flagKey, $this->generalArgKeys ) ) {
				$helpGeneral .= $help;
			} else {
				$helpScript .= $help;
			}
		}
		foreach ( $this->options as $optionName => $optionInfo ) {
			$help = "\n  --{$optionName}" . $placeholder[$optionInfo['type']] . ": {$optionInfo['description']}";
			if ( in_array( $optionName, $this->generalArgKeys ) ) {
				$helpGeneral .= $help;
			} else {
				$helpScript .= $help;
			}
		}
		print ($helpScript ? "Parameters to this script:$helpScript" : '')
			. ($helpScript && $helpGeneral ? "\n\n" : '')
			. ($helpGeneral ? "General parameters:$helpGeneral" : '');
	}

	/** @param $seconds int */
	protected function wait( $seconds, $message = '' ) {
		print $message;
		$backspace = chr(8);
		for ( $i = $seconds; $i >= 0; $i-- ) {
			if ( $i != $seconds ) {
				$prevNrLen = strlen( $i + 1 );
				// We have to both print backspaces, spaces and then backspaces again. On
				// MacOSX, backspaces only move the cursor, it doesn't hide the characters.
				// So we overwrite with spaces and then back up (10|->9| instead of 10|->9|0)
				print str_repeat( $backspace, $prevNrLen ) . str_repeat( ' ', $prevNrLen )
					. str_repeat( $backspace, $prevNrLen );
			}
			print $i;
			flush();
			if ( $i > 0 ) {
				sleep( 1 );
			}
		}
		print "\n";
	}

	/**
	 * @param string $action: Correct grammar "This script will $action!"
	 */
	protected function timeWarningForScriptWill( $action, $seconds = 10 ) {
		$this->wait( 10, "WARNING: This script will $action!\nYou can abort now with Control-C. Starting in " );
	}

	/**
	 * @param string $message: [optional] Output text before the input cursor.
	 * @return string
	 */
	protected function cliInput( $prefix = '> ' ) {
		if ( $this->getOption( 'quiet' ) ) {
			return '';
		}
		$this->checkAtty();

		if ( function_exists( 'readline' ) ) {
			// Use readline if available, it's much nicer to work with for the user
			// (autocompletion of filenames and no weird characters when using arrow keys)
			return readline( $prefix );
		} else {
			// Readline is not available on Windows platforms (php.net/intro.readline)
			$this->outRaw( $prefix );
			return rtrim( fgets( STDIN ), "\n" );
		}
	}

	/**
	 * Retrieve a secret value from the cli.
	 * Based on http://www.dasprids.de/blog/2008/08/22/getting-a-password-hidden-from-stdin-with-php-cli.
	 *
	 * @param string $message: [optional] text before the input cursor.
	 * @param string $type: [optional] hidden or stars.
	 * @return string
	 */
	protected function cliInputSecret( $prefix = '> ', $type = 'stars' ) {
		if ( $this->getOption( 'quiet' ) ) {
			return '';
		}
		$this->checkAtty();

		// Can't use readline, it always echoes to the screen.
		$this->outRaw( $prefix );

		$sttyStyle = shell_exec( 'stty -g' );

		if ( $type !== 'stars' ) {
			shell_exec( 'stty -echo' );
			$input = rtrim( fgets( STDIN ), "\n" );
			$this->outRaw( "\n" );
		} else {
			shell_exec( 'stty -icanon -echo min 1 time 0' );
			$input = '';
			while ( true ) {
				$char = fgetc( STDIN );

				// Ready
				if ( $char === "\n" ) {
					$this->outRaw( "\n" );
					break;
				// Backspace
				} else if ( ord( $char) === 127 ) {
					if ( strlen( $input) > 0 ) {
						// Replace last character with blank and return to previous position
						// (back, space, back).
						fwrite( STDOUT, "\x08 \x08" );
						$input = substr( $input, 0, -1 );
					}
				// More input
				} else {
					fwrite( STDOUT, '*' );
					$input .= $char;
				}
			}
		}

		// Restore stty style
		shell_exec( 'stty ' . escapeshellarg( $sttyStyle ) );

		return $input;
	}

	/** @return bool */
	final protected function checkAtty() {
		static $isatty = null;
		if ( $isatty === null ) {
			// Both `echo "foo" | php script.php` and `php script.php > foo.txt`
			// are being prevented.
			$isatty = posix_isatty( STDIN ) && posix_isatty( STDOUT );

			// No need to re-run this check each time, we abort within the if-null check
			if ( !$isatty ) {
				$this->error( 'This script requires an interactive terminal for output and input. Use --quiet to skip input requests.' );
			}
		}
		return $isatty;
	}

	protected function out( $text ) {
		$this->outRaw( "$text\n" );
	}

	protected function outRaw( $text ) {
		if ( !$this->getOption( 'quiet' ) ) {
			print $text;
		}
	}

	protected function error( $msg ) {
		$msg = "Error: $msg\n";
		fwrite( STDERR, $msg );
		exit( E_ERROR );
	}

	public static function newFromContext( TestSwarmContext $context ) {
		$script = new static();
		$script->context = $context;
		return $script;
	}

	final protected function getContext() {
		return $this->context;
	}

	final private function __construct() {}
}
