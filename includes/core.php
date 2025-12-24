<?php
/**
 * Core plugin functionality.
 *
 * @package AccessibilityToolkit
 */

namespace AccessibilityToolkit\Core;

use function AccessibilityToolkit\Utils\is_accessibility_toolkit_settings_screen;
use \WP_Error as WP_Error;
use function AccessibilityToolkit\Utils\is_excluded_page;
use function AccessibilityToolkit\Utils\process_icon_position;

/**
 * Default setup routine
 *
 * @return void
 */
function setup() {
	add_action( 'init', __NAMESPACE__ . '\\init' );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\admin_scripts' );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\admin_styles' );
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_scripts' );

	do_action( 'accessibility_toolkit_loaded' );
}

/**
 * Initializes the plugin and fires an action other plugins can hook into.
 *
 * @return void
 */
function init() {
	do_action( 'accessibility_toolkit_init' );
}

/**
 * The list of knows contexts for enqueuing scripts/styles.
 *
 * @return array
 */
function get_enqueue_contexts() {
	return [ 'admin', 'frontend', 'shared', 'shortcode', 'block-editor' ];
}

/**
 * Generate an URL to a script, taking into account whether SCRIPT_DEBUG is enabled.
 *
 * @param string $script  Script file name (no .js extension)
 * @param string $context Context for the script ('admin', 'frontend', or 'shared')
 *
 * @return string|WP_Error URL
 */
function script_url( $script, $context ) {

	if ( ! in_array( $context, get_enqueue_contexts(), true ) ) {
		return new WP_Error( 'invalid_enqueue_context', 'Invalid $context specified in AccessibilityToolkit script loader.' );
	}

	return ACCESSIBILITY_TOOLKIT_URL . "dist/js/{$script}.js";

}

/**
 * Generate an URL to a stylesheet, taking into account whether SCRIPT_DEBUG is enabled.
 *
 * @param string $stylesheet Stylesheet file name (no .css extension)
 * @param string $context    Context for the script ('admin', 'frontend', or 'shared')
 *
 * @return string URL
 */
function style_url( $stylesheet, $context ) {

	if ( ! in_array( $context, get_enqueue_contexts(), true ) ) {
		return new WP_Error( 'invalid_enqueue_context', 'Invalid $context specified in AccessibilityToolkit stylesheet loader.' );
	}

	return ACCESSIBILITY_TOOLKIT_URL . "dist/css/{$stylesheet}.css";

}


/**
 * Enqueue scripts for admin.
 *
 * @return void
 */
function admin_scripts() {
	if ( ! is_accessibility_toolkit_settings_screen() ) {
		return;
	}

	wp_enqueue_script(
		'accessibility_toolkit_admin',
		script_url( 'admin', 'admin' ),
		[ 'wp-color-picker' ],
		ACCESSIBILITY_TOOLKIT_VERSION,
		true
	);
}

/**
 * Enqueue styles for admin.
 *
 * @return void
 */
function admin_styles() {

	if ( ! is_accessibility_toolkit_settings_screen() ) {
		return;
	}

	wp_enqueue_style(
		'accessibility_toolkit_admin',
		style_url( 'admin-style', 'admin' ),
		[
			'wp-color-picker',
		],
		ACCESSIBILITY_TOOLKIT_VERSION
	);

}

/**
 * Enqueue scripts for frontend.
 *
 * @return void
 */
function enqueue_scripts() {
	$settings = \AccessibilityToolkit\Utils\get_settings();

	if ( is_excluded_page() ) {
		return;
	}

	/**
	 * Filter show or hide accessibility toolkit from frontend.
	 *
	 * @hook   accessibility_toolkit_show_frontend
	 *
	 * @param  {boolean} $show_frontend Whether to show the frontend script. Default is true.
	 *
	 * @return {boolean} Whether to show the frontend script. Default is true.
	 * @since  1.0
	 */
	$show_frontend = apply_filters( 'accessibility_toolkit_show_frontend', true );

	if ( ! $show_frontend ) {
		return;
	}

	wp_enqueue_script(
		'accessibility_toolkit_frontend',
		script_url( 'frontend', 'frontend' ),
		[],
		ACCESSIBILITY_TOOLKIT_VERSION,
		[
			'strategy'  => 'defer',
			'in_footer' => true,
		]
	);

	$js_options = [
		'labels'   => [
			'resetTitle'          => esc_html__( 'Reset', 'accessibility-toolkit' ),
			'closeTitle'          => esc_html__( 'Close', 'accessibility-toolkit' ),
			'menuTitle'           => esc_html__( 'Accessibility Options', 'accessibility-toolkit' ),
			'increaseText'        => esc_html__( 'Increase Text Size', 'accessibility-toolkit' ),
			'decreaseText'        => esc_html__( 'Decrease Text Size', 'accessibility-toolkit' ),
			'increaseTextSpacing' => esc_html__( 'Increase Text Spacing', 'accessibility-toolkit' ),
			'decreaseTextSpacing' => esc_html__( 'Decrease Text Spacing', 'accessibility-toolkit' ),
			'increaseLineHeight'  => esc_html__( 'Increase Line Height', 'accessibility-toolkit' ),
			'decreaseLineHeight'  => esc_html__( 'Decrease Line Height', 'accessibility-toolkit' ),
			'invertColors'        => esc_html__( 'Invert Colors', 'accessibility-toolkit' ),
			'grayHues'            => esc_html__( 'Gray Hues', 'accessibility-toolkit' ),
			'underlineLinks'      => esc_html__( 'Underline Links', 'accessibility-toolkit' ),
			'bigCursor'           => esc_html__( 'Big Cursor', 'accessibility-toolkit' ),
			'readingGuide'        => esc_html__( 'Reading Guide', 'accessibility-toolkit' ),
			'textToSpeech'        => esc_html__( 'Text to Speech', 'accessibility-toolkit' ),
			'speechToText'        => esc_html__( 'Speech to Text', 'accessibility-toolkit' ),
			'disableAnimations'   => esc_html__( 'Disable Animations', 'accessibility-toolkit' ),
		],
		'modules'  => [
			'increaseText'        => $settings['enable_text_size'],
			'decreaseText'        => $settings['enable_text_size'],
			'increaseTextSpacing' => $settings['enable_text_spacing'],
			'decreaseTextSpacing' => $settings['enable_text_spacing'],
			'increaseLineHeight'  => $settings['enable_line_height'],
			'decreaseLineHeight'  => $settings['enable_line_height'],
			'invertColors'        => false,
			'grayHues'            => $settings['enable_gray_hues'],
			'underlineLinks'      => $settings['enable_underline_links'],
			'bigCursor'           => $settings['enable_big_cursor'],
			'readingGuide'        => $settings['enable_reading_guide'],
			'textToSpeech'        => false,
			'speechToText'        => false, // not supported in an efficient way
			'disableAnimations'   => false,
		],
		'icon'     => [
			'circular'        => $settings['icon']['circular'],
			'position'        => process_icon_position(
				$settings['icon']['position'],
				$settings['icon']['offset_x'],
				$settings['icon']['offset_y'],
				$settings['icon']['offset_unit']
			),
			'fontFaceSrc'     => [ ACCESSIBILITY_TOOLKIT_URL . 'dist/css/material-icons.css' ],
			'dimensions'      => [
				'width'  => [
					'size'  => $settings['icon']['dimension_w'],
					'units' => $settings['icon']['dimension_unit'],
				],
				'height' => [
					'size'  => $settings['icon']['dimension_h'],
					'units' => $settings['icon']['dimension_unit'],
				],
			],
			'backgroundColor' => $settings['icon']['background_color'],
			'color'           => $settings['icon']['color'],
		],
		'language' => [
			'textToSpeechMessages' => [
				'Screen Reader enabled. Reading Pace - Normal' => esc_html__( 'Screen Reader enabled. Reading Pace - Normal', 'accessibility-toolkit' ),
				'Reading Pace - Fast'      => esc_html__( 'Reading Pace - Fast', 'accessibility-toolkit' ),
				'Reading Pace - Slow'      => esc_html__( 'Reading Pace - Slow', 'accessibility-toolkit' ),
				'Screen Reader - Disabled' => esc_html__( 'Screen Reader - Disabled', 'accessibility-toolkit' ),
				'Colors Set To Normal'     => esc_html__( 'Colors Set To Normal', 'accessibility-toolkit' ),
				'Colors Inverted'          => esc_html__( 'Colors Inverted', 'accessibility-toolkit' ),
				'Gray Hues Disabled'       => esc_html__( 'Gray Hues Disabled', 'accessibility-toolkit' ),
				'Gray Hues Enabled'        => esc_html__( 'Gray Hues Enabled', 'accessibility-toolkit' ),
				'Links UnderLined'         => esc_html__( 'Links UnderLined', 'accessibility-toolkit' ),
				'Links UnderLine Removed'  => esc_html__( 'Links UnderLine Removed', 'accessibility-toolkit' ),
				'Big Cursor Enabled'       => esc_html__( 'Big Cursor Enabled', 'accessibility-toolkit' ),
				'Big Cursor Disabled'      => esc_html__( 'Big Cursor Disabled', 'accessibility-toolkit' ),
				'Reading Guide Enabled'    => esc_html__( 'Reading Guide Enabled', 'accessibility-toolkit' ),
				'Reading Guide Disabled'   => esc_html__( 'Reading Guide Disabled', 'accessibility-toolkit' ),
				'Text Size Increased'      => esc_html__( 'Text Size Increased', 'accessibility-toolkit' ),
				'Text Size Decreased'      => esc_html__( 'Text Size Decreased', 'accessibility-toolkit' ),
				'Line Height Increased'    => esc_html__( 'Line Height Increased', 'accessibility-toolkit' ),
				'Line Height Decreased'    => esc_html__( 'Line Height Decreased', 'accessibility-toolkit' ),
				'Text Spacing Increased'   => esc_html__( 'Text Spacing Increased', 'accessibility-toolkit' ),
				'Text Spacing Decreased'   => esc_html__( 'Text Spacing Decreased', 'accessibility-toolkit' ),
			],
		],
		'hotkeys'  => [
			'enabled' => $settings['enable_hotkeys'],
		],
	];

	if ( ! empty( $settings['accessibility_statement'] ) ) {
		if ( $settings['as_iframe'] ) {
			$js_options['iframeModals'] = [
				[
					'iframeUrl'  => esc_url_raw( $settings['accessibility_statement'] ),
					'buttonText' => esc_html__( 'Accessibility Statement', 'accessibility-toolkit' ),
					'icon'       => 'policy',
				],
			];
		} else {
			$js_options['statement'] = [
				'url'  => esc_url_raw( $settings['accessibility_statement'] ),
				'text' => esc_html__( 'Accessibility Statement', 'accessibility-toolkit' ),
			];
		}
	}

	$js_options = apply_filters( 'accessibility_toolkit_js_options', $js_options );

	wp_localize_script(
		'accessibility_toolkit_frontend',
		'AccessibilityToolkit',
		$js_options
	);
}
