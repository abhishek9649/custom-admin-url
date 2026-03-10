<?php
/**
 * Security Logic for Admin Shield
 */

// 1. Direct access protection fixed (Error: missing_direct_file_access_protection)
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'plugins_loaded', 'car_protect_admin' );

function car_protect_admin() {
	$slug = get_option( 'car_admin_slug', 'secure-access' );

	if ( is_admin() && ! defined( 'DOING_AJAX' ) && ! defined( 'DOING_CRON' ) ) {
		
		$php_self        = isset( $_SERVER['PHP_SELF'] ) ? sanitize_text_field( wp_unslash( $_SERVER['PHP_SELF'] ) ) : '';
		$current_script  = basename( $php_self );
		$allowed_scripts = array( 'upgrade.php', 'update.php', 'admin-ajax.php' );

		if ( in_array( $current_script, $allowed_scripts, true ) ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
            // wp_safe_redirect(home_url('/404'), 404);
			wp_safe_redirect( home_url( '/' . $slug ) );
			exit;
		}
	}
}

add_action( 'wp_login_failed', 'car_track_failed_logins' );

function car_track_failed_logins( $username ) {
	$sec_settings = get_option( 'cau_settings' );

	if ( isset( $sec_settings['brute_force'] ) && 1 === (int) $sec_settings['brute_force'] ) {
		$ip            = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0.0.0.0';
		$transient_key = 'car_failed_' . str_replace( '.', '_', $ip );
		
		$attempts = get_transient( $transient_key );
		$attempts = ( $attempts ) ? (int) $attempts + 1 : 1;

		$max_attempts = isset( $sec_settings['max_attempts'] ) ? (int) $sec_settings['max_attempts'] : 5;
		$lockout_min  = isset( $sec_settings['lockout_duration'] ) ? (int) $sec_settings['lockout_duration'] : 30;

		if ( $attempts >= $max_attempts ) {
			set_transient( 'car_block_' . str_replace( '.', '_', $ip ), true, $lockout_min * MINUTE_IN_SECONDS );
			delete_transient( $transient_key );
		} else {
			set_transient( $transient_key, $attempts, HOUR_IN_SECONDS );
		}
	}
}

add_filter( 'authenticate', 'car_check_brute_lockout', 30, 3 );

function car_check_brute_lockout( $user, $username, $password ) {
	$ip        = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0.0.0.0';
	$is_locked = get_transient( 'car_block_' . str_replace( '.', '_', $ip ) );

	if ( $is_locked ) {
		$sec_settings = get_option( 'cau_settings' );
		$lockout_min  = isset( $sec_settings['lockout_duration'] ) ? (int) $sec_settings['lockout_duration'] : 30;
		
		// 2. Fixed Text Domain: 'admin-shield' changed to 'custom-admin-url' (Error: TextDomainMismatch)
		return new WP_Error(
			'locked_out',
			sprintf(
				/* translators: %d: lockout duration in minutes */
				__( '🛡️ Admin Shield: Too many failed attempts. Try again after %d minutes.', 'custom-admin-url' ),
				$lockout_min
			)
		);
	}
	return $user;
}