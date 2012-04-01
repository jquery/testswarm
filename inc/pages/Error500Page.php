<?php
/**
 * Error "500" handler.
 *
 * @author Timo Tijhof, 2012
 * @since 0.3.0
 * @package TestSwarm
 */

class Error500Page extends Page {

	protected $exceptionObj;

	protected function initContent() {
		global $swarmInstallDir;

		self::httpStatusHeader( 500 );

		$this->setTitle( "Error 500: " . Page::getHttpStatusMsg( 500 ) );

		$e = $this->exceptionObj;

		$html = '<div class="errorbox">An internal error occurred.'
				. ' The following error message was caught:<br><br><strong>'
				. nl2br( htmlspecialchars( $e->getMessage() ) ) . '</strong></div>';

		if ( $this->getContext()->getConf()->debug->show_exception_details ) {
			$html .=
				'<p>Caught in <b><code>.'
				. htmlspecialchars( substr( $e->getFile(), strlen( $swarmInstallDir ) ) )
				. '</code></b> on line <b><code>' . htmlspecialchars( $e->getLine() )
				. '</code></b>.</p><p>Backtrace:</p><pre>' . nl2br( htmlspecialchars( $e->getTraceAsString() ) )
				. '</pre>';
		} else {
			$html .=
				'<p><strong>To the administrator</strong>:'
				. '<br>Set <b><code>show_exception_details = 1;</code></b> '
				. 'in the <code>[debug]</code> section at the bottom of '
				. '<code>testswarm.ini</code> to show detailed debugging information.</p>';
		}

		return $html;
	}

	public function setExceptionObj( Exception $exceptionObj ) {
		$this->exceptionObj = $exceptionObj;
	}
}
