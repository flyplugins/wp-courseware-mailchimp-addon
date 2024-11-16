<?php
/**
 * WP Courseware - Mailchimp - Enrollment.
 *
 * Defines all functions required to properly
 * integrate with WP Courseware enrollment.
 *
 * @since 1.0.0
 */

namespace FlyPlugins\WPCW\Mailchimp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Hooks.
add_action( 'wpcw_enroll_user', __NAMESPACE__ . '\mailchimp_actions_upon_enrollment', 10, 2 );

/**
 * Convert Kit Actions upon Enrollment.
 *
 * @since 1.0.0
 *
 * @param int   $user_id The user id.
 * @param array $courses_enrolled The courses the student was enrolled.
 */
function mailchimp_actions_upon_enrollment( $user_id, $courses_enrolled ) {
	if ( ! is_mailchimp_enabled() || empty( $courses_enrolled ) ) {
		return;
	}

	$user             = get_userdata( $user_id );
	$user_email       = $user->user_email;
	$user_fname       = $user->first_name;
	$user_lname       = $user->last_name;
	$user_fulladdress = '';

	$billing_address_1 = get_user_meta( $user_id, 'billing_address_1', true );
	$billing_address_2 = get_user_meta( $user_id, 'billing_address_2', true );
	$billing_city      = get_user_meta( $user_id, 'billing_city', true );
	$billing_state     = get_user_meta( $user_id, 'billing_state', true );
	$billing_postcode  = get_user_meta( $user_id, 'billing_postcode', true );
	$billing_country   = get_user_meta( $user_id, 'billing_country', true );

	$api_key            = wpcw_get_setting( 'mailchimp_api_key' );
	$mailchimp_tags     = wpcw_get_setting( 'mailchimp_tags' );
	$mailchimp_tags_arr = explode( ',', $mailchimp_tags );
	$list_id            = wpcw_get_setting( 'mailchimp_audiance' );

	$status       = 'subscribed'; // "subscribed" or "unsubscribed" or "cleaned" or "pending"
		$args     = array(
			'method'  => 'PUT',
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( 'user:' . $api_key ),
			),
			'body'    => json_encode(
				array(
					'email_address' => $user_email,
					'status'        => $status,
					'tags'          => $mailchimp_tags_arr,
					'merge_fields'  => array(
						'FNAME'   => $user_fname,
						'LNAME'   => $user_lname,
						'ADDRESS' => array(
							'addr1'   => $billing_address_1,
							'addr2'   => $billing_address_2,
							'city'    => $billing_city,
							'state'   => $billing_state,
							'zip'     => $billing_postcode,
							'country' => $billing_country,
						),
					),
				)
			),
		);
		$response = wp_remote_post( 'https://' . substr( $api_key, strpos( $api_key, '-' ) + 1 ) . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/' . md5( strtolower( $user_email ) ), $args );
		$body     = json_decode( $response['body'] );

		if ( $response['response']['code'] == 200 && $body->status == $status ) {
			// Success message.
		} else {
			// Fail message.
		}

}
