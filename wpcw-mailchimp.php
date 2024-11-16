<?php
/**
 * Plugin Name: WP Courseware - Mailchimp Addon
 * Plugin URI:  https://wordpress.org/plugins/wp-courseware-mailchimp-addon/
 * Author:      Fly Plugins
 * Author URI:  https://flyplugins.com/
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Description: Mailchimp add-on for WP Courseware. Subscribe your customers to mailchimp audiance, and tags upon enrollment.
 * Version:     1.0.1
 * Text Domain: wpcw-mailchimp
 * Domain Path: /languages/
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Version.
define( 'WPCW_MAILCHIMP_VERSION', '1.0.1' );

// File, Path & Url.
define( 'WPCW_MAILCHIMP_FILE', __FILE__ );
define( 'WPCW_MAILCHIMP_PATH', plugin_dir_path( WPCW_MAILCHIMP_FILE ) );
define( 'WPCW_MAILCHIMP_URL', plugin_dir_url( WPCW_MAILCHIMP_FILE ) );

/**
 * Load WP Courseware - Mailchimp Plugin.
 *
 * @since 1.0.0
 */
function wpcw_mailchimp_load() {
	// Load Textdomain.
	load_plugin_textdomain( 'wpcw-mailchimp', false, WPCW_MAILCHIMP_PATH . 'languages/' );

	// Load Requirements.
	require_once WPCW_MAILCHIMP_PATH . 'includes/requirements.php';

	// Meets Requirements?
	if ( ! wpcw_mailchimp_meets_requirements() ) {
		return;
	}

	// Load Files.
	require_once WPCW_MAILCHIMP_PATH . 'includes/common.php';
	require_once WPCW_MAILCHIMP_PATH . 'includes/enrollment.php';
	require_once WPCW_MAILCHIMP_PATH . 'includes/settings.php';

}
add_action( 'plugins_loaded', 'wpcw_mailchimp_load' );
