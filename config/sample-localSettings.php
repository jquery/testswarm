<?php
/**
 * Load TestSwarm settings.
 * @return {mixed} If array, settings will be extended with it.
 *  If something else, it will be silently ignored and logged to error_log.
 */
$settingsFile = __DIR__ . '/localSettings.json';
$settings = is_readable( $settingsFile ) ? json_decode( file_get_contents( $settingsFile ) ) : null;
if ( !$settings ) {
	error_log( 'TestSwarm Warning: Unable to parse localSettings.json.' );
}
return $settings;
