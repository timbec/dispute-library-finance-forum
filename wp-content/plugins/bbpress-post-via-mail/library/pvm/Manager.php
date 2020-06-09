<?php

class pvm_Manager extends pvm_Autohooker {
	protected static $available = array();
	protected static $registered_settings = array();

	public static function bootstrap() {
		self::register_hooks();
	}

	public static function key_for_setting( $connector, $key, $site_id = null ) {
		if ( is_multisite() ) {
			$site_id = $site_id ? $site_id : get_current_blog_id();
			return sprintf( 'pvm.site_%d.%s.%s', $site_id, $connector, $key );
		}

		return sprintf( 'pvm.%s.%s', $connector, $key );
	}

	/**
	 * @wp-action edit_user_profile
	 * @wp-action show_user_profile
	 */
	public static function user_profile_fields( $user_id ) {
		echo '<h3>' . esc_html__( 'Notification Settings', 'pvm' ) . '</h3>';

		if ( pvm::is_network_mode() ) {
			// On multisite, we want to output a table of all
			// notification settings
			$sites = pvm::get_option( 'pvm_enabled_sites', array() );

			echo '<p class="description">' . esc_html__( 'Set your email notification settings for the following sites.', 'pvm' ) . '</p>';
			do_action( 'pvm.manager.network_profile_fields', $user_id, $sites );

			?>
			<style>
				.pvm-grid .last_of_col {
					border-right: 2px solid rgba(0, 0, 0, 0.2);
				}
				.pvm-grid .last_of_col:last-child {
					border-right: none;
				}

				.pvm-grid thead th,
				.pvm-grid thead td,
				.pvm-grid tbody td {
					text-align: center;
				}
				.pvm-grid thead th {
					font-weight: bold;
				}
				.pvm-grid thead th,
				.pvm-grid thead td {
					border-bottom: 1px solid rgba(0, 0, 0, 0.1);
				}

				.pvm-grid th .details {
					font-weight: normal;
					font-size: 13px;
					color: #aaa;
				}
				.pvm-grid tbody th,
				.pvm-grid tbody td {
					border-bottom: 1px solid rgba(0, 0, 0, 0.05);
				}
			</style>
			<?php

		}
		else {
			?>

			<p class="description"><?php esc_html_e( 'Set your email notification settings for the current site.', 'pvm' ) ?></p>

			<table class="form-table">
				<?php do_action( 'pvm.manager.profile_fields', $user_id ) ?>
			</table>
			<?php
		}
	}

	/**
	 * @wp-action personal_options_update
	 * @wp-action edit_user_profile_update
	 */
	public static function save_profile_settings( $user_id ) {
		// Double-check permissions
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		$args = wp_unslash( $_POST );
		if ( pvm::is_network_mode() ) {
			$sites = pvm::get_option( 'pvm_enabled_sites', array() );
			do_action( 'pvm.manager.save_network_profile_fields', $user_id, $args, $sites );
		}
		else {
			do_action( 'pvm.manager.save_profile_fields', $user_id, $args );
		}
	}

	/**
	 * @wp-action wp_dashboard_setup
	 */
	public static function register_dashboard_widget( $widgets ) {
		wp_add_dashboard_widget( 'pvm_notification_settings', __( 'Notification Settings', 'pvm' ), array( get_class(), 'output_dashboard_widget' ) );
	}

	public static function output_dashboard_widget() {
		$user = wp_get_current_user();
		?>
		<table class="form-table">
			<?php do_action( 'pvm.manager.profile_fields', $user ) ?>
		</table>
		<?php
	}

	protected static function available_sites() {
		$sites = wp_get_sites( array(
			'archived' => false,
			'deleted' => false,
			'spam' => false,
		) );

		return apply_filters( 'pvm.manager.available_sites', $sites );
	}

	public static function register_default_settings() {
		if ( pvm::is_network_mode() ) {
			self::register_network_settings();
		}

		add_settings_section( 'bb_pvm_options_notifications', 'Default Notification Settings', array( get_class(), 'output_default_settings_header' ), 'bb_pvm_options' );

		$connectors = pvm::get_connectors();
		foreach ( $connectors as $type => $connector ) {
			//error_log("Connector: ".$connector->get_name());
			if ( ! is_callable( array( $connector, 'get_available_settings' ) ) ) {
				continue;
			}
                      
			$args = array(
				'type' => $type,
				'connector' => $connector,
			);
			add_settings_field(
				'pvm_options_notifications-' . $type,
				$connector->get_name(),
				array( get_class(), 'output_default_settings' ),
				'bb_pvm_options',
				'bb_pvm_options_notifications',
				$args
			);

			$available = $connector->get_available_settings();	
			$debug_export = var_export($available, true);
	                //error_log("Available settings -> ".$debug_export);
			if ($available) {
				self::$available[ $type ] = $available;

				foreach ( $available as $key => $title ) {
					$setting_key = self::key_for_setting( $type, 'notifications.' . $key );
					register_setting( 'bb_pvm_options', $setting_key );

					// Add the filter ourselves, so that we can specify two params
					add_filter( "sanitize_option_{$setting_key}", array( get_class(), 'sanitize_notification_option' ), 10, 2 );

					// Save the key for later
					self::$registered_settings[ $setting_key ] = array( $type, $key );
				}
			}
		}
	}

	public static function output_default_settings_header() {
		echo '<p>' . __('Set the default user notification settings here.', 'pvm') . '</p>';
	}

	public static function output_default_settings( $args ) {
		$connector = $args['connector'];
		?>
		<table class="form-table">
			<?php $connector->output_settings() ?>
		</table>
		<?php
	}

	/**
	 * POST data in PHP has `.` converted to underscores. For
	 * `register_setting` to work correctly, we need to undo this.
	 *
	 * The reason PHP does this appears to be a holdover from legacy
	 * `register_globals` days. PHP variables can't have a `.` in them, so it
	 * converts these to legal variable names.
	 *
	 * @wp-filter option_page_capability_bb_pvm_options
	 */
	public static function unmangle_notification_data( $cap ) {
		foreach ( self::$registered_settings as $key => $opts ) {
			$mangled = str_replace( '.', '_', $key );

			if ( isset( $_POST[ $mangled ] ) && !isset( $_POST[ $key ] ) ) {
				$_POST[ $key ] = $_POST[ $mangled ];
			}
		}

		return $cap;
	}

	public static function sanitize_notification_option( $value, $name ) {
		if ( ! isset( self::$registered_settings[ $name ] ) ) {
			return $value;
		}

		list( $connector, $key ) = self::$registered_settings[ $name ];
		$valid = self::$available[ $connector ][ $key ];

		// Check the value is valid
		if ( isset( $valid[ $value ] ) ) {
			return $value;
		}

		add_settings_error(
			$name,
			'bb_pvm_option_invalid',
			__('The notification option is invalid', 'pvm')
		);
		return false;
	}

	public static function register_network_settings() {
		register_setting( 'bb_pvm_options', 'pvm_enabled_sites', array(__CLASS__, 'validate_sites') );
		add_settings_field( 'bb_pvm_options_sites', 'Active Sites', array( __CLASS__, 'settings_field_sites' ), 'bb_pvm_options', 'bb_pvm_options_global' );
	}

	public static function settings_field_sites() {
		$sites = self::available_sites();
		$current = pvm::get_option( 'pvm_enabled_sites', array() );

		foreach ( $sites as $site ) {
			$details = get_blog_details( $site['blog_id'] );
			$value = absint( $site['blog_id'] );
			$enabled = in_array( $value, $current );

			printf(
				'<label><input type="checkbox" name="%s[]" value="%s" %s /> %s</label><br />',
				'pvm_enabled_sites',
				esc_attr( $value ),
				checked( $enabled, true, false ),
				esc_html( $details->blogname )
			);

		}

		echo '<p class="description">' . esc_html__( 'Select which sites to activate pvm on.', 'pvm' ) . '</p>';
	}

	public static function validate_sites( $value ) {
		$value = (array) $value;

		$sites = self::available_sites();
		$site_ids = wp_list_pluck( $sites, 'blog_id' );

		// Ensure all values are available
		$sanitized = array_values( array_intersect( $value, $site_ids ) );
		$sanitized = array_map( 'absint', $sanitized );
		return $sanitized;
	}
}
