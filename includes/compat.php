<?php
/**
 * Plugin compatibility with 3rd party plugins/themes
 *
 * @package AccessibilityMagic
 */

namespace AccessibilityMagic\Compat;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Default setup routine
 *
 * @return void
 */
function setup() {
	add_filter( 'accessibility_toolkit_show_frontend', __NAMESPACE__ . '\\maybe_show_frontend' );
	add_filter( 'powered_cache_delayed_js_skip', __NAMESPACE__ . '\\skip_powered_cache_delay_js', 10, 2 );
}

/**
 * Maybe show frontend based on the status
 *
 * @param bool $status Current status
 *
 * @return bool
 */
function maybe_show_frontend( $status ) {
	// Disable on Divi Builder
	if ( isset( $_GET['et_fb'] ) ) { // phpcs:ignore
		return false;
	}

	return $status;
}

/**
 * Skip powered cache delay
 *
 * @param bool   $is_delay_skipped whether to skip delay or not
 * @param string $script           script tag
 *
 * @return mixed|true
 */
function skip_powered_cache_delay_js( $is_delay_skipped, $script ) {
	if ( false !== stripos( $script, 'accessibility_toolkit_frontend' ) ) {
		return true;
	}

	return $is_delay_skipped;
}
