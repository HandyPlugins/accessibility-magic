<?php
/**
 * Common utilities and functions
 *
 * @package AccessibilityToolkit
 */

namespace AccessibilityToolkit\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use AccessibilityToolkit\Encryption;
use const AccessibilityToolkit\Constants\DEFAULT_SETTINGS;
use const AccessibilityToolkit\Constants\LICENSE_ENDPOINT;
use const AccessibilityToolkit\Constants\LICENSE_INFO_TRANSIENT;
use const AccessibilityToolkit\Constants\LICENSE_KEY_OPTION;
use const AccessibilityToolkit\Constants\SETTING_OPTION;

/**
 * Get settings with defaults
 *
 * @return array
 * @since  1.0
 */
function get_settings() {
	$defaults = DEFAULT_SETTINGS;

	if ( ACCESSIBILITY_TOOLKIT_IS_NETWORK ) {
		$settings = get_site_option( SETTING_OPTION, [] );
	} else {
		$settings = get_option( SETTING_OPTION, [] );
	}

	// Merge settings with defaults, ensuring new additions and nested arrays are included
	$settings = array_replace_recursive( $defaults, $settings );

	return $settings;
}


/**
 * Is plugin activated network wide?
 *
 * @param string $plugin_file file path
 *
 * @return bool
 * @since 1.0
 */
function is_network_wide( $plugin_file ) {
	if ( ! is_multisite() ) {
		return false;
	}

	if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
		require_once ABSPATH . '/wp-admin/includes/plugin.php';
	}

	return is_plugin_active_for_network( plugin_basename( $plugin_file ) );
}


/**
 * Get the documentation url
 *
 * @param string $path     The path of documentation
 * @param string $fragment URL Fragment
 *
 * @return string final URL
 */
function get_doc_url( $path = null, $fragment = '' ) {
	$doc_base       = 'https://handyplugins.co/wp-accessibility-toolkit/docs/';
	$utm_parameters = '?utm_source=wp_admin&utm_medium=plugin&utm_campaign=settings_page';

	if ( ! empty( $path ) ) {
		$doc_base .= ltrim( $path, '/' );
	}

	$doc_url = trailingslashit( $doc_base ) . $utm_parameters;

	if ( ! empty( $fragment ) ) {
		$doc_url .= '#' . $fragment;
	}

	return $doc_url;
}

/**
 * Check weather current screen is accessibility toolkit settings page or not
 *
 * @return bool
 * @since 1.0
 */
function is_accessibility_toolkit_settings_screen() {
	$current_screen = get_current_screen();

	if ( ! is_a( $current_screen, '\WP_Screen' ) ) {
		return false;
	}

	if ( false !== strpos( $current_screen->base, 'accessibility-toolkit' ) ) {
		return true;
	}

	return false;
}

/**
 * Get the supported units
 *
 * @return string[]
 */
function get_supported_units() {
	return [
		'px',
		'%',
		'vh',
		'vw',
		'em',
		'rem',
	];
}

/**
 * Process the icon position
 *
 * @param string $position Position of the icon eg: bottom-right
 * @param int    $offset_x Offset in x direction
 * @param int    $offset_y Offset in y direction
 * @param string $unit     Unit of the offset
 *
 * @return string[]
 */
function process_icon_position( $position, $offset_x, $offset_y, $unit ) {
	// Initialize the icon position array as empty
	$icon = [ 'type' => 'fixed' ];

	// Split the position input into components
	$position_parts = explode( '-', $position );

	// Adjust the array based on the position input and offset values
	foreach ( $position_parts as $part ) {
		switch ( $part ) {
			case 'top':
				$icon[ $part ] = [
					'size'  => intval( $offset_y ),
					'units' => $unit,
				];
				break;
			case 'bottom':
				$icon[ $part ] = [
					'size'  => intval( $offset_y ),
					'units' => $unit,
				];
				break;
			case 'left':
				$icon[ $part ] = [
					'size'  => intval( $offset_x ),
					'units' => $unit,
				];
				break;
			case 'right':
				$icon[ $part ] = [
					'size'  => intval( $offset_x ),
					'units' => $unit,
				];
				break;
		}
	}

	return $icon;
}


/**
 * Mask given string
 *
 * @param string $input_string  String
 * @param int    $unmask_length The length of unmask
 *
 * @return string
 * @since 1.0
 */
function mask_string( $input_string, $unmask_length ) {
	$output_string = substr( $input_string, 0, $unmask_length );

	if ( strlen( $input_string ) > $unmask_length ) {
		$output_string .= str_repeat( '*', strlen( $input_string ) - $unmask_length );
	}

	return $output_string;
}

/**
 * Get the excluded pages
 *
 * @return array
 */
function get_excluded_pages() {
	$settings       = get_settings(); // phpcs:ignore
	$excluded_pages = [];

	if ( ! empty( $settings['excluded_pages'] ) ) {
		$excluded_pages = preg_split( '#(\r\n|\r|\n)#', $settings['excluded_pages'], - 1, PREG_SPLIT_NO_EMPTY );
	}

	return apply_filters( 'accessibility_toolkit_excluded_pages', $excluded_pages );
}


/**
 * Check if the current page is excluded
 *
 * @return bool
 */
function is_excluded_page() {
	$excluded_pages = get_excluded_pages();

	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';

	if ( ! empty( $excluded_pages ) ) {
		foreach ( $excluded_pages as $exception ) {
			if ( preg_match( '#^[\s]*$#', $exception ) ) {
				continue;
			}

			// full url exception
			if ( preg_match( '#^https?://#', $exception ) ) {
				$exception = wp_parse_url( $exception, PHP_URL_PATH );
			}

			if ( empty( $exception ) ) {
				continue;
			}

			if ( preg_match( '#^(' . preg_quote( $exception, '#' ) . ')#', $request_uri ) ) {
				return true;
			}
		}
	}

	return false;
}
