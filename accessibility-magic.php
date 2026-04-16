<?php
/**
 * Plugin Name:       Accessibility Magic
 * Plugin URI:        https://handyplugins.co/wp-accessibility-toolkit/
 * Description:       Accessibility Magic is a collection of tools to make your WordPress website more accessible.
 * Version:           1.0.1
 * Requires at least: 5.0
 * Requires PHP:      7.2
 * Author:            HandyPlugins
 * Author URI:        https://handyplugins.co/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       accessibility-magic
 * Domain Path:       /languages
 *
 * @package           AccessibilityMagic
 */

namespace AccessibilityMagic;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Useful global constants.
define( 'ACCESSIBILITY_MAGIC_VERSION', '1.0.1' );
define( 'ACCESSIBILITY_MAGIC_DB_VERSION', '1.0' );
define( 'ACCESSIBILITY_MAGIC_PLUGIN_FILE', __FILE__ );
define( 'ACCESSIBILITY_MAGIC_URL', plugin_dir_url( __FILE__ ) );
define( 'ACCESSIBILITY_MAGIC_PATH', plugin_dir_path( __FILE__ ) );
define( 'ACCESSIBILITY_MAGIC_INC', ACCESSIBILITY_MAGIC_PATH . 'includes/' );

// deactivate free
if ( defined( 'ACCESSIBILITY_TOOLKIT_PRO_PLUGIN_FILE' ) ) {
	if ( ! function_exists( 'deactivate_plugins' ) ) {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	deactivate_plugins( plugin_basename( ACCESSIBILITY_TOOLKIT_PRO_PLUGIN_FILE ) );

	return;
}

// Require Composer autoloader if it exists.
if ( file_exists( ACCESSIBILITY_MAGIC_PATH . '/vendor/autoload.php' ) ) {
	require_once ACCESSIBILITY_MAGIC_PATH . 'vendor/autoload.php';
}

// Include files.
require_once ACCESSIBILITY_MAGIC_INC . 'constants.php';
require_once ACCESSIBILITY_MAGIC_INC . 'utils.php';
require_once ACCESSIBILITY_MAGIC_INC . 'core.php';
require_once ACCESSIBILITY_MAGIC_INC . 'settings.php';
require_once ACCESSIBILITY_MAGIC_INC . 'compat.php';


$accessibility_magic_network_activated = Utils\is_network_wide( ACCESSIBILITY_MAGIC_PLUGIN_FILE );
if ( ! defined( 'ACCESSIBILITY_MAGIC_IS_NETWORK' ) ) {
	define( 'ACCESSIBILITY_MAGIC_IS_NETWORK', $accessibility_magic_network_activated );
}

/**
 * Setup routine
 *
 * @return void
 * @since 1.0 bootstrapping with plugins_loaded hook
 */
function setup_accessibility_magic() {
	// Bootstrap.
	Core\setup();
	Compat\setup();
	Settings\setup();
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\setup_accessibility_magic' );
