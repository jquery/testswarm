<?php
/**
 * Basic class to abstract database interaction.
 *
 * @author Timo Tijhof, 2012
 * @since 0.3.0
 * @package TestSwarm
 */
class Database {
	private $context;

	protected $host, $username, $password, $dbname;
	protected $conn;
	protected $isOpen = false;

	/**
	 * Creates a Database object, opens the connection and returns the instance.
	 *
	 * @param context TestSwarmContext
	 * @param $connType int: [optional]
	 */

	public static function newFromContext( TestSwarmContext $context, $connType = DBCON_DEFAULT ) {
		$dbConf = $context->getConf()->database;
		$db = new self();

		$db->context = $context;
		$db->host = $dbConf->host;
		$db->username = $dbConf->username;
		$db->password = $dbConf->password;
		$db->dbname = $dbConf->database;

		$db->open( $connType );

		return $db;
	}

	/**
	 * @param $connType int: DBCON_DEFAULT or DBCON_PERSISTENT.
	 */
	public function open( $connType = DBCON_DEFAULT ) {
		$this->close();

		switch ( $connType ) {
		case DBCON_DEFAULT:
			$this->conn = mysql_connect( $this->host, $this->username, $this->password, /*force_new=*/true );
			break;
		case DBCON_PERSISTENT:
			$this->conn = mysql_pconnect( $this->host, $this->username, $this->password );
			break;
		default:
			throw new SwarmException( "Invalid connection type." );
		}

		if ( !$this->conn ) {
			throw new SwarmException( "Connection to {$this->host} failed.\nMySQL Error " . $this->lastErrNo() . ": " . $this->lastErrMsg() );
		}

		if ( $this->dbname ) {
			$isOK = mysql_select_db( $this->dbname, $this->conn );
			if ( !$isOK ) {
				throw new SwarmException( "Selecting database `{$this->dbname}` on {$this->host} failed." );
			}
		} else {
			$isOK = (bool)$this->conn;
		}

		$this->isOpen = $isOK;
		return $this;
	}

	/** @return array of objects|false */
	public function getRows( $sql ) {
		$res = $this->doQuery( $sql );
		if ( $res && $this->getNumRows( $res ) ) {
			$ret = array();
         while ( $res && $row = $this->fetchObject( $res ) ) {
         	$ret[] = $row;
         }
         return $ret;
		}
		return false;
	}

	/** @return obj|false */
	public function getRow( $sql ) {
		$res = $this->doQuery( $sql );
		if ( $res && $this->getNumRows( $res ) ) {
			return $this->fetchObject( $res );
		}
		return false;
	}

	/** @return mixed|false */
	public function getOne( $sql ) {
		$res = $this->doQuery( $sql );
		if ( $res && $this->getNumRows( $res ) ) {
			$row = mysql_fetch_array( $res );
			return $row ? reset( $row ) : false;
		}
		return false;
	}
	
	/** @return int */
	public function getNumRows( $res ) {
		$n = mysql_num_rows( $res );
		if ( $this->lastErrNo() ) { throw new SwarmException( 'Error in getNumRows: ' . $this->lastErrMsg() ); }
		return $n;
	}

	/** 
	 * Queries other than SELECT, such as DELETE, UPDATE and INSERT.
	 * @return resource|false
	 */
	public function query( $sql ) {
		return $this->doQuery( $sql );
	}

	/** @return int */
	public function getInsertId() {
		return intval( mysql_insert_id( $this->conn ) );
	}

	public function lastErrNo() {
		return $this->conn ? mysql_errno( $this->conn ) : mysql_errno();
	}

	public function lastErrMsg() {
		return $this->conn ? mysql_error( $this->conn ) : mysql_error();
	}

	/** @return bool */
	public function close() {
		$this->isOpen = false;
		if ( $this->conn ) {
			$ret = $this->closeConn();
			$this->conn = null;
			return $ret;
		} else {
			return true;
		}
	}

	/** @return MySQL resource|false */
	protected function doQuery( $sql ) {
		return mysql_query( $sql, $this->conn );
	}

	protected function fetchObject( $res ) {
		$obj = mysql_fetch_object( $res );
		if ( $this->lastErrNo() ) { throw new SwarmException( 'Error in fetchObject: ' . $this->lastErrMsg() ); }
		return $obj;
	}

	/** @return bool */
	protected function closeConn() {
		return mysql_close( $this->conn );
	}

	protected function checkEnvironment() {
		if ( !function_exists( "mysql_connect" ) ) {
			throw new SwarmException( "MySQL functions missing." );
		}
	}

	private function __construct() {
		$this->checkEnvironment();
	}
}
