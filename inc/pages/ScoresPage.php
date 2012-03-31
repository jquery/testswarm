<?php
/**
 * "Scores" page.
 *
 * @since 0.1.0
 * @package TestSwarm
 */

// TODO need to test the table with actual runs in the database
class ScoresPage extends Page {

	protected function initContent() {
		$db = $this->getContext()->getDB();

		$this->setTitle( "Scores" );

		$html = '<blockquote>All users with a score greater than zero. The score is the number of tests run by that user\'s clients.</blockquote>';

		$result = $db->getRows(
			"SELECT
				users.name as user_name,
				SUM(total) as alltotal
			FROM
				clients, run_client, users
			WHERE	clients.id=run_client.client_id
			AND	clients.user_id=users.id
			GROUP BY user_id
			HAVING alltotal > 0
			ORDER by alltotal DESC;"
		);

		$num = 1;

		if ($result) {
			$html .= '<table class="scores">';
			foreach ( $result as $row ) {
				$user  = $row->user_name;
				$total = $row->alltotal;

				$html .= '<tr><td class="num">' . $num. '</td>'
					. '<td><a href="' . swarmpath("user/$user/") . '">' . $user . '</a></td>'
					. '<td class="num">' . $total . '</td></tr>';
				$num++;
			}

			$html .= '</table>';
		}

		return $html;
	}

}
