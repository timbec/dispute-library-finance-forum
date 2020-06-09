<?php

class pvm_Admin extends pvm_Autohooker {
	/**
	 * Should we wipe the handler-specific options?
	 *
	 * When the handler is changed, we can't keep the same handler-specific
	 * options saved, so this flag ensures that we wipe the data.
	 *
	 * @var boolean
	 */
	protected static $wipe_handler_options = false;

	protected static $registered_handler_settings = false;

	/**
	 * Bootstrap the class
	 *
	 * Ensures all necessary hooks are added
	 */
	public static function bootstrap() {
		self::register_hooks();
		pvm_Manager::bootstrap();
	}

	/**
	 * Initialization
	 *
	 * @wp-action admin_init
	 */
	public static function init() {
		if (!is_admin()) {
			return false;
		}
		register_setting( 'bb_pvm_options', 'bb_pvm_replyto', array(__CLASS__, 'validate_replyto') );
		register_setting( 'bb_pvm_options', 'bb_pvm_handler_type', array(__CLASS__, 'validate_type') );
		register_setting( 'bb_pvm_options', 'bb_pvm_replyto', array(__CLASS__, 'validate_replyto') );
		register_setting( 'bb_pvm_options', 'bb_pvm_from_email', array(__CLASS__, 'validate_from_email') );
		register_setting( 'bb_pvm_options', 'bb_pvm_send_to_author', array(__CLASS__, 'validate_send_to_author') );
		register_setting( 'bb_pvm_options', 'bb_pvm_handler_options', array(__CLASS__, 'validate_handler_options') );

		register_setting( 'bb_pvm_options', 'bb_pvm_new_topic_subj', array(__CLASS__, 'validate_new_topic_subj') );
        register_setting( 'bb_pvm_options', 'bb_pvm_new_topic_msg', array(__CLASS__, 'validate_new_topic_msg') );
        register_setting( 'bb_pvm_options', 'bb_pvm_new_reply_subj', array(__CLASS__, 'validate_new_reply_subj') );
        register_setting( 'bb_pvm_options', 'bb_pvm_new_reply_msg', array(__CLASS__, 'validate_new_reply_msg') );
        register_setting( 'bb_pvm_options', 'bb_pvm_new_topic_notice', array(__CLASS__, 'validate_new_topic_notice') );
        register_setting( 'bb_pvm_options', 'bb_pvm_new_reply_notice', array(__CLASS__, 'validate_new_reply_notice') );
        register_setting( 'bb_pvm_options', 'bb_pvm_group_notice', array(__CLASS__, 'validate_group_notice') );
        register_setting( 'bb_pvm_options', 'bb_pvm_topic_autosubscribe', array(__CLASS__, 'validate_topic_autosubscribe') );
        register_setting( 'bb_pvm_options', 'bb_pvm_new_post_subj', array(__CLASS__, 'validate_new_post_subj') );
        register_setting( 'bb_pvm_options', 'bb_pvm_new_post_msg', array(__CLASS__, 'validate_new_post_msg') );
        register_setting( 'bb_pvm_options', 'bb_pvm_new_comment_subj', array(__CLASS__, 'validate_new_comment_subj') );
        register_setting( 'bb_pvm_options', 'bb_pvm_new_comment_msg', array(__CLASS__, 'validate_new_comment_msg') );
          
		register_setting( 'bb_pvm_options', 'bb_pvm_send_bad_reply', array(__CLASS__, 'validate_send_bad_reply') );
	    register_setting( 'bb_pvm_options', 'bb_pvm_bad_reply_subj', array(__CLASS__, 'validate_bad_reply_subj') );
        register_setting( 'bb_pvm_options', 'bb_pvm_bad_reply_msg', array(__CLASS__, 'validate_bad_reply_msg') );

        register_setting( 'bb_pvm_options', 'bb_pvm_send_attachments', array(__CLASS__, 'validate_send_attachments') );
        
        register_setting( 'bb_pvm_options', 'bb_pvm_attachments', array(__CLASS__, 'validate_attachments') );
        register_setting( 'bb_pvm_options', 'bb_pvm_attachments_num', array(__CLASS__, 'validate_attachments_num') );
        register_setting( 'bb_pvm_options', 'bb_pvm_attachments_size', array(__CLASS__, 'validate_attachments_size') );
        register_setting( 'bb_pvm_options', 'bb_pvm_attachments_allowed', array(__CLASS__, 'validate_attachments_allowed') );
        register_setting( 'bb_pvm_options', 'bb_pvm_attachments_ignored', array(__CLASS__, 'validate_attachments_ignored') );

        register_setting( 'bb_pvm_options', 'bb_pvm_send_rej_att_user', array(__CLASS__, 'validate_send_rej_att_user') );
        register_setting( 'bb_pvm_options', 'bb_pvm_rej_att_user_subj', array(__CLASS__, 'validate_rej_att_user_subj') );
        register_setting( 'bb_pvm_options', 'bb_pvm_rej_att_user_msg', array(__CLASS__, 'validate_rej_att_user_msg') );

        register_setting( 'bb_pvm_options', 'bb_pvm_send_rej_att_admin', array(__CLASS__, 'validate_send_rej_att_admin') );
        register_setting( 'bb_pvm_options', 'bb_pvm_rej_att_admin_subj', array(__CLASS__, 'validate_rej_att_admin_subj') );
        register_setting( 'bb_pvm_options', 'bb_pvm_rej_att_admin_msg', array(__CLASS__, 'validate_rej_att_admin_msg') );

		// Global Settings
		add_settings_section('bb_pvm_options_global', __('Main Settings','pvm'), array(__CLASS__, 'settings_section_main'), 'bb_pvm_options');
		add_settings_field('bb_pvm_options_global_type', __('Messaging Handler','pvm'),
					array(__CLASS__, 'settings_field_type'), 'bb_pvm_options', 'bb_pvm_options_global');
		add_settings_field('bb_pvm_options_global_replyto', __('Reply-To Address','pvm'),
					array(__CLASS__, 'settings_field_replyto'), 'bb_pvm_options', 'bb_pvm_options_global');
		add_settings_field('bb_pvm_options_global_from_email', __('From Address','pvm'),
					array(__CLASS__, 'settings_field_from'), 'bb_pvm_options', 'bb_pvm_options_global');
		add_settings_field('bb_pvm_options_global_send_to_author', __('Send To','pvm'),
					array(__CLASS__, 'settings_field_send_to_author'), 'bb_pvm_options', 'bb_pvm_options_global');
	
        add_settings_field('bb_pvm_options_global_send_bad_reply', __('Send Bad Reply notification','pvm'),
                          array(__CLASS__, 'settings_field_send_bad_reply'), 'bb_pvm_options', 'bb_pvm_options_global');
    
		add_settings_section('bb_pvm_options_handleroptions', false, array(__CLASS__, 'settings_section_handler'), 'bb_pvm_options');
        // Note: title is false so that we can handle it ourselves
		foreach ( pvm::get_connectors() as $connector ) {
			if ( ! is_callable( $connector, 'register_settings' ) ) {
				continue;
			}
			$connector->register_settings();
		}
        add_settings_section('bb_pvm_options_attachments', __('Attachment options','pvm'), array(__CLASS__, 'settings_section_attachments'), 'bb_pvm_options');

        add_settings_field('bb_pvm_options_send att_attachments', __('Send attachments in the mail notifications','pvm'),
                          array(__CLASS__, 'settings_field_send_attachments'), 'bb_pvm_options', 'bb_pvm_options_attachments');

        add_settings_field('bb_pvm_options_att_attachments', __('Accept attachments in the mail replies','pvm'),
                          array(__CLASS__, 'settings_field_attachments'), 'bb_pvm_options', 'bb_pvm_options_attachments');

        add_settings_field('bb_pvm_options_att_attachments_num', __('Maximum number of attachments in the mail replies','pvm'),
                          array(__CLASS__, 'settings_field_attachments_num'), 'bb_pvm_options', 'bb_pvm_options_attachments');
        
        add_settings_field('bb_pvm_options_att_attachments_size', __('Maximum size of attachment in the mail replies','pvm'),
                          array(__CLASS__, 'settings_field_attachments_size'), 'bb_pvm_options', 'bb_pvm_options_attachments');
        
        add_settings_field('bb_pvm_options_att_attachments_ignored', __('Ignore attachments of this type','pvm'),
                          array(__CLASS__, 'settings_field_attachments_ignored'), 'bb_pvm_options', 'bb_pvm_options_attachments');
        
        add_settings_field('bb_pvm_options_att_attachments_allowed', __('Accept attachments of this type','pvm'),
                          array(__CLASS__, 'settings_field_attachments_allowed'), 'bb_pvm_options', 'bb_pvm_options_attachments');
        
        add_settings_field('bb_pvm_options_att_send_rej_att_user', __('Send notification on rejected attachment to the poster','pvm'),
                          array(__CLASS__, 'settings_field_send_rej_att_user'), 'bb_pvm_options', 'bb_pvm_options_attachments');

        add_settings_field('bb_pvm_options_att_send_rej_att_admin', __('Send notification on rejected attachment to the admin','pvm'),
                          array(__CLASS__, 'settings_field_send_rej_att_admin'), 'bb_pvm_options', 'bb_pvm_options_attachments');



        add_settings_section('bb_pvm_options_messages', __('Notification messages','pvm'), array(__CLASS__, 'settings_section_messages'), 'bb_pvm_options');
		add_settings_field('bb_pvm_options_messages_bad_reply_subj',__('Bad Reply Subject','pvm'),
                          array(__CLASS__, 'settings_field_bad_reply_subj'), 'bb_pvm_options', 'bb_pvm_options_messages');
        add_settings_field('bb_pvm_options_messages_bad_reply_msg', __('Bad Reply Message','pvm'),
                          array(__CLASS__, 'settings_field_bad_reply_msg'), 'bb_pvm_options', 'bb_pvm_options_messages');

 		add_settings_field('bb_pvm_options_messages_rej_att_subj_user',__('Rejected Attachment Subject for user','pvm'),
                          array(__CLASS__, 'settings_field_rej_att_subj_user'), 'bb_pvm_options', 'bb_pvm_options_messages');
        add_settings_field('bb_pvm_options_messages_rej_att_msg_user',__('Rejected Attachment Message for user','pvm'),
                          array(__CLASS__, 'settings_field_rej_att_msg_user'), 'bb_pvm_options', 'bb_pvm_options_messages');
        add_settings_field('bb_pvm_options_messages_rej_att_reply_subj_admin',__('Rejected Attachment Subject for admin','pvm'),
                          array(__CLASS__, 'settings_field_rej_att_subj_admin'), 'bb_pvm_options', 'bb_pvm_options_messages');
        add_settings_field('bb_pvm_options_messages_rej_att_reply_msg_admin',__('Rejected Attachment Message for admin','pvm'),
                          array(__CLASS__, 'settings_field_rej_att_msg_admin'), 'bb_pvm_options', 'bb_pvm_options_messages');

		add_settings_section('bb_pvm_options_messagesWP', __('Notification messages for WordPress','pvm'), array(__CLASS__, 'settings_section_messagesWP'), 'bb_pvm_options');
		add_settings_field('bb_pvm_options_messages_new_post_subj',__('New Post Subject','pvm'),
				          array(__CLASS__, 'settings_field_new_post_subj'), 'bb_pvm_options', 'bb_pvm_options_messagesWP');
        add_settings_field('bb_pvm_options_messages_new_post_msg', __('New Post Message','pvm'),
				          array(__CLASS__, 'settings_field_new_post_msg'), 'bb_pvm_options', 'bb_pvm_options_messagesWP');
		add_settings_field('bb_pvm_options_messages_new_comment_subj',__('New Comment Subject','pvm'),
				          array(__CLASS__, 'settings_field_new_comment_subj'), 'bb_pvm_options', 'bb_pvm_options_messagesWP');
        add_settings_field('bb_pvm_options_messages_new_comment_msg', __('New Comment Message','pvm'),
				          array(__CLASS__, 'settings_field_new_comment_msg'), 'bb_pvm_options', 'bb_pvm_options_messagesWP');
	        
		if (is_plugin_active('bbpress/bbpress.php')) {
			 add_settings_section('bb_pvm_options_messagesBBP', __('Notification messages bbPress','pvm'), array(__CLASS__, 'settings_section_messagesBBP'), 'bb_pvm_options');
             add_settings_field('bb_pvm_options_messages_new_topic_subj',__('New Topic Subject','pvm'),
                                array(__CLASS__, 'settings_field_new_topic_subj'), 'bb_pvm_options', 'bb_pvm_options_messagesBBP');
             add_settings_field('bb_pvm_options_messages_new_topic_msg', __('New Topic Message','pvm'),
                                array(__CLASS__, 'settings_field_new_topic_msg'), 'bb_pvm_options', 'bb_pvm_options_messagesBBP');
             add_settings_field('bb_pvm_options_messages_new_topic_notice', __('New Topic Notice','pvm'),
                                array(__CLASS__, 'settings_field_new_topic_notice'), 'bb_pvm_options', 'bb_pvm_options_messagesBBP');
             add_settings_field('bb_pvm_options_messages_new_reply_subj',__('New Reply Subject','pvm'),
                                array(__CLASS__, 'settings_field_new_reply_subj'), 'bb_pvm_options', 'bb_pvm_options_messagesBBP');
             add_settings_field('bb_pvm_options_messages_new_reply_msg', __('New Reply Message','pvm'),
                                array(__CLASS__, 'settings_field_new_reply_msg'), 'bb_pvm_options', 'bb_pvm_options_messagesBBP');
             add_settings_field('bb_pvm_options_messages_new_reply_notice', __('New Reply Notice','pvm'),
                                array(__CLASS__, 'settings_field_new_reply_notice'), 'bb_pvm_options', 'bb_pvm_options_messagesBBP');
             add_settings_field('bb_pvm_options_topic_autosubscribe',__('Autosubscribe to the Topic on Reply ','pvm'),
                                array(__CLASS__, 'settings_field_topic_autosubscribe'), 'bb_pvm_options', 'bb_pvm_options_messagesBBP');
         }
        if (class_exists('Group_Activity_Subscription'))
            add_settings_field('bb_pvm_options_messages_group_notice', __('Group Subscriptions Notice','pvm'),
                                array(__CLASS__, 'settings_field_group_notice'), 'bb_pvm_options', 'bb_pvm_options_messagesBBP');

		pvm_Manager::register_default_settings();
	}

	/**
	 * Add our menu item
	 *
	 * @wp-action admin_menu
	 * @wp-action network_admin_menu
	 */
	public static function register_menu() {
		if ( pvm::is_network_mode() ) {
			$parent = 'settings.php';
		}
		else {
			$parent = 'options-general.php';
		}
		add_submenu_page( $parent, _x('bbPress Post Via Mail', 'page title', 'pvm'), _x('bbPress Post Via Mail', 'menu title', 'pvm'), 'manage_options', 'bb_pvm_options', array(__CLASS__, 'admin_page'));
	}

	/**
	 * Print the content
	 */
	public static function admin_page() {
		$action = 'options.php';
		if ( pvm::is_network_mode() ) {
			$action = 'settings.php?page=bb_pvm_options';
		}
	?>
		<div class="wrap">
			<h2><?php _e('bbPress Post Via Mail Options', 'pvm') ?></h2>
			<form method="post" action="<?php echo esc_attr( $action ) ?>">
				<?php settings_fields('bb_pvm_options') ?>
				<?php do_settings_sections('bb_pvm_options') ?>
				<?php submit_button() ?>
			</form>
		</div>

		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				var clearForm = function () {
					// Replace the title and form with the contents
					var $header = $('#bbsub-handlersettings-header');
					var $table = $header.next();
					if ( $table.is( '.form-table' ) ) {
						$table.remove();
					}
					$header.remove();

					var $error = $('#bbsub-handlersettings-error');
					if ( $error.length ) {
						$error.remove();
					}
				};
				$('#bb_pvm_options_global_type').on('change', function (e) {
					$('#bb_pvm_options_global_type').after(' <img src="<?php echo esc_js( esc_url( admin_url( 'images/loading.gif' ) ) ); ?>" id="bbsub-loading" />' );
					$.ajax({
						url: ajaxurl,
						data: {
							action: 'bb_pvm_handler_section',
							handler: $(this).val()
						},
						success: function (response) {
							clearForm();

							$('#bbsub-handlersettings-insert').after(response);
							$('#bbsub-loading').remove();
						},
						error: function (response) {
							clearForm();

							$('#bbsub-handlersettings-insert').after(response.responseText);
							$('#bbsub-loading').remove();
						}
					});
				});
			})
		</script>
	<?php
	}

	/**
	 * Handle option saving in the network admin
	 *
	 * Alas, the network admin doesn't include an options handler, so we need
	 * to use our own here isntead.
	 *
	 * @wp-action load-settings_page_bb_pvm_options
	 */
	public static function handle_save_on_network() {
		if ( ! pvm::is_network_mode() ) {
			return;
		}

		if ( empty( $_POST ) || empty( $_REQUEST['action'] ) || $_REQUEST['action'] !== 'update' ) {
			return;
		}

		$option_page = 'bb_pvm_options';
		$capability = apply_filters( "option_page_capability_{$option_page}", 'manage_network_options' );

		if ( !current_user_can( $capability ) )
			wp_die( __( 'Cheatin&#8217; uh?' ), 403 );

		check_admin_referer( $option_page . '-options' );

		$whitelist_options = apply_filters( 'whitelist_options', array() );
		if ( !isset( $whitelist_options[ $option_page ] ) )
			wp_die( __( '<strong>ERROR</strong>: options page not found.' ) );

		$options = $whitelist_options[ $option_page ];

		foreach ( $options as $option ) {
			if ( $unregistered )
				_deprecated_argument( 'options.php', '2.7', sprintf( __( 'The <code>%1$s</code> setting is unregistered. Unregistered settings are deprecated. See http://codex.wordpress.org/Settings_API' ), $option, $option_page ) );

			$option = trim( $option );
			$value = null;
			if ( isset( $_POST[ $option ] ) ) {
				$value = $_POST[ $option ];
				if ( ! is_array( $value ) )
					$value = trim( $value );
				$value = wp_unslash( $value );
			}
			update_site_option( $option, $value );
		}

		/**
		 * Handle settings errors and return to options page
		 */
		// If no settings errors were registered add a general 'updated' message.
		if ( !count( get_settings_errors() ) )
			add_settings_error('general', 'settings_updated', __('Settings saved.'), 'updated');
		set_transient('settings_errors', get_settings_errors(), 30);

		/**
		 * Redirect back to the settings page that was submitted
		 */
		$goback = add_query_arg( 'settings-updated', 'true',  wp_get_referer() );
		wp_redirect( $goback );
		exit;
	}

	/**
	 * @wp-action network_admin_notices
	 */
	public static function network_settings_errors() {
		if ( $GLOBALS['plugin_page'] === 'bb_pvm_options' ) {
			require(ABSPATH . 'wp-admin/options-head.php');
		}
	}

	/**
	 * Handle an AJAX request for the handler section
	 *
	 * @wp-action wp_ajax_bb_pvm_handler_section
	 */
	public static function ajax_handler_section() {
		try {
			if (!isset($_REQUEST['handler'])) {
				throw new Exception(__('Invalid handler type', 'pvm'));
			}

			// Setup the handler settings for the newly selected handler
			$handler = self::validate_type($_REQUEST['handler']);
			if (!$handler) {
				throw new Exception(__('Invalid handler', 'pvm'));
			}

			$options = pvm::get_option('bb_pvm_handler_options', array());
			// validate_type() will set this flag if the type isn't equal to
			// the current one
			if (self::$wipe_handler_options) {
				$options = array();
			}
			self::register_handler_settings_fields('bb_pvm_options', 'bb_pvm_options_handleroptions', $handler, $options);
			self::$registered_handler_settings = true;

			// Now, output the section
			$page = 'bb_pvm_options';
			$section = 'bb_pvm_options_handleroptions';


			global $wp_settings_fields;
			self::settings_section_handler();

			if ( !isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section]) )
				die();

			echo '<table class="form-table">';
			do_settings_fields($page, $section);
			echo '</table>';

			// Special field to ensure we don't wipe settings fully
			echo '<input type="hidden" name="bb_pvm_used_ajax" value="1" />';
		}
		catch (Exception $e) {
			header('X-Excepted: true', true, 500);
			echo '<div class="error" style="width:317px" id="bbsub-handlersettings-error"><p>' . $e->getMessage() . '</p></div>';
		}

		die();
	}

	/**
	 * Print description for the main settings section
	 *
	 * @see self::init()
	 */
	public static function settings_section_main() {
		// echo '<p>' . __('Main settings for the plugin', 'pvm') . '</p>';	
	}
	/**
         * Print description for the main settings section
       	 *
         * @see self::init()
         */
    public static function settings_section_attachments() {
    echo '<p>' . __('Allow to post attachments along with replies', 'pvm') . '</p>';
    }
    public static function settings_section_messages() {
    echo '<p>' . __('Clear to restore defaults, then click on Save twice', 'pvm') . '</p>';
    }
	public static function settings_section_messagesWP() {
		 echo '<p>' . __('Clear to restore defaults, then click on Save twice', 'pvm') . '</p>';

        }
	 public static function settings_section_messagesBBP() {
		echo '<p>' . __('Clear to restore defaults, then click on Save twice', 'pvm') . '</p>';

              
        }
	/**
	 * Print field for the handler type
	 *
	 * @see self::init()
	 */
	public static function settings_field_type() {
		$current = pvm::get_option('bb_pvm_handler_type', false);
		$available = pvm::get_handlers();

		if (empty($available)) {
			echo '<p class="error">' . __('No handlers are available!', 'pvm') . '</p>';
		}

		echo '<select name="bb_pvm_handler_type" id="bb_pvm_options_global_type">';
		echo '<option>' . _x('None', 'handler', 'pvm') . '</option>';
		foreach ($available as $type => $class) {
			echo '<option value="' . esc_attr($type) . '"' . selected($current, $type) . '>' . esc_html($class::get_name()) . '</option>';
		}
		echo '</select>';
	}

	/**
	 * Validate the handler type
	 *
	 * Ensures that the selected type can actually be selected
	 * @param string $input Selected class name
	 * @return string|bool Selected class name if valid, otherwise false
	 */
	public static function validate_type($input) {
		if ( in_array( $input, array_keys(pvm::get_handlers()) ) ) {
			if ($input !== pvm::get_option('bb_pvm_handler_type', false) && empty($_POST['bb_pvm_used_ajax'])) {
				self::$wipe_handler_options = true;
			}
			return $input;
		}

		add_settings_error(
			'bb_pvm_handler_type',
			'bb_pvm_handler_invalid',
			__('The selected handler is invalid', 'pvm')
		);
		return false;
	}

	/**
	 * Print field for the reply-to address
	 *
	 * @see self::init()
	 */
	public static function settings_field_replyto() {
		$current = pvm::get_option('bb_pvm_replyto', '');

		echo '<input type="text" name="bb_pvm_replyto" class="regular-text" value="' . esc_attr($current) . '" />';
		echo '<p class="description">';
		_e('pvm will append an authentication token to this email before sending.', 'pvm');
		echo '</p>';
	}

	/**
	 * Validate the reply-to address
	 *
	 * Ensures that the reply-to address is a valid formattable email address
	 * @param string $input New reply-to address
	 * @return string Updated reply-to address if valid, otherwise the old address
	 */
	public static function validate_replyto($input) {
		$oldvalue = pvm::get_option('bb_pvm_replyto', '');

		if ( strpos( $input, '+' ) !== false) {
			add_settings_error(
				'bb_pvm_replyto',
				'bb_pvm_replyto_invalid',
				__('The reply-to address must not contain a plus address section', 'pvm')
			);
			return $oldvalue;
		}

		list( $user_part, $host_part ) = explode( '@', $input );
		$user_part .= '+%1$s-%2$d-%3$d-%4$s';
		$address = $user_part . '@' . $host_part;

		// Test it out!
		$hash = pvm::get_hash('5', wp_get_current_user(), '42');
		$formatted = sprintf($address, 5, 42, wp_get_current_user()->ID, $hash);

		// Check that the resulting email is valid
		if (!is_email($formatted)) {
			add_settings_error(
				'bb_pvm_replyto',
				'bb_pvm_replyto_invalid',
				__('The reply-to address must be a valid address', 'pvm')
			);
			return $oldvalue;
		}

		return $input;
	}

	/**
	 * Print field for the Send to Author checkbox
	 *
	 * @see self::init()
	 */
	public static function settings_field_send_to_author() {
		$current = pvm::get_option('bb_pvm_send_to_author', '');

		echo '<label><input type="checkbox" name="bb_pvm_send_to_author" ' . checked($current, true, false) . ' /> ';
		_e('Send a notification to the reply author', 'pvm');
		echo '</label>';
	}

	/**
	 * Validate the Send to Author checkbox
	 *
	 * @param string $input
	 * @return string
	 */
	public static function validate_send_to_author($input) {
		return (bool) $input;
	}

	/**
	 * Print field for the reply-to address
	 *
	 * @see self::init()
	 */
	public static function settings_field_from() {
		$current = pvm::get_option('bb_pvm_from_email', '');

		echo '<input type="email" name="bb_pvm_from_email" class="regular-text" value="' . esc_attr($current) . '" />';
		echo '<p class="description">' . __('Leave blank to use the default email address (<code>wordpress@</code>)', 'pvm') . '</p>';
	}
	/**
         * Print field for the new topic message subject
         *
         * @see self::init()
         */
        public static function settings_field_new_topic_subj() {
            $current = pvm::get_option('bb_pvm_new_topic_subj', '');
            if (!strlen($current))
			    $current=__('{author} started a new topic {title} on forum {forum}','pvm');
            echo '<input type="text" name="bb_pvm_new_topic_subj" class="regular-text" value="' . esc_attr($current) . '" />';
            echo '<p class="description">' . __('You can use following tags: {site} {author} {forum} {title} - they will be substituted', 'pvm') . '</p>';
        }

        public static function validate_new_topic_subj($input) {
                return $input;
        }

	/**
         * Print field for the new topic message content
         *
         * @see self::init()
         */
        public static function settings_field_new_topic_msg() {
                $current = pvm::get_option('bb_pvm_new_topic_msg', '');
                if (!strlen($current)) {
                   $current  = "{content}\n---\n";
                   $current .= __('If you want to reply to the topic, send reply to this email directly or view <a href="{link}">the topic online</a>','pvm');
                }
                echo '<textarea name="bb_pvm_new_topic_msg" class="large-text" rows="8" id="bb_pvm_new_topic_msg" >' . esc_attr($current) . '</textarea>';
		echo '<p class="description">' . __('You can use following tags: {author} {site} {forum} {title} {content} {link} - they will be substituted', 'pvm') . '</p>';
        }

	public static function validate_new_topic_msg($input) {
                return $input;
        }
     public static function settings_field_new_topic_notice() {
                $current = pvm::get_option('bb_pvm_new_topic_notice', '');
                if (!strlen($current)) {
                   $current = __('You are receiving this email because you subscribed to the forum {forum} on site {site}','pvm');
                   $current .= "\n". __('Login and visit the forum if you wish to unsubscribe from these emails.','pvm');
                }
                echo '<textarea name="bb_pvm_new_topic_notice" class="large-text" rows="8" id="bb_pvm_new_topic_notice" >' . esc_attr($current) . '</textarea>';
        echo '<p class="description">' . __('You can use following tags: {author} {site} {forum} {title} {content} {link} - they will be substituted', 'pvm') . '</p>';
        }

    public static function validate_new_topic_notice($input) {
                return $input;
        }
    public static function settings_field_new_reply_notice() {
                $current = pvm::get_option('bb_pvm_new_reply_notice', '');
                if (!strlen($current)) {
                   $current = __('You are receiving this email because you subscribed to the topic {title} on forum {forum}','pvm');
                   $current .= "\n". __('Login and visit the forum if you wish to unsubscribe from these emails.','pvm');
                }
                echo '<textarea name="bb_pvm_new_reply_notice" class="large-text" rows="8" id="bb_pvm_new_reply_notice" >' . esc_attr($current) . '</textarea>';
        echo '<p class="description">' . __('You can use following tags: {author} {site} {forum} {title} {content} {link} - they will be substituted', 'pvm') . '</p>';
        }

    public static function validate_new_reply_notice($input) {
                return $input;
        }

    public static function settings_field_group_notice() {
                $current = pvm::get_option('bb_pvm_group_notice', '');
                if (!strlen($current)) {
                   $current = __( 'Your email setting for this group is: {group_status}', 'pvm' );
                   $current .= "\n" . sprintf( __( 'To change your email setting for this group, please log in and go <a href="{settings_link}">settings,</a>','pvm'));
                }
                echo '<textarea name="bb_pvm_group_notice" class="large-text" rows="8" id="bb_pvm_group_notice" >' . esc_attr($current) . '</textarea>';
        echo '<p class="description">' . __('You can use following tags: {group_status} {settings_link} - they will be substituted', 'pvm') . '</p>';
        }

    public static function validate_group_notice($input) {
                return $input;
        }


	/**
         * Print field for the new reply message subject
         *
         * @see self::init()
         */
        public static function settings_field_new_reply_subj() {
                $current = pvm::get_option('bb_pvm_new_reply_subj', '');
		if (!strlen($current))
                        $current=__('{author} posted new reply in topic {title} on forum {forum}','pvm');
                echo '<input type="text" name="bb_pvm_new_reply_subj" class="regular-text" value="' . esc_attr($current) . '" />';
                echo '<p class="description">' . __('You can use following tags: {author} {site} {forum} {title}', 'pvm') . '</p>';
        }

        public static function validate_new_reply_subj($input) {
                return $input;
        }

	/**
         * Print field for the new reply message content
         *
         * @see self::init()
         */
	public static function settings_field_new_reply_msg() {
        $current = pvm::get_option('bb_pvm_new_reply_msg', '');
		if (!strlen($current)) {
            $current  = "{content}\n---\n";
            $current .= __('If you also want to reply to this topic, send reply to this email directly or view <a href="{link}">the topic and reply online</a>','pvm');
        }
        echo '<textarea name="bb_pvm_new_reply_msg" class="large-text" rows="8" id="bb_pvm_new_reply_msg" >' . esc_attr($current) . '</textarea>';
        echo '<p class="description">' . __('You can use following tags: {author} {site} {forum} {title} {content} {link} - they will be substituted', 'pvm') . '</p>';
    }
	public static function validate_new_reply_msg($input) {
                return $input;
        }
	/*
         * Print field for the new post message subject
         *
         * @see self::init()
         */
        public static function settings_field_new_post_subj() {
                $current = pvm::get_option('bb_pvm_new_post_subj', '');
		if (!strlen($current))
                        $current=__('New post on site {site}','pvm');
                echo '<input type="text" name="bb_pvm_new_post_subj" class="regular-text" value="' . esc_attr($current) . '" />';
                echo '<p class="description">' . __('You can use following tags: {author} {site} {title} - they will be substituted', 'pvm') . '</p>';
        }

        public static function validate_new_post_subj($input) {
        }

	/**
         * Print field for the new post message content
         *
         * @see self::init()
         */
    public static function settings_field_topic_autosubscribe() {
        $current = pvm::get_option('bb_pvm_topic_autosubscribe', '');

        echo '<label><input type="checkbox" name="bb_pvm_topic_autosubscribe" ' . checked($current, true, false) . ' /> ';
        _e('Subscribe to forum topic replied to via mail', 'pvm');
        echo '</label>';
    }

    /**
     * Validate the Send to Author checkbox
     *
     * @param string $input
     * @return string
     */
    public static function validate_topic_autosubscribe($input) {
        return (bool) $input;
    }
    
    // Attachments settings
    public static function settings_field_send_attachments() {
        $current = pvm::get_option('bb_pvm_send_attachments', '');
        echo '<label><input type="checkbox" name="bb_pvm_send_attachments" ' . checked($current, true, false) . ' /> ';
        _e('Send attachments to the posts, topic and replies in mail notifications.', 'pvm');
        echo '</label>';
    }

    public static function validate_send_attachments($input) {
        return (bool) $input;
    }


    public static function settings_field_attachments() {
        $current = pvm::get_option('bb_pvm_attachments', '');

        echo '<label><input type="checkbox" name="bb_pvm_attachments" ' . checked($current, true, false) . ' /> ';
        _e('Allow attachments to be posted via mail.', 'pvm');
        echo '</label>';
    }

    public static function validate_attachments($input) {
        return (bool) $input;
    }

    public static function settings_field_send_rej_att_admin() {
        $current = pvm::get_option('bb_pvm_send_rej_att_admin', '');

        echo '<label><input type="checkbox" name="bb_pvm_send_rej_att_admin" ' . checked($current, true, false) . ' /> ';
        _e('Send notification to admin whenever a user tries to attach file type not listed as allowed.', 'pvm');
        echo '</label>';
    }

    public static function validate_send_rej_att_admin($input) {
        return (bool) $input;
    }

    public static function settings_field_send_rej_att_user() {
        $current = pvm::get_option('bb_pvm_send_rej_att_user', '');

        echo '<label><input type="checkbox" name="bb_pvm_send_rej_att_user" ' . checked($current, true, false) . ' /> ';
        _e('Send notification to user on the attemp to attach file type not listed as allowed.', 'pvm');
        echo '</label>';
    }

    public static function validate_send_rej_att_user($input) {
        return (bool) $input;
    }


    public static function settings_field_attachments_num() {
        $current = pvm::get_option('bb_pvm_attachments_num', '');
        echo '<input type="text" name="bb_pvm_attachments_num" size="4"  value="' . esc_attr($current) . '" />    ';
        _e('Allowed number of attachments to be posted per mail.', 'pvm');
    }

    public static function validate_attachments_num($input) {
        return $input;
    }
    public static function settings_field_attachments_size() {
        $current = pvm::get_option('bb_pvm_attachments_size', '');
        echo '<input type="text" name="bb_pvm_attachments_size" size="8"  value="' . esc_attr($current) . '" />   ';
        _e('Allowed size of each attachments to be posted per mail in KiB.', 'pvm');
    }
    public static function validate_attachments_size($input) {
        return $input;
    }

    
    public static function settings_field_attachments_allowed() {
        $current = pvm::get_option('bb_pvm_attachments_allowed', '');
        if (!strlen($current)) {
            $current = "application/pdf\ntext/plain\ntext/html\napplication/msword\n";
            $current.= "image/bmp\nimage/x-windows-bmp\nnimage/jpeg\naudio/mpeg3\nvideo/mpeg\naudio/mpeg\nimage/tiff\naudio/wav";
        }
        echo '<textarea name="bb_pvm_attachments_allowed" class="large-text" rows="8" id="bb_pvm_attachments_allowed" >' . esc_attr($current) . '</textarea>';
        _e('Type of attachments allowed. Only attachments matching listes MIME types or file extentions will be accepted.','pvm');
        echo '<br>';
        _e('Provide MIME types or file extensions, one item per line. Comments after # ignored', 'pvm');
        echo '<br>';
        _e('Example:','pvm');
        echo ' image/jpeg # jpeg image or *.jpeg # image';
        echo '<br>';

    }

    public static function validate_attachments_allowed($input) {
        return $input;
    }

    public static function settings_field_attachments_ignored() {
        $current = pvm::get_option('bb_pvm_attachments_ignored', '');
        if (!strlen($current)) {
            $current = "*.exe #executable files\n";
        }
        echo '<textarea name="bb_pvm_attachments_ignored" class="large-text" rows="8" id="bb_pvm_attachments_ignored" >' . esc_attr($current) . '</textarea>';
        _e('Type of attachments to be ignored. They will be silently rejected without any notification.','pvm.');
        echo '<br>';
        _e('Provide MIME types or file extensions, one item per line. Comments after # ignored', 'pvm');
        echo '<br>';
        _e('Example:','pvm');
        echo ' application/pgp-signature # digital signature';
        echo '*.vcf #vCard file';

        echo '<br>';
        _e('Attachments listed here will be excluded prior to analizing the <b>allowed</b> list below','pvm');
    }

    /**
     * Validate the Attachments checkbox
     *
     * @param string $input
     * @return string
     */
    public static function validate_attachments_ignored($input) {
        return $input;
    }


    public static function settings_field_rej_att_subj_user() {
        $current = pvm::get_option('bb_pvm_rej_att_user_subj', '');
        if (!strlen($current))
            $current=__('Some attachments to your reply to post {title} on {site} have been rejected','pvm');
            echo '<input type="text" name="bb_pvm_rej_att_user_subj" class="regular-text" value="' . esc_attr($current) . '" />';
            echo '<p class="description">' . __('You can use following tags: {author} {site} {title} - they will be substituted', 'pvm') . '</p>';
        }

        public static function validate_rej_att_user_subj($input) {
            return $input;
    }

    /**
         * Print field for the new comment message content
         *
         * @see self::init()
         */
    public static function settings_field_rej_att_msg_user() {
        $current = pvm::get_option('bb_pvm_rej_att_user_msg', '');
        if (!strlen($current)) {
            $current = __("You sent a reply to the post {title} on {site} with wrong attachments.\nThey were rejected because of following errors:\n\n{error}\n\n",'pvm');
            $current.= __("Correct them and send your reply again.",'pvm');
        }
        echo '<textarea name="bb_pvm_rej_att_user_msg" class="large-text" rows="8" id="bb_pvm_rej_att_user_msg" >' . esc_attr($current) . '</textarea>';
        echo '<p class="description">' . __('You can use following tags: {author} {site} {title} {error} - they will be substituted', 'pvm') . '</p>';
    }
        
    public static function validate_rej_att_user_msg($input) {
                return $input;
    }
    public static function settings_field_rej_att_subj_admin() {
        $current = pvm::get_option('bb_pvm_rej_att_admin_subj', '');
        if (!strlen($current))
            $current=__('Some attachments to reply of {author} to post {title} on {site} have been rejected','pvm');
            echo '<input type="text" name="bb_pvm_rej_att_admin_subj" class="regular-text" value="' . esc_attr($current) . '" />';
            echo '<p class="description">' . __('You can use following tags: {author} {site} {title} - they will be substituted', 'pvm') . '</p>';
        }

        public static function validate_rej_att_admin_subj($input) {
            return $input;
    }

    public static function settings_field_rej_att_msg_admin() {
        $current = pvm::get_option('bb_pvm_rej_att_admin_msg', '');
        if (!strlen($current)) {
            $current = __("User {user} replied to the post {title} on {site} with wrong attachments.\nThey were rejected because of following errors:\n\n{error}\n\n",'pvm');
            $current .=__('Consider changing some settings for attachments in plugin settings panel by adding allowed attachment types.','pvm');
        }
        echo '<textarea name="bb_pvm_rej_att_admin_msg" class="large-text" rows="8" id="bb_pvm_rej_att_admin_msg" >' . esc_attr($current) . '</textarea>';
        echo '<p class="description">' . __('You can use following tags: {author} {site} {title} {content} {error} - they will be substituted', 'pvm') . '</p>';
    }
        
    public static function validate_rej_att_admin_msg($input) {
        return $input;
    }
    //________

    public static function settings_field_new_post_msg() {
        $current = pvm::get_option('bb_pvm_new_post_msg', '');
        if (!strlen($current)) {
            $current = __("{content}\n---\nReply to this email directly or view it online:\n{link}\n\n",'pvm');
            $current .=__("You are receiving this email because you subscribed to it. Login and change your user settings to unsubscribe from these emails.",'pvm');
        }
        echo '<textarea name="bb_pvm_new_post_msg" class="large-text" rows="8" id="bb_pvm_new_post_msg" >' . esc_attr($current) . '</textarea>';
        echo '<p class="description">' . __('You can use following tags: {author} {site} {title} {content} {link} - they will be substituted', 'pvm') . '</p>';
    }
	public static function validate_new_post_msg($input) {
		return $input;
    }

	/**
         * Print field for the new comment message subject
         *
         * @see self::init()
         */
        public static function settings_field_new_comment_subj() {
                $current = pvm::get_option('bb_pvm_new_comment_subj', '');
		if (!strlen($current))
                $current=__('New comment to post {title} on {site}','pvm');
                echo '<input type="text" name="bb_pvm_new_comment_subj" class="regular-text" value="' . esc_attr($current) . '" />';
                echo '<p class="description">' . __('You can use following tags: {author} {site} {title} - they will be substituted', 'pvm') . '</p>';
        }

        public static function validate_new_comment_subj($input) {
                return $input;
        }

	/**
         * Print field for the new comment message content
         *
         * @see self::init()
         */
	public static function settings_field_new_comment_msg() {
                $current = pvm::get_option('bb_pvm_new_comment_msg', '');
		if (!strlen($current)) {
                   $current  = __("{content}\n---\nReply to this email directly or view it online:\n{link}\n\n",'pvm');
                   $current .= __("You are receiving this email because you subscribed to it. Login and change your user settings to unsubscribe from these emails.",'pvm');
                }
                echo '<textarea name="bb_pvm_new_comment_msg" class="large-text" rows="8" id="bb_pvm_new_comment_msg" >' . esc_attr($current) . '</textarea>';
                echo '<p class="description">' . __('You can use following tags: {author} {site} {title} {content} {link}- they will be substituted', 'pvm') . '</p>';
        }
	public static function validate_new_comment_msg($input) {
                return $input;
        }
	/* Print field for the Send Bad Reply notofication to Author checkbox
         *
         * @see self::init()
         */
        public static function settings_field_send_bad_reply() {
                $current = pvm::get_option('bb_pvm_send_bad_reply', '');

                echo '<label><input type="checkbox" name="bb_pvm_send_bad_reply" ' . checked($current, true, false) . ' /> ';
                _e('Send a notification about bad reply to the reply author', 'pvm');
                echo '</label>';
        }

        /**
         * Validate the Send to Author checkbox
         *
         * @param string $input
         * @return string
         */
        public static function validate_send_bad_reply($input) {
                return (bool) $input;
        }

	/**
         * Print field for the bad reply message subject
         *
         * @see self::init()
         */
        public static function settings_field_bad_reply_subj() {
                $current = pvm::get_option('bb_pvm_bad_reply_subj', '');
		if (!strlen($current))
                        $current=__('Bad reply {title} on site {site}','pvm');
                echo '<input type="text" name="bb_pvm_bad_reply_subj" class="regular-text" value="' . esc_attr($current) . '" />';
                echo '<p class="description">' . __('You can use following tags: {author} {site} {title} - they will be substituted', 'pvm') . '</p>';
        }

        public static function validate_bad_reply_subj($input) {
                return $input;
        }

	/**
         * Print field for the new reply message content
         *
         * @see self::init()
         */
	public static function settings_field_bad_reply_msg() {
                $current = pvm::get_option('bb_pvm_bad_reply_msg', '');
		if (!strlen($current)) {
           $current = __("Hi {user},\nSomeone just tried to post to the {title} topic from {from},\n",'pvm');
           $current.= __("but were unable to authenticate it as the message send by you. \n",'pvm');
           $current.= __("Error message: {error}\n",'pvm');
           $current.= __("If you recently tried to reply to this topic, try replying to the original topic message again not to the reply notification.",'pvm');
	 $current.= "\n";   
           $current.= __("If that does not work, post on the topic {link} via your browser and tell the admin about the problem.\n",'pvm');
	   $current.= "\n";	 
           $current.= __("The admins at {site}.\n\n",'pvm');
                }
                echo '<textarea name="bb_pvm_bad_reply_msg" class="large-text" rows="8" id="bb_pvm_new_reply_msg" >' . esc_attr($current) . '</textarea>';
                echo '<p class="description">' . __('You can use following tags: {user} {from} {site} {title} - they will be substituted', 'pvm') . '</p>';
        }
	public static function validate_bad_reply_msg($input) {
                return $input;
        }

	/**
	 * Validate the reply-to address
	 *
	 * Ensures that the reply-to address is a valid formattable email address
	 * @param string $input New reply-to address
	 * @return string Updated reply-to address if valid, otherwise the old address
	 */
	public static function validate_from_email($input) {
		$oldvalue = pvm::get_option('bb_pvm_from_email', '');
        if (!strlen($input))
            return $input;
		// Check that the resulting email is valid
		if (!is_email($input)) {
			add_settings_error(
				'bb_pvm_from_email',
				'bb_pvm_from_invalid',
				__('The from address must be a valid address', 'pvm')
			);
			return $oldvalue;
		}

		return $input;
	}

	public static function settings_section_handler() {
		if (!self::$registered_handler_settings) {
			self::register_handler_settings_fields('bb_pvm_options', 'bb_pvm_options_handleroptions');
			self::$registered_handler_settings = true;
		}

		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			echo '<div id="bbsub-handlersettings-insert"></div>';
		}

		global $wp_settings_fields;

		// If the handler didn't register any options, don't bother to print the
		// title for the section
		$page = 'bb_pvm_options';
		$section = 'bb_pvm_options_handleroptions';
		if ( !isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section]) )
			return;

		echo '<div id="bbsub-handlersettings-header">';
		echo '<h3 id="bbsub-handlersettings-title">' . __('Handler Settings', 'pvm') . '</h3>';

		try {
			$handler = pvm::get_handler_class();
		}
		catch (Exception $e) {
			return;
		}
		$handler::options_section_header();
		echo '</div>';
	}

	/**
	 * Notify the handler to register handler-specific options
	 *
	 * @see self::init()
	 */
	public static function register_handler_settings_fields($group, $section, $handler_type = null, $current = null) {
		if ($current === null) {
			$current = pvm::get_option('bb_pvm_handler_options', array());
		}
		try {
			$handler = pvm::get_handler_class($handler_type);
		}
		catch (Exception $e) {
			return false;
		}
		$handler::register_option_fields($group, $section, $current);
	}

	/**
	 * Validate the handler-specific options via the handler's methods
	 *
	 * @see self::init()
	 */
	public static function validate_handler_options($input) {
		if (self::$wipe_handler_options) {
			return array();
		}

		try {
			$handler = pvm::get_handler_class();
		}
		catch (Exception $e) {
			return array();
		}
		return $handler::validate_options($input);
	}
}
