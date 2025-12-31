<?php
/**
 * Plugin Name:       Accessibility Toolkit
 * Plugin URI:        https://handyplugins.co/wp-accessibility-toolkit/
 * Description:       Accessibility Toolkit is a collection of tools to make your WordPress website more accessible.
 * Version:           1.0
 * Requires at least: 5.0
 * Requires PHP:      7.2
 * Author:            HandyPlugins
 * Author URI:        https://handyplugins.co/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       accessibility-toolkit
 * Domain Path:       /languages
 *
 * @package           AccessibilityToolkit
 */

namespace AccessibilityToolkit;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Useful global constants.
define( 'ACCESSIBILITY_TOOLKIT_VERSION', '1.0' );
define( 'ACCESSIBILITY_TOOLKIT_DB_VERSION', '1.0' );
define( 'ACCESSIBILITY_TOOLKIT_PLUGIN_FILE', __FILE__ );
define( 'ACCESSIBILITY_TOOLKIT_URL', plugin_dir_url( __FILE__ ) );
define( 'ACCESSIBILITY_TOOLKIT_PATH', plugin_dir_path( __FILE__ ) );
define( 'ACCESSIBILITY_TOOLKIT_INC', ACCESSIBILITY_TOOLKIT_PATH . 'includes/' );

// deactivate free
if ( defined( 'ACCESSIBILITY_TOOLKIT_PRO_PLUGIN_FILE' ) ) {
	if ( ! function_exists( 'deactivate_plugins' ) ) {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	deactivate_plugins( plugin_basename( ACCESSIBILITY_TOOLKIT_PRO_PLUGIN_FILE ) );

	return;
}

// Require Composer autoloader if it exists.
if ( file_exists( ACCESSIBILITY_TOOLKIT_PATH . '/vendor/autoload.php' ) ) {
	require_once ACCESSIBILITY_TOOLKIT_PATH . 'vendor/autoload.php';
}

// Include files.
require_once ACCESSIBILITY_TOOLKIT_INC . 'constants.php';
require_once ACCESSIBILITY_TOOLKIT_INC . 'utils.php';
require_once ACCESSIBILITY_TOOLKIT_INC . 'core.php';
require_once ACCESSIBILITY_TOOLKIT_INC . 'settings.php';
require_once ACCESSIBILITY_TOOLKIT_INC . 'compat.php';


$accessibility_toolkit_network_activated = Utils\is_network_wide( ACCESSIBILITY_TOOLKIT_PLUGIN_FILE );
if ( ! defined( 'ACCESSIBILITY_TOOLKIT_IS_NETWORK' ) ) {
	define( 'ACCESSIBILITY_TOOLKIT_IS_NETWORK', $accessibility_toolkit_network_activated );
}

/**
 * Setup routine
 *
 * @return void
 * @since 1.0 bootstrapping with plugins_loaded hook
 */
function setup_accessibility_toolkit() {
	// Bootstrap.
	Core\setup();
	Compat\setup();
	Settings\setup();
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\setup_accessibility_toolkit' );
