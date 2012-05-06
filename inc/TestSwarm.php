<?php
/**
 * Wrapper class containing various request-specific objects.
 * Each of these objects is created only once for the context.
 * The creation happends on-demand and is put in a private cache.
 *
 * @author Timo Tijhof, 2012
 * @since 1.0.0
 * @package TestSwarm
 */
class TestSwarmContext {
	protected $browserInfo, $conf, $db, $request, $version;

	/**
	 * The context is self-initializing. The only thing it
	 * needs to be passed is an array with all setting keys from testswarm.ini
	 * (including ones commented out in the sample file, it has to contain them all)
	 * Population of default values of optional settings happens in init.php
	 * @param $config
	 */
	public function __construct( stdClass $config ) {
		$this->conf = $config;
	}

	public function getBrowserInfo() {
		if ( $this->browserInfo === null ) {
			$ua = isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : "";
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
					"Database is temporarily locked for maintenance (since: "
					. strftime( "%c", $lock ) . ")"
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
		$dbLockFile = $this->getConf()->storage->cacheDir . "/database.lock";
		$dbLocktime = false;
		if ( is_bool( $change ) ) {
			if ( $change ) {
				$done = @touch( $dbLockFile );
			} else {
				$done = @unlink( $dbLockFile );
			}
			if ( !$done ) {
				throw new SwarmException( "Unable to change database.lock" );
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
	 * @return string (see also calculateVersion)
	 */
	public function getVersion( $options = array() ) {
		$options = (array)$options;

		if ( in_array( "bypass-cache", $options ) ) {
			return $this->calculateVersion();
		}

		// Cache within the class, never calculate more than once per request
		if ( $this->version !== null ) {
			return $this->version;
		}

		// Deal with cache expiration
		$versionCacheFile = $this->getConf()->storage->cacheDir . "/version_testswarm.cache";
		if ( is_readable( $versionCacheFile ) ) {
			$versionCacheFileUpdated = filemtime( $versionCacheFile );
			if ( $versionCacheFileUpdated < strtotime( "5 minutes ago" ) ) {
				unlink( $versionCacheFile );
			}
		}

		// If cache file (still) exists it means we can use it
		if ( is_readable( $versionCacheFile ) ) {
			$this->version = trim( file_get_contents( $versionCacheFile ) );
			return $this->version;
		}

		// Calculate it and populate the class cache and file cache
		$this->version = $this->calculateVersion();
		$isWritten = file_put_contents( $versionCacheFile, $this->version );
		if ( $isWritten === false ) {
			throw new SwarmException( "Writing to cache directory failed." );
		}

		return $this->version;
	}

	/**
	 * Compute the version of this TestSwarm install, including the Git hash
	 * if the installation directory contains a git repository.
	 * @since 1.0.0
	 *
	 * @return string: e.g. "0.2.0" (for installs without a Git repo),
	 * or something like "1.0.0-dev (749a4af2)" for installs with a Git repo.
	 */
	protected function calculateVersion() {
		global $swarmInstallDir;

		$baseVersionFile = "$swarmInstallDir/config/version.ini";
		if ( !is_readable( $baseVersionFile ) ) {
			throw new SwarmException( "version.ini is missing or unreadable." );
		}
		$version = trim( file_get_contents( $baseVersionFile ) );

		// If this is a git repository, get a hold of the HEAD SHA1 hash as well,
		// and append it to the version.
		$gitHeadFile = "$swarmInstallDir/.git/HEAD";
		if ( is_readable( $gitHeadFile ) ) {
			$gitHead = file_get_contents( $gitHeadFile );
			if ( preg_match( "/ref: (.*)/", $gitHead, $matches ) ) {
				$gitHead = rtrim( $matches[1] );
			} else {
				$gitHead = trim( $gitHead );
			}

			$gitRefFile = "$swarmInstallDir/.git/$gitHead";
			if ( is_readable( $gitRefFile ) ) {
				$gitHeadState = basename( $gitRefFile ) . '@' . substr( rtrim( file_get_contents( $gitRefFile ) ), 0, 8 );
			} else {
				// If such refs file doesn't exist, maybe HEAD is detached,
				// in which case ./.git/HEAD should contain the actual SHA1 already.
				$gitHeadState = substr( $gitHead, 0, 8);
			}

			$version .= " (" . $gitHeadState . ")";
		}

		return $version;
	}
}
