<?php
/**
 * WP Courseware - Mailchimp - Common.
 *
 * Defines all functions common and
 * useful throughout the plugin.
 *
 * @since 1.0.0
 */

namespace FlyPlugins\WPCW\Mailchimp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Is Mailchimp Enabled?
 *
 * @since 1.0.0
 *
 * @return bool True if Mailchimp is enabled.
 */
function is_mailchimp_enabled() {
	$api_key = get_mailchimp_api_key();
	/**
	 * Filter: Mailchimp Enabled?
	 *
	 * @since 1.0.0
	 *
	 * @param bool The hook enabled boolean. Default is true.
	 *
	 * @return bool The hook enabled boolean. Default is true.
	 */
	$enabled = apply_filters( 'wpcw_mailchimp_enabled', true );

	return ( 'yes' === wpcw_get_setting( 'mailchimp_enable' ) ) && $api_key && $enabled
		? true
		: false;
}

/**
 * Get Mailchimp Api Key.
 *
 * @since 1.0.0
 *
 * @return string The Mailchimp Api key.
 */
function get_mailchimp_api_key() {
	return wpcw_get_setting( 'mailchimp_api_key' );
}
