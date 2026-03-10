<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'car_add_dashboard_menu' );

function car_add_dashboard_menu() {
	add_menu_page(
		'Admin Shield',          
		'🛡️ Admin Shield',         
		'manage_options',       
		'admin-shield-settings', 
		'car_render_settings',   
		'dashicons-shield-lock', 
		80                                    
	);
}

function car_render_settings() {
	
	if ( isset( $_POST['car_save'] ) ) {
		check_admin_referer( 'car_save_slug' );
		
		$new_slug = isset( $_POST['car_slug'] ) ? sanitize_title( wp_unslash( $_POST['car_slug'] ) ) : 'secure-access';
		update_option( 'car_admin_slug', $new_slug );
		
		$sec_settings = array(
			'brute_force'      => isset( $_POST['brute_force'] ) ? 1 : 0,
			'max_attempts'     => isset( $_POST['max_attempts'] ) ? absint( wp_unslash( $_POST['max_attempts'] ) ) : 5,
			'lockout_duration' => isset( $_POST['lockout_duration'] ) ? absint( wp_unslash( $_POST['lockout_duration'] ) ) : 30,
		);
		update_option( 'cau_settings', $sec_settings );
		
		flush_rewrite_rules();
		
		add_settings_error( 'car_messages', 'car_message', 'All security settings have been updated successfully!', 'updated' );
	}

	$slug = get_option( 'car_admin_slug', 'secure-access' );
	$sec_settings = get_option( 'cau_settings', array(
		'brute_force'      => 1,
		'max_attempts'     => 5,
		'lockout_duration' => 30,
	) );
	$custom_url = home_url( '/' . $slug );
	?>

	<div class="wrap">
		<h1 class="wp-heading-inline">
			<span class="dashicons dashicons-shield-lock" style="font-size: 2.2rem; width: auto; height: auto; margin-right: 12px; color: #2271b1; vertical-align: middle;"></span> 
			Admin Shield & Security Settings
		</h1>
		<hr class="wp-header-end">

		<?php settings_errors( 'car_messages' ); ?>

		<div class="notice notice-warning is-dismissible" style="margin-top: 20px;">
			<p>
				<strong>⚠️ Security Alert:</strong> Please <strong>bookmark</strong> your new login URL: 
				<code style="background: #fff; padding: 2px 8px; border: 1px solid #ccc;"><?php echo esc_url( $custom_url ); ?></code> 
				If you forget this path, you might be locked out of your dashboard.
			</p>
		</div>

		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-1">
				<div id="post-body-content">
					<form method="post">
						<?php wp_nonce_field( 'car_save_slug' ); ?>

						<div class="postbox">
							<div class="postbox-header">
								<h2 class="hndle ui-sortable-handle">
									<span class="dashicons dashicons-admin-links"></span> 1. Custom Access Configuration
								</h2>
							</div>
							<div class="inside" style="padding: 0 12px 12px;">
								<table class="form-table">
									<tr>
										<th scope="row">Login Slug</th>
										<td>
											<input type="text" name="car_slug" value="<?php echo esc_attr( $slug ); ?>" class="regular-text" placeholder="e.g. secure-login" required />
											<p class="description">This slug replaces <code>wp-login.php</code>. Example: <code>yoursite.com/<?php echo esc_html( $slug ); ?></code></p>
										</td>
									</tr>
								</table>
							</div>
						</div>

						<div class="postbox">
							<div class="postbox-header">
								<h2 class="hndle ui-sortable-handle">
									<span class="dashicons dashicons-lock"></span> 2. Brute Force Protection
								</h2>
							</div>
							<div class="inside" style="padding: 0 12px 12px;">
								<table class="form-table">
									<tr>
										<th scope="row">Protection Status</th>
										<td>
											<fieldset>
												<label for="brute_force">
													<input type="checkbox" name="brute_force" id="brute_force" value="1" <?php checked( 1, $sec_settings['brute_force'] ); ?> /> 
													Enable IP lockout after multiple failed login attempts.
												</label>
											</fieldset>
										</td>
									</tr>
									<tr>
										<th scope="row">Max Allowed Attempts</th>
										<td>
											<input type="number" name="max_attempts" value="<?php echo esc_attr( $sec_settings['max_attempts'] ); ?>" class="small-text" min="1" />
											<p class="description">Number of failed tries before a user is temporarily blocked.</p>
										</td>
									</tr>
									<tr>
										<th scope="row">Lockout Period (Minutes)</th>
										<td>
											<input type="number" name="lockout_duration" value="<?php echo esc_attr( $sec_settings['lockout_duration'] ); ?>" class="small-text" min="1" />
											<p class="description">How long the IP address will remain on the blacklist.</p>
										</td>
									</tr>
								</table>
							</div>
						</div>

						<?php submit_button( 'Update Security Shield', 'primary large', 'car_save' ); ?>
					</form>
				</div>
			</div>
		</div>
	</div>
	<?php
}
