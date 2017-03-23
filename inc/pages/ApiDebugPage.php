<?php
/**
 * Debug page for API actions.
 *
 * @author Timo Tijhof
 * @since 1.0.0
 * @package TestSwarm
 */
class ApiDebugPage extends Page {

	protected $apiAction = '';
	protected $apiResponse;

	protected function initContent() {
		if ( !defined( 'SWARM_ENTRY' ) || SWARM_ENTRY !== 'API' ) {
			echo "This page is not viewable outside the scope of the API.\n";
			exit;
		}

		$this->setTitle( "API Response" );
		$this->setRobots( "noindex,nofollow" );

		$html = "";
		$html .= "<h2>action=" . htmlspecialchars( $this->apiAction ) . "</h2>";

		if ( $this->apiAction === 'apihelp' ) {
			$html .= "<h4>Available actions</h4>";
			$actions = $this->apiResponse['apihelp']['action'];
			$html .= '<div class="swarm-columns"><ul>';
			foreach ( $actions as $action ) {
				$html .= '<li><a href="?action=' . htmlspecialchars( urlencode( $action ) ) . '">'
					. $action . '</a></li>';
			}
			$html .= '</ul></div>';

			$html .= "</ul><h4>Available formats</h4>";
			$formats = $this->apiResponse['apihelp']['format'];
			$html .= '<ul>';
			foreach ( $formats as $format ) {
				$html .= '<li><code><a href="?format=' . htmlspecialchars( urlencode( $format ) )
					. '">' . htmlspecialchars( $format ) . '</a></code></li>';
			}
			$html .= '</ul>';

			$html .= "<h4>Response</h4>";
			$html .= '<pre>';
			$html .= htmlspecialchars( json_encode( $this->apiResponse, JSON_PRETTY_PRINT ) );
			$html .= '</pre>';
		} else {
			$html .= "<h4>Request <small><code>wasPosted: " . (
				$this->getContext()->getRequest()->wasPosted()
					? "true"
					: "false"
			) . "</code></small></h4>";
			$html .= "<pre>";

			ob_start();
			var_dump( $_POST + $_GET );
			$html .= htmlspecialchars( ob_get_contents() );
			ob_end_clean();

			$html .= "</pre>";

			$html .= "<h4>Response</h4>";
			$html .= "<pre>";

			ob_start();
			var_dump( $this->apiResponse );
			$html .= htmlspecialchars( ob_get_contents() );
			ob_end_clean();

			$html .= "</pre>";
		}

		return $html;
	}

	public function setActionName( $actionName ) {
		$this->apiAction = $actionName;
	}

	public function setApiResponse( Array $apiResponse ) {
		$this->apiResponse = $apiResponse;
	}
}
