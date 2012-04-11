<?php
/**
 * Api Debug handler.
 *
 * @author Timo Tijhof, 2012
 * @since 0.3.0
 * @package TestSwarm
 */

class ApiDebugPage extends Page {

	protected $apiResponse;

	protected function initContent() {

		if ( !defined( 'SWARM_ENTRY' ) || SWARM_ENTRY !== 'API' ) {
			echo "This page is not viewable outside the scope of the API.\n";
			exit;
		}

		$this->setTitle( "API Response" );
		$this->setRobots( "noindex,nofollow" );

		$html = "";

		$html .= "<h3>Request <small><code>wasPosted: " . (
			$this->getContext()->getRequest()->wasPosted()
				? "true"
				: "false"
		) . "</code></small></h3>";
		$html .= "<pre>";

		ob_start();
		var_dump( $_POST + $_GET );
		$html .= htmlspecialchars( ob_get_contents() );
		ob_end_clean();

		$html .= "</pre>";

		$html .= "<h3>Response</h3>";
		$html .= "<pre>";

		ob_start();
		var_dump( $this->apiResponse );
		$html .= htmlspecialchars( ob_get_contents() );
		ob_end_clean();

		$html .= "</pre>";

		return $html;
	}

	public function setApiResponse( Array $apiResponse ) {
		$this->apiResponse = $apiResponse;
	}
}
