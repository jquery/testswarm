<?php
	$title = "Scores";
	
	define(SCORES_PAGINATION_WINDOW, 5); // this should be an odd number, probably prime
	
	define(SCORES_TRUNCATE_QUERY, "
		TRUNCATE TABLE scores
	");
	
	define(SCORES_REBUILD_QUERY, "
		INSERT INTO scores ( name, score )
		SELECT
			users.name,
			SUM(total) as alltotal
		FROM
			clients,
			run_client,
			users
		WHERE clients.id=run_client.client_id AND clients.user_id=users.id
		GROUP BY user_id
		HAVING alltotal > 0
	");
	
	define(SCORES_COUNT_QUERY, "
		SELECT count(*) FROM scores
	");
	
	$web_config = getItem('web', $config, Array());
	$names_per_page = $default_per_page = intval( getItem('scores.names_per_page', $web_config, 50) );
	
	$CLEAN["start"] = intval( getItem('start', $_REQUEST, 1) );
	$CLEAN["offset"] = $CLEAN['start'] - 1; // the names are numbered starting at one
	$CLEAN["end"] = intval( getItem('end', $_REQUEST, $CLEAN["offset"] + $default_per_page ) );
	
	$names_per_page = $CLEAN['end'] - $CLEAN["offset"];
	
	switch($_REQUEST["cmd"]) {
		case "rebuild":
			mysql_queryf(SCORES_TRUNCATE_QUERY);
			mysql_queryf(SCORES_REBUILD_QUERY);
			break;
		default:
	}
	
	function windowed_offset($offset, $per_page) {
		$rows_result = mysql_queryf(SCORES_COUNT_QUERY);

		$row = mysql_fetch_row($rows_result);
		$total = intval($row[0]);
		
		$pagi_offset = $offset - ($per_page * (SCORES_PAGINATION_WINDOW - 1)) / 2;
		$pagi_limit = $per_page * SCORES_PAGINATION_WINDOW;
		
		if($pagi_limit > $total) {
			$pagi_limit = $total;
		}

		if($pagi_offset + $pagi_limit > $total) {
			$pagi_offset = $total - $pagi_limit;
		}

		if($pagi_offset <= 0) {
			$pagi_offset = 0;
		}
		
		return array(
			"offset" => floor($pagi_offset),
			"limit" => $pagi_limit
		);
	}
	
	function pagination ($current_start, $current_end, $offset, $limit, $items_per) {
		$output = array();
		
		$pages = ceil($limit / $items_per);

		$start = $current_start - $items_per;
		$end = $current_start - 1;
		$class = "previous";
		
		if($current_start < $items_per) {
			$start = 1;
			$end = $items_per;
			$class .= " disabled";
		}
		
		array_push($output, array(
			"href" => swarmpath("scores/$start-$end/"),
			"class" => $class,
			"textContent" => "&lsaquo;"
		));
		
		for($i = 0; $i < $pages; $i++) {
			$start = $offset + ($i * $items_per) + 1;
			$end = $start + $items_per - 1;
			$class = "";
			
			if($start == $current_start && $end == $current_end) {
				$class .= "current";
			}
			
			array_push($output, array(
				"href" => swarmpath("scores/$start-$end/"),
				"class" => $class,
				"textContent" => ceil($start / $items_per)
			));

		}
		
		$start = $current_end + 1;
		$end = $current_end + $items_per;
		$class = "next";
		
		if($start >= $offset + $limit) {
			$start = ($pages - 1) * $items_per + 1;
			$end = $start + $items_per - 1;
			$class .= " disabled";
		}
	
		array_push($output, array(
			"href" => swarmpath("scores/$start-$end/"),
			"class" => $class,
			"textContent" => "&rsaquo;"
		));
		
		return $output;
	}
?>
