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

		$this->setTitle( Page::getHttpStatusMsg( 500 ) );

		$e = $this->exceptionObj;

		$html = '<div class="alert alert-error">An internal error occurred.'
				. ' The following error message was caught:<br><br><strong>'
				. nl2br( htmlspecialchars( $e->getMessage() ) ) . '</strong></div>';

		if ( $this->getContext()->getConf()->debug->show_exception_details ) {
			$html .=
				'<p>Caught in <code>.'
				. htmlspecialchars( substr( $e->getFile(), strlen( $swarmInstallDir ) ) )
				. '</code> on line <code>' . htmlspecialchars( $e->getLine() )
				. '</code>.</p><p>Backtrace:</p><pre>' . nl2br( htmlspecialchars( $e->getTraceAsString() ) )
				. '</pre>';
		} else {
			$html .=
				'<p><small><strong>To the administrator</strong>:</small>'
				. '<br><small>Set <tt>show_exception_details = 1;</tt> '
				. 'in the <tt>[debug]</tt> section at the bottom of '
				. '<tt>testswarm.ini</tt> to show detailed debugging information.</small></p>';
		}

		return $html;
	}

	public function setExceptionObj( Exception $exceptionObj ) {
		$this->exceptionObj = $exceptionObj;
	}
}
