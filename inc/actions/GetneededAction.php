<?php
/**
 * "Getneeded" action.
 *
 * @author Jörn Zaefferer, 2012
 * @since 0.3.0
 * @package TestSwarm
 */

class GetneededAction extends Action {

	public function doAction() {
		$db = $this->getContext()->getDB();

		$result = $db->getRows(
			"SELECT DISTINCT
				useragent_id
			FROM
				run_useragent
			WHERE
				runs < 1
			AND status = 0;"
		);

		$useragent_ids = array();
		foreach( $result as $useragent ) {
			array_push($useragent_ids, (int)$useragent->useragent_id);
		}

		$this->setData( $useragent_ids );
	}
}
