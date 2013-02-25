<?php
/**
 * Class to create or get info about a client.
 * Should NOT stored in session because one browser can have
 * multiple tabs (=multiple clients) running.
 *
 * @author Timo Tijhof, 2012
 * @since 1.0.0
 * @package TestSwarm
 */
class Client {
	/**
	 * @var $context TestSwarmContext
	 */
	protected $context;

	protected $clientRow;

	/**
	 * @param $clientId int
	 */
	protected function loadFromID( $clientID ) {
		$db = $this->context->getDB();
		$browserInfo = $this->context->getBrowserInfo();

		// Verify that the client exists.
		$clientRow = $db->getRow(str_queryf(
			'SELECT
				*
			FROM
				clients
			WHERE id = %u
			LIMIT 1;',
			$clientID
		));

		if ( !$clientRow || !$clientRow->id ) {
			throw new SwarmException( 'Invalid client ID.' );
		}

		// Although we can't completely prevent fraudulent submissions
		// without switching to a token system, at least verify that the
		// client_id's user_agent matches the User-Agent header that made
		// this request.
		if ( $clientRow->useragent_id != $browserInfo->getSwarmUaID() ) {
			throw new SwarmException( "Your user agent does not match this client's registered user agent." );
		}

		// Save a query by not re-selecting the row, assume success and
		// simulate the same update on our object
		$clientRow->updated = swarmdb_dateformat( SWARM_NOW );

		// Update its record so that we know that it's still alive
		$db->query(str_queryf(
			'UPDATE clients
			SET
				updated = %s
			WHERE id = %u
			LIMIT 1;',
			$clientRow->updated,
			$clientRow->id
		));

		$this->clientRow = $clientRow;
	}

	protected function loadNew() {
		$browserInfo = $this->context->getBrowserInfo();
		$db = $this->context->getDB();
		$request = $this->context->getRequest();


		// If the useragent isn't known, abort with an error message
		if ( !$browserInfo->isInSwarmUaIndex() ) {
			throw new SwarmException( 'Your browser is not needed by this swarm.' );
		}

		$clientName = $request->getVal( 'item', 'anonymous' );
		if ( !$clientName ) {
			// The UI javascript injects a default value and if the field is missing
			// the above WebRequest#getVal fallback catches it. But if the field
			// was submitted with an empty string, then just ignore it and go to anonymous as well.
			// We don't want to hold back potential swarm joiners.
			$clientName = 'anonymous';
		}
		if ( !self::isValidName( $clientName ) ) {
			throw new SwarmException( 'Invalid client name. Names should be no longer than 128 characters.' );
		}

		// Insert in a new record for the client and get its ID
		$db->query(str_queryf(
			'INSERT INTO clients (name, useragent_id, useragent, ip, updated, created)
			VALUES(%s, %s, %s, %s, %s, %s);',
			$clientName,
			$browserInfo->getSwarmUaID(),
			$browserInfo->getRawUA(),
			$request->getIP(),
			swarmdb_dateformat( SWARM_NOW ),
			swarmdb_dateformat( SWARM_NOW )
		));

		$this->clientRow = $db->getRow(str_queryf(
			'SELECT * FROM clients WHERE id = %u LIMIT 1;',
			$db->getInsertId()
		));
	}

	public function getClientRow() {
		return $this->clientRow;
	}

	/**
	 * @param $context TestSwarmContext
	 * @param $runToken string
	 * @param $clientID int: [optional] Instead of creating a new client entry,
	 * create an instance for an existing client entry.
	 */
	public static function newFromContext( TestSwarmContext $context, $runToken, $clientID = null ) {
		self::validateRunToken( $context, $runToken );

		$client = new self();
		$client->context = $context;

		if ( $clientID !== null ) {
			$client->loadFromID( $clientID );
		} else {
			$client->loadNew();
		}
		return $client;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public static function isValidName( $name ) {
		return !!preg_match( '/' . self::getNameValidationRegex() . '/', $name );
	}

	public static function getNameValidationRegex() {
		return '^.{1,128}$';
	}

	public static function validateRunToken( TestSwarmContext $context, $runToken ) {
		$conf = $context->getConf();
		if ( !$conf->client->requireRunToken ) {
			return true;
		}
		$cacheFile = $conf->storage->cacheDir . '/run_token_hash.cache';
		if ( !is_readable( $cacheFile ) ) {
			throw new SwarmException( 'Configuration requires a runToken but none has been configured.' );
		}
		$runTokenHash = trim( file_get_contents( $cacheFile ) );
		if ( $runTokenHash === sha1( $runToken ) ) {
			return true;
		}
		throw new SwarmException( 'This TestSwarm requires a run token. Either none was entered or it is invalid.' );
	}

	public static function getMaxAge( TestSwarmContext $context ) {
		$conf = $context->getConf();
		return time() - ( $conf->client->pingTime + $conf->client->pingTimeMargin );
	}

	/** Don't allow direct instantiations of this class, use newFromContext instead. */
	private function __construct() {}
}
