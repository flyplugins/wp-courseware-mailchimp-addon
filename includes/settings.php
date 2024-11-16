<?php
/**
 * WP Courseware - Mailchimp - Settings.
 *
 * Defines all required functions for
 * setting and retrieving settings.
 *
 * @since 1.0.0
 */

namespace FlyPlugins\WPCW\Mailchimp;

// Exit if accessed directly.
use WP_Screen;

defined( 'ABSPATH' ) || exit;

// Hooks.
add_filter( 'wpcw_admin_settings_tab_addons', __NAMESPACE__ . '\settings_tab_addons' );
add_action( 'wpcw_enqueue_scripts', __NAMESPACE__ . '\settings_enqueue_assets' );
add_action( 'wpcw_enqueue_scripts', __NAMESPACE__ . '\settings_enqueue_assets' );
/**
 * Settings Tab Add-ons.
 *
 * @since 1.0.0
 *
 * @param array $addons The settings tab addons.
 *
 * @return array $addons The settings tab addons.
 */
function settings_tab_addons( $addons ) {
	// Check to see if Mailchimp extension exists.
	if ( isset( $addons['sections']['mailchimp'] ) ) {
		return $addons;
	}

	$section_slug           = wpcw_post_var( 'section' ) ? wpcw_post_var( 'section' ) : '';
	$mailchimp_audiance_arr = array( '0' => esc_html__( 'Select', 'wp-courseware' ) );
	$mailchimp_audiance     = array();
	$saved_api_key          = wpcw_get_setting( 'mailchimp_api_key' );
	$api_key                = wpcw_post_var( 'mailchimp_api_key' ) ? wpcw_post_var( 'mailchimp_api_key' ) : '';
	
	if ( isset( $_POST['wpcw-form-submit'] ) && 'mailchimp' === $section_slug  ) {
		
		if ($saved_api_key != $api_key ) {
			$_POST['mailchimp_tags'] = '';
		}
		$listarray = array();
		$args      = array(
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( 'user:' . $api_key ),
			),
		);
		$url       = 'https://' . substr( $api_key, strpos( $api_key, '-' ) + 1 ) . '.api.mailchimp.com/3.0/lists/';
		$result    = wp_remote_get( $url, $args );
		if ( ! is_wp_error( $result ) ) {
			$body = json_decode( $result['body'] );

			if ( ! empty( $body->lists ) ) {
				foreach ( $body->lists as $list ) {
					$listarray[ $list->id ] = $list->name;
				}
			}
			update_option( 'mailchimp_audiance', maybe_serialize( $listarray ) );

		} else {
			$listarray = array();
			update_option( 'mailchimp_audiance', maybe_serialize( $listarray ) );

		}

		

		
	}
		$mailchimp_audiance = get_option( 'mailchimp_audiance', true );
		$mailchimp_audiance = maybe_unserialize( $mailchimp_audiance );

	if ( is_array($mailchimp_audiance) && 0 < count( $mailchimp_audiance ) ) {

		foreach ( $mailchimp_audiance as $key => $mailchimp_audiance_val ) {
			$mailchimp_audiance_arr += array( $key => $mailchimp_audiance_val );
		}
	}

	/**
	 * Filter: Mailchimp Settings Fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array The array of mailchimp settings fields.
	 *
	 * @return array The array of mailchimp settings fields.
	 */
	$mailchimp_fields = apply_filters(
		'wpcw_mailchimp_settings_fields',
		array(
			array(
				'type'  => 'heading',
				'key'   => 'mailchimp_section_heading',
				'title' => esc_html__( 'Mailchimp', 'wpcw-mailchimp' ),
				'desc'  => esc_html__( 'Below are settings related to the Mailchimp addon.', 'wpcw-mailchimp' ),
			),
			array(
				'type'     => 'checkbox',
				'key'      => 'mailchimp_enable',
				'title'    => esc_html__( 'Enable Mailchimp?', 'wpcw-mailchimp' ),
				'label'    => esc_html__( 'Enable the Mailchimp integration.', 'wpcw-mailchimp' ),
				'desc_tip' => esc_html__( 'When Mailchimp is enabled, WP Courseware will use the settings below to add users to mailchimp audiance and tags when enrolled into a course.', 'wpcw-mailchimp' ),
				'default'  => 'yes',
			),
			array(
				'type'        => 'password',
				'key'         => 'mailchimp_api_key',
				'default'     => '',
				'placeholder' => esc_html__( 'Mailchimp Api Key', 'wpcw-mailchimp' ),
				'title'       => esc_html__( 'Mailchimp Api Key', 'wpcw-mailchimp' ),
				'desc'        => esc_html__( 'Enter your Mailchimp Api key.', 'wpcw-mailchimp' ),
				'desc_tip'    => esc_html__( 'Your Mailchimp Api key can be found in your Mailchimp account under the Extras menu item.', 'wpcw-mailchimp' ),
				'condition'   => array(
					'field' => 'mailchimp_enable',
					'value' => 'on',
				),
			),
			array(
				'type'     => 'select',
				'key'      => 'mailchimp_audiance',
				'title'    => esc_html__( 'Mailchimp Audiance', 'wpcw-mailchimp' ),
				'label'    => esc_html__( 'Mailchimp Audiance integration.', 'wpcw-mailchimp' ),
				'desc_tip' => esc_html__( 'List of mailchimp audiance created under your account.', 'wpcw-mailchimp' ),
				'options'  => $mailchimp_audiance_arr,

			),
			array(
				'type'     => 'text',
				'key'      => 'mailchimp_tags',
				'title'    => esc_html__( 'Mailchimp Tags', 'wpcw-mailchimp' ),
				'label'    => esc_html__( 'Mailchimp Tags', 'wpcw-mailchimp' ),
				'desc_tip' => esc_html__( 'Add tags with comma seperator.', 'wpcw-mailchimp' ),
				'default'  => '',
			),

		)
	);

	/**
	 * Filter: Mailchimp Settings Section
	 *
	 * @since 1.0.0
	 *
	 * @param array The mailchimp settings section params.
	 *
	 * @return array The mailchimp settings section params.
	 */
	$addons['sections']['mailchimp'] = apply_filters(
		'wpcw_mailchimp_settings_section',
		array(
			'label'   => esc_html__( 'Mailchimp', 'wpcw-mailchimp' ),
			'form'    => true,
			'default' => true,
			'fields'  => $mailchimp_fields,
			'submit'  => esc_html__( 'Save Settings', 'wpcw-mailchimp' ),
		)
	);

	return $addons;
}

/**
 * Settings Enqueue Assets.
 *
 * @since 1.0.0
 *
 * @param WP_Screen $admin_screen The admin screen slug.
 */
function settings_enqueue_assets( $admin_screen ) {
	if ( 'wp-courseware_page_wpcw-settings' !== $admin_screen->id ) {
		return;
	}
}
