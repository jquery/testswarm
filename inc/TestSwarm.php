<?php
/**
 * Wrapper class containing various request-specific objects.
 * Each of these objects is created only once for the context.
 * The creation happends on-demand and is put in a private cache.
 *
 * @author Timo Tijhof, 2012-2013
 * @since 1.0.0
 * @package TestSwarm
 */
class TestSwarmContext {
	protected $browserInfo, $conf, $db, $request, $auth, $versionInfo;

	/**
	 * The context is self-initializing. The only thing it needs to be passed is
	 * an object with all setting keys from testswarm-defaults.json. Logic for
	 * loading defaults and overriding with local settings is in inc/init.php
	 * @param $config
	 */
	public function __construct( stdClass $config ) {
		$this->conf = $config;
	}

	public function getBrowserInfo() {
		if ( $this->browserInfo === null ) {
			$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '-';
			$this->browserInfo = BrowserInfo::newFromContext( $this, $ua );
		}
		return $this->browserInfo;
	}

	/**
	 * Get the configuration object
	 * @return stdClass
	 */
	public function getConf() {
		return $this->conf;
	}

	/**
	 * Get the Database object
	 * @return Database
	 */
	public function getDB() {
		if ( $this->db === null ) {
			// Check if there is a database lock
			$lock = $this->dbLock();
			if ( $lock ) {
				throw new SwarmException(
					'Database is temporarily locked for maintenance (since: '
					. strftime( '%c', $lock ) . ')'
				);
			}
			$this->db = Database::newFromContext( $this );
		}
		return $this->db;
	}

	/**
	 * Get and set the lock status.
	 * @param $change bool: [optional] Change the lock state.
	 * @return bool|int Boolean false or a timestamp of when the lock was set.
	 */
	public function dbLock( $change = null ) {
		$dbLockFile = $this->getConf()->storage->cacheDir . '/database.lock';
		$dbLocktime = false;
		if ( is_bool( $change ) ) {
			if ( $change ) {
				$done = @touch( $dbLockFile );
			} else {
				$done = @unlink( $dbLockFile );
			}
			if ( !$done ) {
				throw new SwarmException( 'Unable to change database.lock' );
			}
		}
		if ( file_exists( $dbLockFile ) ) {
			$dbLocktime = @filemtime( $dbLockFile );
		}
		return $dbLocktime;
	}

	/**
	 * Get the WebRequest object
	 * @return WebRequest
	 */
	public function getRequest() {
		if ( $this->request === null ) {
			$this->request = WebRequest::newFromContext( $this );
		}
		return $this->request;
	}

	/**
	 * Get the authentication object.
	 * This logic used to be in init.php and accessed in other files directly
	 * from the session. However since initialisation of the session can sometimes
	 * only be determined after the context is created (see api.php), we need to
	 * defer it to here where we lazy-init the object. Another reason is security
	 * (see github.com/jquery/testswarm/issues/181), we need to invalidate the
	 * session if we detect it is no longer up to date with the corresponding
	 * account in the database (which we can only access after init).
	 * @return object|false
	 */
	public function getAuth() {
		if ( $this->auth === null ) {
			$request = $this->getRequest();

			$auth = $request->getSessionData( 'auth' );
			if ( !$auth ) {
				return $this->flushAuth();
			}

			// Invalidate session if it is malformed (different structure)
			if ( !isset( $auth->project ) || !is_object( $auth->project ) ) {
				return $this->flushAuth();
			}

			// Invalidate session if it is outdated (password changed, project deleted, ..)
			$projectRow = $this->getDB()->getRow(str_queryf(
				'SELECT
					auth_token,
					updated
				FROM projects
				WHERE id = %s',
				$auth->project->id
			));
			if ( $auth->project->auth_token !== $projectRow->auth_token || $auth->project->updated !== $projectRow->updated ) {
				return $this->flushAuth();
			}
			// Valid!
			$this->auth = $auth;
		}
		return $this->auth;
	}

	public function flushAuth() {
		$this->getRequest()->setSessionData( 'auth', null );
		$this->auth = false;
		return $this->auth;
	}

	public function createDerivedRequestContext( Array $query = array(), $method = 'GET' ) {
		$derivContext = clone $this;

		$req = DerivativeWebRequest::newFromContext( $derivContext );
		$req->setRawQuery( $query );
		$req->setWasPosted( $method === 'POST' );

		$derivContext->request = $req;
		return $derivContext;
	}

	/**
	 * Get the current TestSwarm version (cached for 5 minutes).
	 *
	 * @param $options string|array: 'bypass-cache'
	 * @return array (see also calculateVersionInfo)
	 */
	public function getVersionInfo( $options = array() ) {
		$options = (array)$options;

		if ( in_array( 'bypass-cache', $options ) ) {
			return $this->calculateVersionInfo();
		}

		// Cache within the class, never calculate more than once per request
		if ( $this->versionInfo !== null ) {
			return $this->versionInfo;
		}

		// Deal with cache expiration
		$versionCacheFile = $this->getConf()->storage->cacheDir . '/version_info.json';
		if ( is_readable( $versionCacheFile ) ) {
			$versionCacheFileUpdated = filemtime( $versionCacheFile );
			if ( $versionCacheFileUpdated < strtotime( '5 minutes ago' ) ) {
				unlink( $versionCacheFile );
			}
		}

		// If cache file (still) exists it means we can use it
		if ( is_readable( $versionCacheFile ) ) {
			$this->versionInfo = json_decode( file_get_contents( $versionCacheFile ), /*assoc=*/true );
			return $this->versionInfo;
		}

		// Calculate it and populate the class cache and file cache
		$this->versionInfo = $this->calculateVersionInfo();
		$isWritten = file_put_contents( $versionCacheFile, json_encode( $this->versionInfo ) );
		if ( $isWritten === false ) {
			throw new SwarmException( 'Writing to cache directory failed.' );
		}

		return $this->versionInfo;
	}

	/**
	 * Compute the version of this TestSwarm install, including the Git hash
	 * if the installation directory contains a git repository.
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function calculateVersionInfo() {
		global $swarmInstallDir;

		$baseVersionFile = "$swarmInstallDir/config/version.ini";
		if ( !is_readable( $baseVersionFile ) ) {
			throw new SwarmException( 'version.ini is missing or unreadable.' );
		}

		$swarmVersion = trim( file_get_contents( $baseVersionFile ) );
		$devInfo = null;

		// If this is a git repository, get a hold of the HEAD SHA1 hash as well,
		// and append it to the version.
		$gitHeadFile = "$swarmInstallDir/.git/HEAD";
		if ( is_readable( $gitHeadFile ) ) {
			// Get HEAD
			$gitHeadFileCnt = file_get_contents( $gitHeadFile );
			if ( preg_match( '/ref: (.*)/', $gitHeadFileCnt, $matches ) ) {
				$gitHead = trim( $matches[1] );
			} else {
				$gitHead = trim( $gitHead );
			}

			// Get current branch
			if ( $gitHead && preg_match( "#^refs/heads/(.*)$#", $gitHead, $m ) ) {
				// If it is a simple head, only return the heads name
				$gitBranch = $m[1];
			} else {
				// Otherwise it is something else, for which we'll show the full name
				$gitBranch = $gitHead;
			}

			// Get SHA1
			$gitRefFile = "$swarmInstallDir/.git/$gitHead";
			if ( is_readable( $gitRefFile ) ) {
				$gitSHA1 = trim( file_get_contents( $gitRefFile ) );
			} else {
				// If such refs file doesn't exist, HEAD is detached,
				// in which case ./.git/HEAD contains the SHA1 directly.
				$gitSHA1 = $gitHead;
			}

			$devInfo = array(
				'branch' => $gitBranch,
				'SHA1' => $gitSHA1,
			);
		}

		return array(
			'TestSwarm' => $swarmVersion,
			'devInfo' => $devInfo,
		);
	}
}
