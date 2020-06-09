<?php

class pvm_Connector_bbPress {
	protected $handler;

	public function __construct( $handler ) {
		$this->handler = $handler;
		remove_action( 'bbp_new_topic',    'bbp_notify_forum_subscribers', 11, 5 );
		remove_action( 'bbp_new_reply',    'bbp_notify_topic_subscribers', 11, 5 );
		add_action( 'bbp_new_topic', array( $this, 'notify_new_topic' ), 20, 4 );
		add_action( 'bbp_new_reply', array( $this, 'notify_on_reply'  ), 20, 5 );
        if ( class_exists( 'BuddyPress' ))
            add_filter( 'bbp_before_record_activity_parse_args', array( $this, 'map_activity_to_group' ),15,1);
		add_action( 'pvm.reply.insert', array( $this, 'handle_insert' ), 20, 2 );
        add_action( 'bp_ass_activity_notification_message', array($this, 'notify_group_forum'),10,2);
	}

	/**
	 * Get a human-readable name for the handler
	 *
	 * This is used for the handler selector and is shown to the user.
	 * @return string
	 */
	public static function get_name() {
		return 'bbPress';
	}
        public function notify_group_forum ($message, $meta) {
            // Return NULL to block notifications sent by Group Email Subscriptions
            return;
        }
        public function send_mail ($users, pvm_Message $message, $notices) {
		$options = $message->get_options();

		$from = pvm::get_from_address();
		if ( $author = $message->get_author() ) {
			$from = sprintf( '%s <%s>', $author, $from );
		}
		$messages = array();
        $data="";
        if ( $text = $message->get_text() ) {
            $data = $text;
            $type = 'text/plain';
        }
        if ( $html = $message->get_html() ) {
            $data = $html;
            $type = 'text/html';
        }
        if (!strlen($data))
        {
            return;
        }
		foreach ($users as $user) {
			$to = $user->user_email;
            $subj = $message->get_subject();
            $reply_to=$message->get_reply_address($user);
			$headers = array();
            $attachments = $message->get_attachments();
			// Set the message ID if we've got one
			if ( ! empty( $options['message-id'] ) ) {
				$headers = array(
					'Name' => 'Message-ID',
					'Value' => $options['message-id'],
				);
			}

			// If this is a reply, set the headers as needed
			if ( ! empty( $options['in-reply-to'] ) ) {
				$original = $options['in-reply-to'];
				if ( is_array( $original ) ) {
					$original = isset( $options['in-reply-to'][ $user->ID ] ) ? $options['in-reply-to'][ $user->ID ] : null;
				}

				if ( ! empty( $original ) ) {
					$headers = array(
						'Name' => 'In-Reply-To',
						'Value' => $original,
					);
				}
			}

			if ( ! empty( $options['references'] ) ) {
				$references = implode( ' ', $options['references'] );
				$headers = array(
					'Name' => 'References',
					'Value' => $references,
				);
			}
            $header[]='Reply-To: '.$reply_to;
            $header[]='Content-Type: '. $type;
			wp_mail( $to, $subj, nl2br($data.$notices[$user->ID]), $header, $attachments );
		}
		
	}
   public function get_post_attachments_paths($post_id) {
        $attachments_paths = array ();
        $args = array( 'post_type' => 'attachment', 'posts_per_page' => -1, 'post_status' =>'any', 'post_parent' => $post_id );
        $attachments = get_posts( $args );
        if ( $attachments ) {
            foreach ( $attachments as $attachment ) {
                $fullsize_path = get_attached_file( $attachment->ID );
                $attachments_paths[] = $fullsize_path;
            }
        }
        return $attachments_paths;
    }

        /**
        * Notify user roles on new topic
        */
	public function notify_new_topic( $topic_id = 0, $forum_id = 0, $anonymous_data = 0, $topic_author = 0) {
        $notices = array();
        $subscribed_users = array();
		$user_ids = bbp_get_forum_subscribers($forum_id, true);
        if (class_exists('Group_Activity_Subscription')) {
           // Get current BP group
           $group = groups_get_current_group();
           if (!empty( $group ) )
               $subscribed_users = groups_get_groupmeta( $group->id, 'ass_subscribed_users' );
        }
        //$debug_export = var_export($user_ids, true);
        //error_log("Notify new topic - forum subscribers ids: ".$debug_export);
        $group_notice = "\n\n".pvm::get_option('bb_pvm_group_notice', '');
        $forum_notice = "\n\n".pvm::get_option('bb_pvm_new_topic_notice', '');
        $forum_notice = str_replace('{title}',bbp_get_topic_title( $topic_id ),$forum_notice);
        $forum_notice = str_replace('{site}',get_option( 'blogname' ),$forum_notice);
        $forum_notice = str_replace('{forum}',bbp_get_forum_title ($forum_id),$forum_notice);
        foreach ($user_ids as $user)
            $notices[$user]=$forum_notice;
        $group_topic_subscribers = array ();
        foreach ($subscribed_users as $key => $group_status) {
            if ($group_status == 'sub' || $group_status == 'supersub') {
                $group_topic_subscribers[] = strval($key);
                $notices[$key] .= $group_notice;
                $settings_link = ass_get_login_redirect_url( trailingslashit( bp_get_group_permalink( $group ) . 'notifications' ), $group_status );
                $notices[$key] = str_replace ('{group_status}',ass_subscribe_translate( $group_status ),$notices[$key]);
                $notices[$key] = str_replace ('{settings_link}', $settings_link,$notices[$key]);
            }
        }
        $user_ids = array_merge($user_ids,$group_topic_subscribers);
        $user_ids = array_unique($user_ids);
        //$debug_export = var_export($user_ids, true);
        //error_log("Notify new topic - forum and group subscribers ids: ".$debug_export);
        if (empty($user_ids)) return false;
		// Get userdata for all users
        $user_ids = array_map(function ($id) {
             return get_userdata($id);
                }, $user_ids);


        $content = get_post_field( 'post_content', $topic_id );
        $send_attachments_in_notification = pvm::get_option('bb_pvm_send_attachments', '');
        if ($send_attachments_in_notification) {
            $attachments_paths = $this -> get_post_attachments_paths( $topic_id );
        }
        // Build email
        $reply_author_name = bbp_get_topic_author_display_name( $topic_id );
        $subject = pvm::get_new_topic_subj();
        $text = pvm::get_new_topic_msg();
        $link = bbp_get_reply_url($topic_id);
        $text = str_replace ('{site}',get_option( 'blogname' ),$text);
        $subject = str_replace ('{site}',get_option( 'blogname' ),$subject);
        $text    = str_replace ('{forum}',bbp_get_forum_title ($forum_id),$text);
        $subject = str_replace ('{forum}',bbp_get_forum_title ($forum_id),$subject);
        $text    = str_replace ('{title}',bbp_get_topic_title( $topic_id ),$text);
        $subject = str_replace ('{title}',bbp_get_topic_title( $topic_id ),$subject);
        $text    = str_replace ('{author}',$reply_author_name,$text);
        $subject = str_replace ('{author}',$reply_author_name,$subject);
        $text    = str_replace ('{link}',$link,$text);
        $subject = str_replace ('{link}',$link,$subject);
        $text    = str_replace ('{content}',$content,$text);
        $subject = str_replace ('{content}',$content,$subject);
        $subject = apply_filters('bb_pvm_email_subject', $subject, 0, $topic_id);
		$options = array(
			'author' => $reply_author_name,
			'id'     => $topic_id,
		);

        $message = new pvm_Message();
        $message->set_subject( $subject );
        $message->set_html( $text);
        $message->set_options( $options );
        $message->set_reply_address_handler( function ( WP_User $user, pvm_Message $message ) use ( $topic_id ) {
                                                return pvm::get_reply_address( $topic_id, $user );
                                           } );
        if ($send_attachments_in_notification)
            $message->set_attachments($attachments_paths);
        $message->set_author( $reply_author_name );
        $this->send_mail($user_ids, $message,$notices);
		do_action( 'bbp_post_notify_topic_subscribers', $topic_id, $user_ids );

	}
    
    public function map_activity_to_group( $args = array() ) {
        //$debug_export = var_export($args, true);
        //error_log("Map activity to group: ".$debug_export);
        if ( !class_exists( 'BuddyPress' ))
            return $args;
        if (array_key_exists ( 'component', $args ) && $args['component'] == buddypress()->groups->id)
            // Already done, returning
            return $args;
        // Get current BP group
        $group = groups_get_current_group();
        if (!empty( $group ) )
            return $args;   // Let it be processed by BuddyPress handle
        // Get the group slug from the link
        $link_parts = explode ("/",$args['primary_link']);
        $parts_num = count($link_parts);
        for ($i=0;$i<$parts_num;$i++)
            if ($link_parts[$i] == buddypress()->groups->id) break;
        if ($i == $parts_num)
            // Reached end of link, not a group forum
            return $args;
        $group_id = groups_get_id($link_parts[$i+1]);  // Get group by slug
        $group = groups_get_group( array( 'group_id' => $group_id ) );
        // Not posting from a BuddyPress group? stop now!
        if ( empty( $group ) )
            return $args;

        // Set the component to 'groups' so the activity item shows up in the group
        $args['component']         = buddypress()->groups->id;

        // Move the forum post ID to the secondary item ID
        $args['secondary_item_id'] = $args['item_id'];

        // Set the item ID to the group ID so the activity item shows up in the group
        $args['item_id']           = $group->id;

        // Update the group's last activity
        groups_update_last_activity( $group->id );

        return $args;
    }
    
	/**
	 * Send a notification to subscribers
	 *
	 * @wp-filter bbp_new_reply 1
	 */
	public function notify_on_reply( $reply_id = 0, $topic_id = 0, $forum_id = 0, $anonymous_data = false, $reply_author = 0 ) {
        if ($this->handler === null) {
			return false;
		}

		global $wpdb;
        $notices = array();
		if (!bbp_is_subscriptions_active()) {
			return false;
		}
                
		$reply_id = bbp_get_reply_id( $reply_id );
		$topic_id = bbp_get_topic_id( $topic_id );
		$forum_id = bbp_get_forum_id( $forum_id );
                
		if (!bbp_is_reply_published($reply_id)) {
			return false;
		}
		if (!bbp_is_topic_published($topic_id)) {
			return false;
		}

		$user_ids = bbp_get_topic_subscribers($topic_id, true);
        //$debug_export = var_export($user_ids, true);
        //error_log("Notify new reply - topic subscribers ids: ".$debug_export);
        $subscribed_users = array();
        if (class_exists('Group_Activity_Subscription')) {
           // Get current BP group
           $group = groups_get_current_group();
           if (!empty( $group ) )
               $subscribed_users = groups_get_groupmeta( $group->id, 'ass_subscribed_users' );
        }
        $group_notice = "\n\n".pvm::get_option('bb_pvm_group_notice', '');
        $forum_notice = "\n\n".pvm::get_option('bb_pvm_new_reply_notice', '');
        $forum_notice = str_replace('{title}',bbp_get_topic_title( $topic_id ),$forum_notice);
        $forum_notice = str_replace ('{site}',get_option( 'blogname' ),$forum_notice);
        $forum_notice = str_replace ('{forum}',bbp_get_forum_title ($forum_id),$forum_notice);
        foreach ($user_ids as $user)
            $notices[$user]=$forum_notice;
        $group_topic_subscribers = array ();
        foreach ($subscribed_users as $key => $group_status) {
            if ($group_status == 'supersub') {
                $group_topic_subscribers[] = strval($key);
                $settings_link = ass_get_login_redirect_url( trailingslashit( bp_get_group_permalink( $group ) . 'notifications' ), $group_status );
                $notices[$key] .= $group_notice;
                $notices[$key] = str_replace ('{group_status}',ass_subscribe_translate( $group_status ),$notices[$key]);
                $notices[$key] = str_replace ('{settings_link}', $settings_link,$notices[$key]);
            }
        }
        $user_ids = array_merge($user_ids,$group_topic_subscribers);
        $user_ids = array_unique($user_ids);
        //$debug_export = var_export($user_ids, true);
        //error_log("Notify new reply - topic and group subscribers ids: ".$debug_export);
		if (empty($user_ids)) {
			return false;
		}

		// Poster name
		$reply_author_name = apply_filters('bb_pvm_reply_author_name', bbp_get_reply_author_display_name($reply_id));

		do_action( 'bbp_pre_notify_subscribers', $reply_id, $topic_id, $user_ids );

		// Don't send notifications to the person who made the post
		$send_to_author = pvm::get_option('bb_pvm_send_to_author', false);

		if (!$send_to_author && !empty($reply_author)) {
			$user_ids = array_filter($user_ids, function ($id) use ($reply_author) {
				return ((int) $id !== (int) $reply_author);
			});
		}

		// Get userdata for all users
		$user_ids = array_map(function ($id) {
			return get_userdata($id);
		}, $user_ids);

		// Sanitize the HTML into text
		//$content = apply_filters('bb_pvm_html_to_text', bbp_get_reply_content($reply_id));
		//$content = bbp_get_reply_content($reply_id);
        $content = get_post_field( 'post_content', $reply_id );
        $debug_export = var_export($reply_id, true);
		// Build email
		$subject = pvm::get_new_reply_subj();
		$text = pvm::get_new_reply_msg();
		$link = bbp_get_reply_url($reply_id);
        $text = str_replace ('{site}',get_option( 'blogname' ),$text);
        $subject = str_replace ('{site}',get_option( 'blogname' ),$subject);
		$text    = str_replace ('{forum}',bbp_get_forum_title ($forum_id),$text);
       	$subject = str_replace ('{forum}',bbp_get_forum_title ($forum_id),$subject);
		$text    = str_replace ('{title}',bbp_get_topic_title( $topic_id ),$text);
        $subject = str_replace ('{title}',bbp_get_topic_title( $topic_id ),$subject);
		$text    = str_replace ('{author}',$reply_author_name,$text);
       	$subject = str_replace ('{author}',$reply_author_name,$subject);
		$text    = str_replace ('{link}',$link,$text);
        $subject = str_replace ('{link}',$link,$subject);
		$text    = str_replace ('{content}',$content,$text);
        $subject = str_replace ('{content}',$content,$subject);
		$subject = apply_filters('bb_pvm_email_subject', $subject, $reply_id, $topic_id);
        $text = nl2br($text);
		$options = array(
			'id'     => $topic_id,
			'author' => $reply_author_name,
		);
        $send_attachments_in_notification = pvm::get_option('bb_pvm_send_attachments', '');
        if ($send_attachments_in_notification)
            $attachments_paths = $this->get_post_attachments_paths($reply_id);
        $message = new pvm_Message();
		$message->set_subject( $subject );
		$message->set_html( $text);
		$message->set_options( $options );
        if ($send_attachments_in_notification)
            $message->set_attachments( $attachments_paths );
        $message->set_reply_address_handler( function ( WP_User $user, pvm_Message $message ) use ( $topic_id ) {
			return pvm::get_reply_address( $topic_id, $user );
		} );
        // JJ $message->set_author( get_the_author_meta( 'display_name', $post->post_author ) );
		$message->set_author($reply_author_name);
        $this->send_mail($user_ids, $message,$notices);
		do_action( 'bbp_post_notify_subscribers', $reply_id, $topic_id, $user_ids );

		return true;
	}

	protected function is_allowed_type( $type ) {
		$allowed = array( 'topic','reply' );
		return in_array( $type, $allowed );
	}
    protected function send_bad_attachments_notification ($user, $title, $errors) {
        $reply_author_name=$user->display_name;
        $error_msg = implode($errors,"\n");
        if(pvm::get_option('bb_pvm_send_rej_att_user', false)) {
            // Send notification to user who sent offending message
            $subj = pvm::get_option('bb_pvm_rej_att_user_subj', false);
            $text = pvm::get_option('bb_pvm_rej_att_user_msg', false);
            $text = str_replace('{author}',$reply_author_name,$text);
            $text = str_replace('{user}',$user->display_name,$text);
            $subj = str_replace('{user}',$user->display_name,$subj);
            $text = str_replace('{site}',get_option( 'blogname' ),$text);
            $subj = str_replace('{site}',get_option( 'blogname' ),$subj);
            $text = str_replace('{title}',$title,$text);
            $text = str_replace('{link}',get_site_url(),$text);
            $subj = str_replace('{title}',$title,$subj);
            $text = str_replace('{error}',$error_msg,$text);
            $text = apply_filters('bb_pvm_send_rej_att_user', $text, $user->ID );
            wp_mail($user->user_email, $subj, $text);
        }
        if(pvm::get_option('bb_pvm_send_rej_att_admin', false)) {
            $user_roles[]="administrator";
            // bail out if no user roles found
            if ( !$user_roles ) return;
            $recipients = array();
            $subj = pvm::get_option('bb_pvm_rej_att_admin_subj', false);
            $text = pvm::get_option('bb_pvm_rej_att_admin_msg', false);
            $subj = str_replace('{author}',$reply_author_name,$subj);
            $text = str_replace('{author}',$reply_author_name,$text);
            $text = str_replace('{user}',$user->display_name,$text);
            $subj = str_replace('{user}',$user->display_name,$subj);
            $text = str_replace('{site}',get_option( 'blogname' ),$text);
            $subj = str_replace('{site}',get_option( 'blogname' ),$subj);
            $text = str_replace('{title}',$title,$text);
            $text = str_replace('{link}',get_site_url(),$text);
            $subj = str_replace('{title}',$title,$subj);
            $text = str_replace('{error}',$error_msg,$text);
            $text = apply_filters('bb_pvm_send_rej_att_admin', $text, $user->ID );
            $admins = get_users(array('role' => 'administrator', 'fields' => array('ID', 'user_email', 'display_name')));
            foreach ($admins as $user) {
                wp_mail($user->user_email, $subj, $text);
            }
        }
    }
	public function handle_insert( $value, pvm_Reply $reply ) {
        if ( ! empty( $value ) ) {
			return $value;
		}
		$post = get_post( $reply->post );
		if (! $post)
			return $value;
		if ( ! $this->is_allowed_type( $post->post_type ) ) {
			return $value;
		}
		$user = $reply->get_user();
		if (! $reply->is_valid() ) {
			pvm::notify_invalid( $user, $reply->from, bbp_get_reply_url($reply->post),bbp_get_topic_title( $reply->post ),__("Invalid authentication code",'pvm') );
			return false;
		}
        // Check if topic exists
        $parrent_status = get_post_status( $reply->post);
        //error_log("Parent :".$parrent_status);
        if (!$parrent_status || $parrent_status == 'trash') {
            error_log("Parrent topic doesn't exists");
            pvm::notify_invalid( $user, $reply->from, bbp_get_reply_url($reply->post),bbp_get_topic_title( $reply->post ),__("Topic does not exist or was deleted",'pvm') );
            return false;
        }
        $attachments = $reply->parse_attachments();
		$new_reply = array(
			'post_parent'       => $reply->post, // topic ID
			'post_author'       => $user->ID,
			'post_content'      => $reply->parse_body(),
            'post_attachments'  => $attachments['attachments'],
			'post_title'        => $reply->subject,
		);
        if ($attachments['errors'])
            $this->send_bad_attachments_notification($user, bbp_get_topic_title( $reply->post ) ,$attachments['errors']);
		$meta = array(
			'author_ip' => '127.0.0.1', // we could parse Received, but it's a pain, and inaccurate
			'forum_id' => bbp_get_topic_forum_id($reply->post),
			'topic_id' => $reply->post
		);
        // Subscribe to topic if needed
        if (pvm::get_option(' bb_pvm_topic_autosubscribe', '')) {
            bbp_add_user_subscription( $user->ID, $reply->post );
        }
		$reply_id = bbp_insert_reply($new_reply, $meta);
        foreach($new_reply['post_attachments'] as $attch) {
            $attch['post_parrent'] = $reply_id;
            $attch['post_author']  = $user->ID;
            $id = wp_insert_attachment($attch, $attch['filename'], $reply_id);
            $amd = wp_generate_attachment_metadata($id, $attch['filename']);
            $debug_export = var_export($amd, true);
            wp_update_attachment_metadata($id, $amd);
        }
		do_action( 'bbp_new_reply', $reply_id, $meta['topic_id'], $meta['forum_id'], false, $new_reply['post_author'] );
		bbp_add_user_subscription( $new_reply['post_author'], $meta['topic_id'] );
    
		return $reply_id;
	}

	public function register_settings() {
		register_setting( 'bb_pvm_options', 'bb_pvm_topics_notification', array(__CLASS__, 'validate_topics_notification') );

		add_settings_section('bb_pvm_options_bbpress', 'bbPress', '__return_null', 'bb_pvm_options');
		add_settings_field('bb_pvm_options_bbpress_topics_notification', 'New Topics Notification', array(__CLASS__, 'settings_field_topics_notification'), 'bb_pvm_options', 'bb_pvm_options_bbpress');
	}

	/**
	 * Print field for new topic notification
	 *
	 * @see self::init()
	 */

	/**
	 * Validate the new topic notification
	 *
	 * @param array $input
	 * @return array
	 */
	public function validate_topic_notification( $input ) {
		return is_array( $input ) ? $input : array();
	}
/**
	 * Get available settings for notifications
	 *
	 * @return array
	 */
	public function get_available_settings() {
                return null;
	}

	public function get_available_settings_short() {
                return null;

        }

	/**
	 * Get default notification settings
	 *
	 * @return array Map of type => pref value
	 */
	protected function get_default_settings() {
		return null;
	}

	/**
	 * Get notification settings for the current user
	 *
	 * @param int $user_id User to get settings for
	 * @return array Map of type => pref value
	 */
	protected function get_settings_for_user( $user_id, $site_id = null ) {
		$available = $this->get_available_settings();
		$settings = array();

		foreach ( $available as $type => $choices ) {
			$key = $this->key_for_setting( 'notifications.' . $type, $site_id );
			$value = get_user_meta( $user_id, $key );
			if ( empty( $value ) ) {
				continue;
			}

			$settings[ $type ] = $value[0];
		}

		return $settings;
	}

	protected function key_for_setting( $key, $site_id = null ) {
		return pvm_Manager::key_for_setting( 'bbpress', $key, $site_id );
	}

	protected function print_field( $field, $settings, $is_defaults_screen = false ) {
		$defaults = $this->get_default_settings();

		$site_id = get_current_blog_id();
		$default = isset( $defaults[ $field ] ) ? $defaults[ $field ] : false;
		$current = isset( $settings[ $field ] ) ? $settings[ $field ] : $default;
		$notifications = $this->get_available_settings();
		if ($notifications)
			foreach ( $notifications as $value => $title ) {
				printf('<label><textarea name="%s" class="large-text" rows="15">%s</textarea></label>',
					esc_attr( $this->key_for_setting( 'notifications.' . $field ) ),
					esc_attr( $value ));
			}
	}

	public function output_settings( $user = null ) {
		// Are we on the notification defaults screen?
		$is_defaults_screen = empty( $user );

		// Grab defaults and currently set
		$settings = $is_defaults_screen ? $this->get_default_settings() : $this->get_settings_for_user( $user->ID );

		?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Emails about new topics will be sent to subscribers of forum', 'pvm' ) ?></th>
				<td>
					<?php $this->print_field( 'message.new.topic', $settings, $is_defaults_screen ) ?>
				</td>
			</tr>
			<tr>	<th scope="row"><?php esc_html_e( "Email about new replies will be sent to topics' subscribers", 'pvm' ) ?></th>
				<td>
                                    	<?php $this->print_field( 'message.new.reply', $settings, $is_defaults_screen ) ?>
                                </td>
			</tr>
		<?php
	}

	public function save_profile_settings( $user_id, $args = array(), $sites = null ) {
		$available = $this->get_available_settings();

		if ( $sites === null ) {
			$sites = array( get_current_blog_id() );
		}

		foreach ( $available as $type => $options ) {
			foreach ( $sites as $site ) {
				$key = $this->key_for_setting( 'notifications.' . $type, $site );

				// PHP strips '.' out of POST data as a relic from the
				// register_globals days, so we need to take that into account
				$request_key = str_replace( '.', '_', $key );
				if ( ! isset( $args[ $request_key ] ) ) {
					continue;
				}
				$value = $args[ $request_key ];

				// Check the value is valid
				$options = array_keys( $options );
				if ( ! in_array( $value, $options ) ) {
					continue;
				}

				// Actually set it!
				if ( ! update_user_meta( $user_id, wp_slash( $key ), wp_slash( $value ) ) ) {
					// TODO: Log this?
					continue;
				}
			}
		}
	}

	public function network_notification_settings( $user = null, $sites ) {
		// Are we on the notification defaults screen?
		$is_defaults_screen = empty( $user );

		$available = $this->get_available_settings();
		$short_names = $this->get_available_settings_short();
		$defaults = $this->get_default_settings();

		?>
		<table class="widefat pvm-grid">
			<thead>
				<tr>
					<th></th>
					<th colspan="<?php echo esc_attr( count( $available['post'] ) ) ?>"
						class="last_of_col"><?php
						esc_html_e( 'Posts', 'pvm' ) ?></th>
					<th colspan="<?php echo esc_attr( count( $available['comment'] ) ) ?>"><?php
						esc_html_e( 'Comments', 'pvm' ) ?></th>
				</tr>
				<tr>
					<th></th>
					<?php
					foreach ( $available as $type => $opts ) {
						$last = key( array_slice( $opts, -1, 1, true ) );

						foreach ( $opts as $key => $title ) {
							printf(
								'<td class="%s"><abbr title="%s">%s</abbr>%s</td>',
								( $key === $last ? 'last_of_col' : '' ),
								esc_attr( $title ),
								esc_html( $short_names[ $type ][ $key ] ),
								( $key === $defaults[ $type ] ) ? ' <strong>*</strong>' : ''
							);
						}
					}
					?>
				</tr>
			</thead>

			<?php
			foreach ( $sites as $site ):
				$details = get_blog_details( $site );
				$settings = $this->get_settings_for_user( $user->ID, $site );

				$title = esc_html( $details->blogname ) . '<br >';
				$path = $details->path;
				if ( $path === '/' ) {
					$path = '';
				}

				$title .= '<span class="details">' . esc_html( $details->domain . $path ) . '</span>';
				?>
				<tr>
					<th scope="row"><?php echo $title ?></th>

					<?php
					foreach ( $available as $type => $opts ) {
						$default = isset( $defaults[ $type ] ) ? $defaults[ $type ] : false;
						$current = isset( $settings[ $type ] ) ? $settings[ $type ] : $default;

						$name = $this->key_for_setting( 'notifications.' . $type, $site );

						foreach ( $opts as $key => $title ) {
							printf(
								'<td><input type="radio" name="%s" value="%s" %s /></td>',
								esc_attr( $name ),
								esc_attr( $key ),
								checked( $key, $current, false )
							);
						}
					}
					?>
				</tr>
			<?php endforeach ?>
		</table>
		<?php
	}

}
