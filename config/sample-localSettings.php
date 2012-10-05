<?php
/**
 * Load TestSwarm settings.
 * @return {mixed} If array, settings will be extended with it.
 *  If something else, it will be silently ignored and logged to error_log.
 */
$settingsFile = __DIR__ . '/localSettings.json';

if ( is_readable( $settingsFile ) ) {
	return json_decode( file_get_contents( $settingsFile ) );
}
