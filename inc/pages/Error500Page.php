<?php
/**
 * Error "500" handler.
 * No database queries should be made from this class, because when
 * the database is locked it will throw an exception that leads here.
 *
 * @author Timo Tijhof, 2012
 * @since 1.0.0
 * @package TestSwarm
 */
class Error500Page extends Page {

	protected $exceptionObj;

	protected function initContent() {
		global $swarmInstallDir;

		self::httpStatusHeader( 500 );

		$this->setTitle( Page::getHttpStatusMsg( 500 ) );
		$this->setRobots( "noindex,nofollow" );

		$e = $this->exceptionObj;

		$html = '<div class="alert alert-error">An internal error occurred.'
				. ' The following error message was caught:<br><br><strong>'
				. nl2br( htmlspecialchars( $e->getMessage() ) ) . '</strong></div>';

		if ( $this->getContext()->getConf()->debug->showExceptionDetails ) {
			$html .=
				'<p>Caught in <code>.'
				. htmlspecialchars( substr( $e->getFile(), strlen( $swarmInstallDir ) ) )
				. '</code> on line <code>' . htmlspecialchars( $e->getLine() )
				. '</code>.</p><p>Backtrace:</p><pre>' . nl2br( htmlspecialchars( $e->getTraceAsString() ) )
				. '</pre>';
		} else {
			$html .=
				'<p><small><strong>To the administrator</strong>:</small>'
				. ' <br><small>To show detailed debugging information, set'
				. ' <tt>"showExceptionDetails": true</tt> in the <tt>"debug"</tt> section'
				. ' of the configuration file.</small></p>';
		}

		return $html;
	}

	public function setExceptionObj( Exception $exceptionObj ) {
		$this->exceptionObj = $exceptionObj;
	}
}
