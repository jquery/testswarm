<?php
/**
 * Database abstraction layer for MySQL.
 *
 * Some methods are based on:
 * - https://github.com/wikimedia/mediawiki/blob/1.27.0/includes/db/Database.php
 * - https://github.com/wikimedia/mediawiki/blob/1.27.0/includes/db/DatabaseMysqlBase.php
 * - https://github.com/wikimedia/mediawiki/blob/1.27.0/includes/db/DatabaseMysqli.php
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
	 */

	public static function newFromContext( TestSwarmContext $context ) {
		$dbConf = $context->getConf()->database;
		$db = new self();

		$db->context = $context;
		$db->host = $dbConf->host;
		$db->username = $dbConf->username;
		$db->password = $dbConf->password;
		$db->dbname = $dbConf->database;

		$db->open();

		return $db;
	}

	final protected function getContext() {
		return $this->context;
	}

	/** @return string: current DB name */
	public function getDBname() {
		return $this->dbname;
	}

	public function open() {
		$this->close();

		$this->conn = mysqli_connect( $this->host, $this->username, $this->password, $this->dbname );
		if ( !$this->conn ) {
			throw new SwarmException( "Connection to {$this->host} failed.\nMySQL Error " . $this->lastErrNo() . ': ' . $this->lastErrMsg() );
		}
		if ( method_exists( $this->conn, 'set_charset' ) ) {
			if ( !$this->conn->set_charset( 'binary' ) ) {
				throw new SwarmException( 'Error setting character set on MySQL connection' );
			}
		}

		$this->isOpen = true;
		return $this;
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
		return mysqli_close( $this->conn );
	}

	/** @return mixed|false */
	public function getOne( $sql ) {
		$res = $this->doQuery( $sql );
		if ( $res && $this->getNumRows( $res ) ) {
			$row = mysqli_fetch_array( $res );
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
	 * @return bool|object: see also http://php.net/mysqli-result.fetch-field-direct
	 */
	public function fieldInfo( $table, $fieldName ) {
		$table = $this->addIdentifierQuotes( $table );

		$prev = $this->ignoreErrors( true );
		$response = $this->doQuery( "SELECT * FROM $table LIMIT 1;" );
		$this->ignoreErrors( $prev );

		if ( !$response ) {
			return false;
		}

		$n = intval( $response->field_count );
		for ( $i = 0; $i < $n; $i += 1 ) {
			$fieldInfoObj = $response->fetch_field_direct( $i );
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
		return intval( $res->num_rows );
	}

	/** @return int */
	public function getInsertId() {
		return intval( $this->conn->insert_id );
	}

	/** @return int */
	public function getAffectedRows() {
		return intval( $this->conn->affected_rows );
	}

	public function lastErrNo() {
		return $this->conn ? mysqli_errno( $this->conn ) : mysqli_connect_errno();
	}

	public function lastErrMsg() {
		return $this->conn ? mysqli_error( $this->conn ) : mysqli_connect_error();
	}

	public function strEncode( $str ) {
		return $this->conn->real_escape_string( $str );
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
		$queryResponse = $this->conn->query( $sql );

		if ( !$this->ignoreErrors && ( $queryResponse === false || $this->lastErrNo() ) ) {
			throw new SwarmException( 'Error in doQuery: ' . $this->lastErrMsg() );
		}

		$this->logQuery( $sql, $queryResponse, $microtimeStart );

		return $queryResponse;
	}

	protected function fetchObject( $res ) {
		$obj = $res->fetch_object();
		if ( !$this->ignoreErrors && $this->lastErrNo() ) {
			throw new SwarmException( 'Error in fetchObject: ' . $this->lastErrMsg() );
		}
		return $obj;
	}

	public function freeResult( $res ) {
		$res->free_result( $res );
		return true;
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
		if ( !function_exists( 'mysqli_connect' ) ) {
			throw new SwarmException( 'MySQL functions missing.' );
		}
	}

	private function __construct() {
		$this->checkEnvironment();
	}
}
