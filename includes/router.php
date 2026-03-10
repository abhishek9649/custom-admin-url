<?php
/**
 * Routing logic for Custom Admin URL
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'car_handle_custom_login' );

function car_handle_custom_login() {
	$slug = get_option( 'car_admin_slug', 'secure-access' );
	
	$raw_request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

	$request_uri = wp_parse_url( $raw_request_uri, PHP_URL_PATH );
	$site_path   = wp_parse_url( site_url(), PHP_URL_PATH );
	
	$relative_path = trim( str_replace( $site_path, '', $request_uri ), '/' );

	// 1. Handle Custom Slug Access
	if ( $relative_path === $slug ) {
		status_header( 200 );
		require_once ABSPATH . 'wp-login.php';
		exit;
	}

	// 2. Block direct wp-login.php access
	if ( false !== strpos( $raw_request_uri, 'wp-login.php' ) && ! is_user_logged_in() ) {
		
		/** * We cannot verify nonces on the login page because the user is not yet authenticated.
		 * The following flags disable the NonceVerification warnings for this specific check.
		 */
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$is_post_login = isset( $_POST['log'] ); 
		
		
		if ( ! isset( $_GET['action'] ) && ! $is_post_login ) {
			global $wp_query;
			$wp_query->set_404();
			status_header( 404 );
			
			if ( get_404_template() ) {
				get_template_part( '404' );
			} else {
				
				wp_die( esc_html__( '404 Not Found', 'custom-admin-url' ), '', array( 'response' => 404 ) );
			}
			exit;
		}
	}
}



add_filter( 'login_url', 'car_custom_login_url', 10, 3 );
function car_custom_login_url( $login_url, $redirect, $force_reauth ) {
	$slug = get_option( 'car_admin_slug', 'secure-access' );
	return home_url( '/' . $slug );
}
