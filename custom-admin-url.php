<?php
/**
 * Plugin Name:       Custom Admin URL
 * Description:       Replaces /wp-admin and /wp-login.php with a custom URL slug to improve security.
 * Version:           1.0.1
 * Author:            Abhishek Sharma 
 * Text Domain:       custom-admin-url
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package CustomAdminURL
 */

// Direct access protection
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Fixed: Constant definition with standardized naming
define( 'CAR_PATH', plugin_dir_path( __FILE__ ) );
define( 'CAR_URL', plugin_dir_url( __FILE__ ) );

require_once CAR_PATH . 'includes/settings.php';
require_once CAR_PATH . 'includes/router.php';
require_once CAR_PATH . 'includes/security.php';

// Flush rules on activation
register_activation_hook( __FILE__, 'car_flush_rules' );
function car_flush_rules() {
	flush_rewrite_rules();
}