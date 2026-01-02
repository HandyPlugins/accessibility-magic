<?php
/**
 * Uninstall functionalities
 * Deletes all plugin related data and configurations
 *
 * @package AccessibilityMagic
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require_once 'accessibility-magic.php';

// delete plugin settings
delete_option( AccessibilityMagic\Constants\SETTING_OPTION );
delete_site_option( AccessibilityMagic\Constants\SETTING_OPTION );
