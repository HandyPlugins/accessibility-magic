<?php
/**
 * Uninstall functionalities
 * Deletes all plugin related data and configurations
 *
 * @package AccessibilityToolkit
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require_once 'accessibility-toolkit.php';

// delete plugin settings
delete_option( AccessibilityToolkit\Constants\SETTING_OPTION );
delete_site_option( AccessibilityToolkit\Constants\SETTING_OPTION );
