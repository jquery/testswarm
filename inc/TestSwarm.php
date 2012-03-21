<?php
/**
 * Wrapper class containing various request-specific objects.
 * Each of these objects is created only once for the context.
 * The creation happends on-demand and is put in a private cache. 
 *
 * @author Timo Tijhof, 2012
 * @since 0.3.0
 * @package TestSwarm
 */
class TestSwarmContext {
	private $browserInfo, $conf, $db, $request;

	/**
	 * The context is self-initializing. The only thing it
	 * needs to be passed is an array with all setting keys from testswarm.ini
	 * (including ones commented out in the sample file, it has to contain them all)
	 * Population of default values of optional settings happens in init.php
	 * @param $swarmConfig array
	 */
	public function __construct( Array $swarmConfig ) {
		$conf = new stdClass;
		foreach ( $swarmConfig as $key => $val ) {
			$conf->$key = is_array( $val ) ? (object)$val : $val;
		}
		$this->conf = $conf;
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
			$this->db = Database::newFromContext( $this );
		}
		return $this->db;
	}

	/**
	 * Get the WebRequest object
	 * @return WebRequest
	 */
	public function getRequest() {
		if ( $this->request === null ) {
			$this->request = new WebRequest( $this );
		}
		return $this->request;
	}
}