<?php
/**
 *  Constants
 *
 * @package MagicLogin
 */

namespace AccessibilityToolkit\Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

const SETTING_OPTION = 'accessibility_toolkit_settings';

const DEFAULT_SETTINGS
= [
	'enable_text_size'        => true,
	'enable_text_spacing'     => true,
	'enable_line_height'      => true,
	'enable_invert_colors'    => true,
	'enable_gray_hues'        => true,
	'enable_underline_links'  => true,
	'enable_big_cursor'       => true,
	'enable_reading_guide'    => true,
	'disable_animations'      => true,
	'enable_text_to_speech'   => true,
	'enable_hotkeys'          => true,
	'accessibility_statement' => '',
	'excluded_pages'          => '',
	'as_iframe'               => false, // open accessibility statement in iframe
	'icon'                    => [
		'circular'         => true,
		'position'         => 'bottom-right',
		'offset_x'         => 50,
		'offset_y'         => 50,
		'offset_unit'      => 'px',
		'dimension_w'      => 50,
		'dimension_h'      => 50,
		'dimension_unit'   => 'px',
		'background_color' => '#4054b2',
		'color'            => '#fff',
	],
];

// urls
const DOCS_URL    = 'hhttps://handyplugins.co/docs-category/wp-accessibility-toolkit/';
const BLOG_URL    = 'https://handyplugins.co/blog/';
const FAQ_URL     = 'https://handyplugins.co/wp-accessibility-toolkit/#faq';
const SUPPORT_URL = 'https://handyplugins.co/contact/';
const GITHUB_URL  = 'https://github.com/HandyPlugins';
const TWITTER_URL = 'https://twitter.com/HandyPlugins';
