<?php
/**
 * Page interface for ClientsAction.
 *
 * @author John Resig
 * @author Jörn Zaefferer
 * @author Timo Tijhof
 * @since 0.1.0
 * @package TestSwarm
 */
class ClientsPage extends Page {

	public function execute() {
		$action = ClientsAction::newFromContext( $this->getContext() );
		$action->doAction();

		$this->setAction( $action );
		$this->content = $this->initContent();
	}

	protected function initContent() {
		$context = $this->getContext();
		$request = $context->getRequest();

		$this->setTitle( 'Clients' );
		$this->setRobots( 'index,nofollow' );
		$html = '';

		$error = $this->getAction()->getError();
		$data = $this->getAction()->getData();

		$params = $data['normalParams'];
		/* @var string|null $item */
		$item = $params['item'];
		$mode = isset( $params['mode'] ) ? $params['mode'] : 'clients';

		if ( $error ) {
			$html .= html_tag( 'div', array( 'class' => 'alert alert-error' ), $error['info'] );
			return $html;
		}

		if ( $item ) {
			$this->setSubTitle( $item );
			$nav = '';
		} else {
			$nav = '<div class="form-actions">';
			$nav .= '<div class="btn-group pull-right">';
			if ( $mode === 'clients' ) {
				$nav .= '<button class="btn active ">Clients <i class="icon-th-list"></i></button>'
				. '<a class="btn" href="' . htmlspecialchars(
					$this->getDerivQuery( $params, array( 'mode' => 'names' ) )
				)
				. '">Names <i class="icon-list-alt"></i></a>';
			} else {
				$nav .= '<a class="btn" href="' . htmlspecialchars(
					$this->getDerivQuery( $params, array( 'mode' => 'clients' ) )
				)
				. '">Clients <i class="icon-th-list"></i></a>'
				. '<button class="btn active">Names <i class="icon-list-alt"></i></button>';
			}
			$nav .= '</div>';
			$nav .= '<div class="btn-group pull-right">xx';
			// TODO: active | all | inactive
			$nav .= '</div></div>';
		}

		$html .= $nav;

		if ( $item || $mode === 'clients' ) {
			if ( !count( $data['clients'] ) ) {
				$html .= '<div class="alert alert-info">No active clients found.</div>';
			} else {
				$html .= $this->showDetails( $data );
			}
		} else {
			if ( !count( $data['overview'] ) ) {
				$html .= '<div class="alert alert-info">No clients found.</div>';
			} else {
				$html .= $this->showOverview( $data );
			}
		}

		return $html;
	}

	/**
	 * @param array<string,string|null> $params
	 * @param array<string,string> $extra
	 * @return string
	 */
	private function getDerivQuery( array $params, array $extra ) {
		// This automatically excludes keys with null value
		return '?' . http_build_query( $extra + $params );
	}

	/**
	 * @param array $data Overview data from ClientsAction
	 * @return string HTML
	 */
	protected function showOverview( $data ) {
		$context = $this->getContext();
		$request = $context->getRequest();

		$overview = $data['overview'];
		$clients = $data['clients'];

		$params = $data['normalParams'];
		$sortField = isset( $params['sort'] ) ? $params['sort'] : 'name';
		$sortDir = isset( $params['sort_dir'] ) ? $params['sort_dir'] : 'asc';

		$navigationSort = array();
		foreach ( array( 'name', 'updated' ) as $field ) {
			$navigationSort[$field] = array(
				'toggleQuery' => array(
					'sort' => $field,
					'sort_dir' => $sortDir === 'asc' ? 'desc' : null,
				),
				'arrowHtml' => $sortField !== $field
					? '<b class="swarm-arrow-muted">'
					: (
						$sortDir === 'asc'
							? '<b class="swarm-arrow-up">'
							: '<b class="swarm-arrow-down">'
					)
			);
		}

		$html = '<table class="table table-striped">'
		 . '<thead><tr>'
		 . '<th class="swarm-toggle" data-href="' . htmlspecialchars( $this->getDerivQuery( $params, $navigationSort['name']['toggleQuery'] ) ) . '">User ' . $navigationSort['name']['arrowHtml'] . '</b></th>'
		 . '<th>Clients</th>'
		 . '<th class="span4 swarm-toggle" data-href="' . htmlspecialchars( $this->getDerivQuery( $params, $navigationSort['updated']['toggleQuery'] ) ) . '">Last ping ' . $navigationSort['updated']['arrowHtml'] . '</b></th>'
		 . '</tr></thead>'
		 . '<tbody>';

		foreach ( $overview as $item ) {
			$clientsHtml = '';
			if ( !count( $item['clientIDs'] ) ) {
				$clientsHtml .= '<span class="muted">N/A</span>';
			} else {
				foreach ( $item['clientIDs'] as $clientID ) {
					$clientsHtml .= html_tag_open( 'a', array( 'href' => $clients[$clientID]['viewUrl'] ) )
					. BrowserInfo::buildIconHtml( $clients[$clientID]['uaData']['displayInfo'], array(
						'size' => 'small',
						'wrap' => false,
					) )
					. '</a>';
				}
			}
			$html .= '<tr>'
				. '<td><a href="' . htmlspecialchars( $item['viewUrl'] ) . '" title="View ' . htmlspecialchars( $item['name'] ) . ' clients">' . htmlspecialchars( $item['name'] ) . '</a></td>'
				. '<td>' . $clientsHtml . '</td>'
				. '<td>' . $this->getPrettyDateHtml( $item, 'updated' ) . ' </td>'
				. '</tr>';
		}
		$html .= '</tbody></table>';

		return $html;

	}

	/**
	 * @param array $data Details data from ClientsAction
	 * @return string HTML
	 */
	protected function showDetails( $data ) {
		$html = '<div class="row">';
		foreach ( $data['clients'] as $client ) {
			$displayInfo = $client['uaData']['displayInfo'];
			$html .=
				'<div class="span4 swarm-client"><div class="well">'
				. '<div class="swarm-client-icon">' . BrowserInfo::buildIconHtml( $displayInfo, array( 'wrap' => false ) ) . '</div>'
				. '<div class="swarm-client-info">'
				. '<p class="swarm-client-title">' . htmlspecialchars( $displayInfo['title'] ) . '</p>'
				. '<table class="table table-condensed">'
				. '<tbody>'
				. '<tr><th>Last ping</th><td>' . self::getPrettyDateHtml( $client, 'pinged' ) . '</td></tr>'
				. '<tr><th>Run</th>' . (
					!$client['lastResult']
						? '<td><span class="muted">Waiting for runs...</span></td>'
						: (
							'<td class="swarm-status-' . $client['lastResult']['status'] . '">'
							. html_tag_open( 'a', array( 'href' => $client['lastResult']['viewUrl'] ) )
							. htmlspecialchars( "#{$client['lastResult']['id']}" )
							. ' ' . JobPage::getStatusIconHtml( $client['lastResult']['status'] )
							. '</a></td>'
						)
				) . '</tr>'
				. '<tr><th>Connected</th><td>' . self::getPrettyDateHtml( $client, 'connected' ) . '</td></tr>'
				. '</tbody>'
				. '</table>'
				. '</div>'
				. '<div class="clearfix"><a href="' . htmlspecialchars( $client['viewUrl'] ) . '" class="pull-right">Details &raquo;</a></div>'
				. '</div></div>';
		}
		return $html . '</div>';
	}

}
