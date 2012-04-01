<?php
/**
 * Class to create or get info about a client.
 * Should NOT stored in session because one browser can have
 * multiple tabs (=multiple clients) running.
 *
 * @author Timo Tijhof, 2012
 * @since 0.3.0
 * @package TestSwarm
 */
class Client {
	/**
	 * @var $context TestSwarmContext
	 */
	protected $context;

	protected $clientRow;
	protected $userRow;

	/**
	 * @param $clientId int
	 */
	protected function loadFromID( $clientID ) {
		$db = $this->context->getDB();

		// Verify that the client exists.
		$clientRow = $db->getRow(str_queryf(
			"SELECT
				*
			FROM
				clients
			WHERE id=%u
			LIMIT 1;",
			$clientID
		));

		if ( !$clientRow || !$clientRow->id ) {
			throw new SwarmException( "Invalid client ID." );
		}

		// Update its record so that we know that it's still alive
		$db->query(str_queryf(
			"UPDATE clients SET updated=%s WHERE id=%u LIMIT 1;",
			swarmdb_dateformat( SWARM_NOW ),
			$clientRow->id
		));
		// Don't re-query the row, assume success and
		// simulate the same update on our object
		$clientRow->updated = swarmdb_dateformat( SWARM_NOW );

		$userRow = $db->getRow(str_queryf(
			"SELECT
				*
			FROM
				users
			WHERE id=%u
			LIMIT 1;",
			$clientRow->user_id
		));

		$this->clientRow = $clientRow;
		$this->userRow = $userRow;
	}

	protected function loadNew() {
		$browserInfo = $this->context->getBrowserInfo();
		$db = $this->context->getDB();
		$request = $this->context->getRequest();
	

		// If the useragent isn't known, abort with an error message
		if ( !$browserInfo->isKnownInTestSwarm() ) {
			throw new SwarmException( "Your browser is not suported in this TestSwarm "
				. "(browser: {$browserInfo->getBrowserCodename()}; version: {$browserInfo->getBrowserVersion()})." );
		}

		// Running a client doesn't require being logged in
		$username = $request->getSessionData( "username", $request->getVal( "item" ) );
		if ( !$username ) {
			throw new SwarmException( "Username required." );
		}

		// Figure out what the user's ID number is
		$userRow = $db->getRow(str_queryf( "SELECT * FROM users WHERE name=%s LIMIT 1;", $username ));

		// If the user doesn't have one, create a new user row for this name
		if ( !$userRow || !$userRow->id ) {
			$db->query(str_queryf(
				"INSERT INTO users (name, created, updated, seed) VALUES(%s, %s, %s, RAND());",
				$username,
				swarmdb_dateformat( SWARM_NOW ),
				swarmdb_dateformat( SWARM_NOW )
			));
			$userRow = $db->getRow(str_queryf( "SELECT * FROM users WHERE id=%s LIMIT 1;", $db->getInsertId() ));
		}

		// Insert in a new record for the client and get its ID
		$db->query(str_queryf(
			"INSERT INTO clients (user_id, useragent_id, useragent, os, ip, created)
			VALUES(%u, %u, %s, %s, %s, %s);",
			$userRow->id,
			$browserInfo->getSwarmUserAgentID(),
			$browserInfo->getRawUA(),
			$browserInfo->getOsCodename(),
			$request->getIP(),
			swarmdb_dateformat( SWARM_NOW )
		));

		$this->clientRow = $db->getRow(str_queryf( "SELECT * FROM clients WHERE id=%s LIMIT 1;", $db->getInsertId() ));
		$this->userRow = $userRow;
	}

	public function getClientRow() {
		return $this->clientRow;
	}

	public function getUserRow() {
		return $this->userRow;
	}

	/**
	 * @param $context TestSwarmContext
	 * @param $clientID int: [optional] Instead of creating a new client entry,
	 * create an instance for an existing client entry.
	 */
	public static function newFromContext( TestSwarmContext $context, $clientID = null ) {
		$client = new self();
		$client->context = $context;

		if ( $clientID !== null ) {
			$client->loadFromID( $clientID );
		} else {
			$client->loadNew();
		}

		return $client;
	}

	/** Don't allow direct instantiations of this class, use newFromContext instead. */
	private function __construct() {}
}
