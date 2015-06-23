<?php
/**
 * Database abstraction layer for MySQL.
 *
 * Some methods are based on:
 * - http://svn.wikimedia.org/viewvc/mediawiki/trunk/phase3/includes/db/Database.php?view=markup&pathrev=113601
 * - http://svn.wikimedia.org/viewvc/mediawiki/trunk/phase3/includes/db/DatabaseMysql.php?view=markup&pathrev=112598
 *
 * @author Timo Tijhof
 * @since 1.0.0
 * @package TestSwarm
 */
class Database {
	private $context;

	protected $host, $username, $password, $dbname;
	protected $conn;
	protected $isOpen = false;

	protected $ignoreErrors = false;

	private $rawQueryHistory = array();

	protected $delimiter = ';';

	/**
	 * Creates a Database object, opens the connection and returns the instance.
	 *
	 * @param context TestSwarmContext
	 * @param $connType int: [optional]
	 */

	public static function newFromContext( TestSwarmContext $context, $connType = SWARM_DBCON_DEFAULT ) {
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

	final protected function getContext() {
		return $this->context;
	}

	/** @return string: current DB name */
	public function getDBname() {
		return $this->dbname;
	}

	/**
	 * @param $connType int: SWARM_DBCON_DEFAULT or SWARM_DBCON_PERSISTENT.
	 */
	public function open( $connType = SWARM_DBCON_DEFAULT ) {
		$this->close();

		switch ( $connType ) {
		case SWARM_DBCON_DEFAULT:
			$this->conn = mysql_connect( $this->host, $this->username, $this->password, /*force_new=*/true );
			break;
		case SWARM_DBCON_PERSISTENT:
			$this->conn = mysql_pconnect( $this->host, $this->username, $this->password );
			break;
		default:
			throw new SwarmException( 'Invalid connection type.' );
		}

		if ( !$this->conn ) {
			throw new SwarmException( "Connection to {$this->host} failed.\nMySQL Error " . $this->lastErrNo() . ': ' . $this->lastErrMsg() );
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

	/** @return bool */
	public function ping() {
		$ping = mysql_ping( $this->conn );
		if ( $ping ) {
			return true;
		}

		$this->open();
		return true;
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

	/** @return bool */
	protected function closeConn() {
		return mysql_close( $this->conn );
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

	/** @return obj|false */
	public function getRow( $sql ) {
		$res = $this->doQuery( $sql );
		if ( $res && $this->getNumRows( $res ) ) {
			return $this->fetchObject( $res );
		}
		return false;
	}

	/** @return array of objects|false */
	public function getRows( $sql ) {
		$res = $this->doQuery( $sql );
		if ( $res && $this->getNumRows( $res ) ) {
			$ret = array();
			while ( $res && $row = $this->fetchObject( $res ) ) {
				$ret[] = $row;
			}
			$this->freeResult( $res );
			return $ret;
		}
		return false;
	}

	/** @return bool */
	public function tableExists( $table ) {
		$table = $this->addIdentifierQuotes( $table );

		$prev = $this->ignoreErrors( true );
		$response = $this->doQuery( "SELECT 1 FROM $table LIMIT 1;" );
		$this->ignoreErrors( $prev );

		return (bool)$response;
	}

	/**
	 * @param $table string
	 * @param $fieldName string
	 * @return bool|object: see also php.net/mysql_fetch_field
	 */
	public function fieldInfo( $table, $fieldName ) {
		$table = $this->addIdentifierQuotes( $table );

		$prev = $this->ignoreErrors( true );
		$response = $this->doQuery( "SELECT * FROM $table LIMIT 1;" );
		$this->ignoreErrors( $prev );

		if ( !$response ) {
			return false;
		}

		$n = mysql_num_fields( $response );
		for ( $i = 0; $i < $n; $i += 1 ) {
			$fieldInfoObj = mysql_fetch_field( $response, $i );
			if ( $fieldInfoObj->name === $fieldName ) {
				return $fieldInfoObj;
			}
		}
		return false;
	}

	/**
	 * Determines whether a field exists in a table.
	 *
	 * @param $table string
	 * @param $fieldName string
	 * @return bool: whether $table has $field.
	 */
	public function fieldExists( $table, $fieldName ) {
		$info = $this->fieldInfo( $table, $fieldName );
		return (bool) $info;
	}

	/**
	 * Roughly parse a .sql file and execute it in small batches
	 * @param $fullSource string: Full source of .sql file
	 * There should be no more than 1 statement (ending in ;) on a single line.
	 * @return bool
	 */
	public function batchQueryFromFile( $fullSource ) {
		$lines = explode( "\n", $fullSource );
		$sql = '';
		$realSql = false;
		foreach ( $lines as $line ) {
			$line = trim( $line );

			// Skip empty lines and comments
			if ( $line === '' || ( $line[0] === '-' && $line[1] === '--' ) ) {
				continue;
			}

			if ( $sql !== '' ) {
				$sql .= ' ';
			}

			$sql .= "$line\n";

			// Is this line the end of statement?
			$lineCopy = $line;
			$lineCopy = preg_replace( '/' . preg_quote( $this->delimiter, '/' ) . '$/', '', $lineCopy );
			if ( $lineCopy != $line ) {
				// Execute what we have so far and reset the sql string
				$realSql = $sql;
				$sql = '';
				$this->query( $realSql );
			}
		}
		return true;
	}

	/**
	 * Queries other than SELECT, such as DELETE, UPDATE and INSERT.
	 * SELECT queries should use getOne, getRow or getRows.
	 *
	 * @return resource|false
	 */
	public function query( $sql ) {
		return $this->doQuery( $sql );
	}

	/** @return int */
	public function getNumRows( $res ) {
		$n = mysql_num_rows( $res );
		if ( !$this->ignoreErrors && $this->lastErrNo() ) {
			throw new SwarmException( 'Error in getNumRows: ' . $this->lastErrMsg() );
		}
		return intval( $n );
	}

	/** @return int */
	public function getInsertId() {
		return intval( mysql_insert_id( $this->conn ) );
	}

	/** @return int */
	public function getAffectedRows() {
		return intval( mysql_affected_rows( $this->conn ) );
	}

	public function lastErrNo() {
		return $this->conn ? mysql_errno( $this->conn ) : mysql_errno();
	}

	public function lastErrMsg() {
		return $this->conn ? mysql_error( $this->conn ) : mysql_error();
	}

	public function strEncode( $str ) {
		$encoded = mysql_real_escape_string( $str, $this->conn );
		if ( $encoded === false ) {
			$this->ping();
			$encoded = mysql_real_escape_string( $str, $this->conn );
		}
		return $encoded;
	}

	public function addIdentifierQuotes( $s ) {
		return '`' . $this->strEncode( $s ) . '`';
	}

	/**
	 * Execute actual queries. Within this class, this function should be used
	 * to do queries, not the wrapper function query(). The logger will log
	 * the caller of the caller of this function. So there should be one function
	 * in between this and outside this class.
	 * @return MySQL resource|false
	 */
	protected function doQuery( $sql ) {
		$microtimeStart = microtime( /*get_as_float=*/true );
		$queryResponse = mysql_query( $sql, $this->conn );

		if ( !$this->ignoreErrors && ( $queryResponse === false || $this->lastErrNo() ) ) {
			throw new SwarmException( 'Error in doQuery: ' . $this->lastErrMsg() );
		}

		$this->logQuery( $sql, $queryResponse, $microtimeStart );

		return $queryResponse;
	}

	protected function fetchObject( $res ) {
		$obj = mysql_fetch_object( $res );
		if ( !$this->ignoreErrors && $this->lastErrNo() ) {
			throw new SwarmException( 'Error in fetchObject: ' . $this->lastErrMsg() );
		}
		return $obj;
	}

	public function freeResult( $res ) {
		if ( is_resource( $res ) ) {
			$ok = mysql_free_result( $res );
			if ( $ok ) {
				return true;
			}
		}
		throw new SwarmException( 'Unable to free MySQL result' );
	}

	public function ignoreErrors( $setting ) {
		$oldVal = $this->ignoreErrors;
		$this->ignoreErrors = (bool)$setting;
		return $oldVal;
	}

	/**
	 * @return bool Whether or not log info was actually generated and saved,
	 * false by default for performance reasons, can be enabled in the
	 * configuration file.
	 */
	protected function logQuery( $sql, $queryResponse, $microtimeStart ) {
		static $doLog;
		if ( $doLog === null ) {
			$doLog = $this->context->getConf()->debug->dbLogQueries;
		}
		if ( $doLog ) {
			$microtimeEnd = microtime( /*get_as_float=*/true );
			$backtrace = debug_backtrace( /*include_objects=*/false );
			// 0:logQuery > 1:doQuery -> 2:(some Database method) -> 3:callee
			$backtrace = $backtrace[ 3 ];
			$backtrace = ( $backtrace['class'] ? "{$backtrace['class']}::" : $backtrace['class'] ) . $backtrace['function'];
			$this->rawQueryHistory[] = array(
				'sql' => $sql,
				'caller' => $backtrace,
				// Only SELECT queries return a resource that getNumRows can use
				'numRows' => is_resource( $queryResponse ) ? $this->getNumRows( $queryResponse ) : null,
				'insertId' => $this->getInsertId(),
				'affectedRows' => $this->getAffectedRows(),
				'queryTime' => $microtimeEnd - $microtimeStart,
			);
		}
		return $doLog;
	}

	public function getQueryLog() {
		return $this->rawQueryHistory;
	}

	protected function checkEnvironment() {
		if ( !function_exists( 'mysql_connect' ) ) {
			throw new SwarmException( 'MySQL functions missing.' );
		}
	}

	private function __construct() {
		$this->checkEnvironment();
	}
}
