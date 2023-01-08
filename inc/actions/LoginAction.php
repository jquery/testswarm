<?php
/**
 * Authenticate with a project and start the session.
 *
 * @author John Resig
 * @author Timo Tijhof
 * @since 0.1.0
 * @package TestSwarm
 */
class LoginAction extends Action {

	/**
	 * @actionMethod POST Required.
	 * @actionParam string projectID
	 * @actionParam string projectPassword
	 */
	public function doAction() {
		$context = $this->getContext();
		$db = $context->getDB();
		$request = $context->getRequest();
		$auth = $context->getAuth();

		// Already logged-in
		if ( $auth ) {
			$projectID = $auth->project->id;

		// Try logging in
		} else {

			if ( !$request->wasPosted() ) {
				$this->setError( "requires-post" );
				return;
			}

			$projectID = $request->getVal( 'projectID' );
			$projectPassword = $request->getVal( 'projectPassword' );

			if ( !$projectID || !$projectPassword ) {
				$this->setError( "missing-parameters" );
				return;
			}

			$projectRow = $db->getRow(str_queryf(
				'SELECT
					id,
					display_title,
					site_url,
					password,
					auth_token,
					updated,
					created
				FROM projects
				WHERE id = %s;',
				$projectID
			));
			if ( !$projectRow ) {
				$this->setError( "invalid-input" );
				return;
			}

			$passwordHash = $projectRow->password;
			unset( $projectRow->password );

			if ( self::comparePasswords( $passwordHash, $projectPassword ) ) {
				// Start auth session
				$request->setSessionData( 'auth', (object) array(
					'project' => $projectRow,
					'sessionToken' => self::generateRandomHash( 40 ),
				) );
			} else {
				$this->setError( "invalid-input" );
				return;
			}
		}

		// We're still here, authentication succeeded!
		$this->setData( array(
			'id' => $projectID
		) );
	}

	/**
	 * Names may only contain lowercase characters and numbers and must start with a letter.
	 * (github.com/jquery/testswarm/issues/118)
	 * @param string $name
	 * @return bool
	 */
	public static function isValidName( $name ) {
		return !!preg_match( '/' . self::getNameValidationRegex() . '/', $name );
	}

	public static function getNameValidationRegex() {
		return '^[a-z][a-z0-9]{0,128}$';
	}

	protected static function getHashAlgo() {
		static $algo = null;
		if ( $algo === null ) {
			// Use the best acceptable algorithm in this environment
			$algos = hash_algos();
			foreach ( array( 'whirlpool', 'sha256', 'sha1' ) as $choice ) {
				if ( in_array( $choice, $algos ) ) {
					$algo = $choice;
					return $algo;
				}
			};
			throw new SwarmException( 'No acceptable algorithm available.' );
		}
		return $algo;
	}

	public static function generateRandomHash( $length ) {
		$hash = '';
		while ( strlen( $hash ) < $length ) {
			// Various random sources
			$rand =
				serialize( $_SERVER )
				. rand() . uniqid( (string)mt_rand(), true )
				. ( function_exists( 'getmypid' ) ? getmypid() : '' )
				. ( function_exists( 'memory_get_usage' ) ? memory_get_usage( true ) : '' )
				. realpath( __FILE__ )
				. serialize( @stat( __FILE__ ) ) // stat() can be a bad boy
			;
			$hash .= hash( self::getHashAlgo(), $rand );

		}
		return substr( $hash, 1, $length );
	}

	/**
	 * @param string $password Plaintext password.
	 * @param string|false $salt [optional] A salt will be generated, optionally
	 *  pass this to re-use a salt.
	 * @return string
	 */
	public static function generatePasswordHash( $password, $salt = false ) {
		if ( $salt === false ) {
			$salt = self::generateRandomHash( 8 );
		}
		return ':A:' . $salt . ':' . sha1( $salt . '|' . sha1( $password ) );
	}

	/**
	 * Generate a ':M:' type blob with user table info from
	 * an older database.
	 * @param stdClass $userRow Row from old `users` table
	 * @return string Raw value for `projects.password` column
	 */
	public static function generatePasswordHashForUserrow( $userRow ) {
		return ':M:' . $userRow->seed . ':' . $userRow->password;
	}

	/**
	 * @param string $hash Password hash (from database).
	 * @param string $input Plaintext password for comparison.
	 * @return bool
	 */
	public static function comparePasswords( $hash, $input ) {
		$type = substr( $hash, 0, 3 );

		// New algorythm since 1.0.0
		if ( $type === ':A:' ) {
			list( $salt, $realHash ) = explode( ':', substr( $hash, 3 ), 2 );
			return sha1( $salt . '|' . sha1( $input ) ) === $realHash;

		// Migrated from old 'users' table (see #generatePasswordHashForUserrow and dbUpdate.php)
		} elseif ( $type === ':M:' ) {
			list( $salt, $realHash ) = explode( ':', substr( $hash, 3 ), 2 );
			// The old users table stores sha1 of seed + plain password
			return sha1( $salt . $input ) === $realHash;
		} else {
			return false;
		}
	}
}
