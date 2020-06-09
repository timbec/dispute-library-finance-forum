<?php

class pvm_Connector_WordPress {
	const SENT_META_KEY = 'pvm_sent';
	const MESSAGE_ID_KEY = 'pvm_message_ids';

	/**
	 * Sending handler
	 *
	 * @var pvm_Handler
	 */
	protected $handler;

	public function __construct( $handler ) {
		$this->handler = $handler;

		add_action( 'publish_post', array( $this, 'notify_on_publish' ), 10, 2 );

		add_action( 'wp_insert_comment', array( $this, 'notify_on_reply' ), 10, 2 );
		add_action( 'comment_approve_comment', array( $this, 'notify_on_reply' ), 10, 2 );

		add_action( 'pvm.reply.insert', array( $this, 'handle_insert' ), 20, 2 );
		add_action( 'pvm.manager.profile_fields', array( $this, 'output_settings' ) );
		add_action( 'pvm.manager.save_profile_fields', array( $this, 'save_profile_settings' ), 10, 2 );
		add_action( 'pvm.manager.network_profile_fields', array( $this, 'network_notification_settings' ), 10, 2 );
		add_action( 'pvm.manager.save_network_profile_fields', array( $this, 'save_profile_settings' ), 10, 3 );
	}

	/**
	 * Get a human-readable name for the handler
	 *
	 * This is used for the handler selector and is shown to the user.
	 * @return string
	 */
	public static function get_name() {
		return 'WordPress';
	}

	public static function is_allowed_type( $type ) {
		// Only notify for allowed types
		$allowed_types = apply_filters( 'pvm.connector.wordpress.post_types', array( 'post' ) );
		return in_array( $type, $allowed_types );
	}
	public function send_mail ($users, pvm_Message $message) {
		$options = $message->get_options();

		$from = pvm::get_from_address();
		if ( $author = $message->get_author() ) {
			$from = sprintf( '%s <%s>', $author, $from );
		}

		$messages = array();
		//$debug_export = var_export($users, true);
        //error_log("Users -> ".$debug_export);
		foreach ($users as $user) {
			$to = $user->user_email;
            $subj = $message->get_subject();
            $reply_to=$message->get_reply_address($user);
			$headers = array();
            $type = '';
			if ( $text = $message->get_text() ) {
				$data = $text;
                $type = 'text/plain';
			}
			if ( $html = $message->get_html() ) {
				$data = $html;
                $type = 'text/html';
			}

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
            //$debug_export = var_export($header, true);
            //error_log("Headers: ".$debug_export);
            //error_log("Message -> To:".$to." Subject:".$subj." Data:".$data);
			wp_mail( $to, $subj, $data, $header );
			//$messages[ $user->ID ] = $this->send_single($data);
		}
		
	}

	/**
	 * Notify user roles on new post
	 */
	public function notify_on_publish( $id = 0, $post = null ) {
		if ( empty( $this->handler ) || ! pvm::is_enabled_for_site() ) {
			return;
		}
        //$debug_export = var_export($post, true);
        //error_log("Post".$debug_export);
		// Double-check status
		if ( get_post_status( $id ) !== 'publish' ) {
			return;
		}

		// Only notify for allowed types
		$allowed_types = apply_filters( 'pvm.connector.wordpress.post_types', array( 'post' ) );
		if ( ! $this->is_allowed_type( $post->post_type ) ) {
			return;
		}

		// Don't notify if we're already done so for the post
		$has_sent = get_post_meta( $id, static::SENT_META_KEY, true );

		/**
		 * Should we send a publish notification?
		 *
		 * This is based on meta by default to avoid double-sending, but
		 * override to change the logic to whatever you like for
		 * post publishing.
		 *
		 * @param bool $should_notify Should we notify for this event?
		 * @param WP_Post $post Post we're going to notify for
		 */
		$should_notify = apply_filters( 'pvm.connector.wordpress.should_notify_publish', ! $has_sent, $post );

		if ( ! $should_notify ) {
			return;
		}

		$recipients = $this->get_post_subscribers( $post );

		// still no users?
		if ( empty( $recipients ) ) {
			return;
		}

		$message = new pvm_Message();

		$message->set_text( $this->get_post_content_as_text( $post ) );
		$message->set_html( $this->get_post_content_as_html( $post ) );

		$subject = apply_filters( 'bb_pvm_topic_email_subject', '[' . get_option( 'blogname' ) . '] ' . html_entity_decode( get_the_title( $id ), ENT_QUOTES ), $id );
		$message->set_subject( $subject );

		$message->set_author( get_the_author_meta( 'display_name', $post->post_author ) );

		$options = array();
		if ( $this->handler->supports_message_ids() ) {
			$options['message-id'] = $this->get_message_id_for_post( $post );
		}
		$message->set_options( $options );

		$message->set_reply_address_handler( function ( WP_User $user, pvm_Message $message ) use ( $post ) {
			return pvm::get_reply_address( 'post_' . $post->ID, $user );
		} );
		//$responses = $this->handler->send_mail( $recipients, $message );
		$responses = $this->send_mail( $recipients, $message );
		if ( ! $this->handler->supports_message_ids() && ! empty( $responses ) ) {
			update_post_meta( $id, self::MESSAGE_ID_KEY, $responses );
		}

		// Stop any future double-sends
		update_post_meta( $id, static::SENT_META_KEY, true );
	}

	protected function get_text_footer( $url ) {
		$text = "---\n";
		$text .= sprintf( 'Reply to this email directly or view it on %s:', get_option( 'blogname' ) );
		$text .= "\n" . $url;

		return apply_filters( 'pvm.connector.wordpress.text_footer', $text, $url );
	}

	protected function get_html_footer( $url ) {
		$footer = '<p style="font-size:small;-webkit-text-size-adjust:none;color:#666;">&mdash;<br>';
		$footer .= sprintf(
			'Reply to this email directly or <a href="%s">view it on %s</a>.',
			$url,
			get_option( 'blogname' )
		);
		$footer .= '</p>';

		return apply_filters( 'pvm.connector.wordpress.html_footer', $footer, $url );
	}

	protected function get_post_content_as_text( $post ) {
		$content = apply_filters( 'the_content', $post->post_content );

		// Sanitize the HTML into text
		$content = apply_filters( 'bb_pvm_html_to_text', $content );

		// Build email
		$text = $content . "\n\n" . $this->get_text_footer( get_permalink( $post->ID ) );

		/**
		 * Filter the email content
		 *
		 * Use this to change document formatting, etc
		 *
		 * @param string $text Text content
		 * @param WP_Post $post Post the content is generated from
		 */
		return apply_filters( 'pvm.connector.wordpress.post_content_text', $text, $post );
	}

	protected function get_post_content_as_html( $post ) {
		$content = apply_filters( 'the_content', $post->post_content );

		$text = $content . "\n\n" . $this->get_html_footer( get_permalink( $post->ID ) );

		/**
		 * Filter the email content
		 *
		 * Use this to add tracking codes, metadata, etc
		 *
		 * @param string $text HTML content
		 * @param WP_Post $post Post the content is generated from
		 */
		return apply_filters( 'pvm.connector.wordpress.post_content_html', $text, $post );
	}

	/**
	 * Send a notification to subscribers
	 */
	public function notify_on_reply( $id = 0, $comment = null ) {
		if ( empty( $this->handler ) || ! pvm::is_enabled_for_site() ) {
			return false;
		}

		if ( wp_get_comment_status( $comment ) !== 'approved' ) {
			return false;
		}

		// Is the post published?
		$post = get_post( $comment->comment_post_ID );
		if ( get_post_status( $post ) !== 'publish' ) {
			return false;
		}

		// Grab the users we should notify
		$users = $this->get_comment_subscribers( $comment );
		if ( empty( $users ) ) {
			return false;
		}

		$message = new pvm_Message();

		// Poster name
		$message->set_author( apply_filters( 'pvm.connector.wordpress.comment_author', $comment->comment_author ) );

		// Don't send notifications to the person who made the post
		$send_to_author = pvm::get_option('bb_pvm_send_to_author', false);

		if ( ! $send_to_author && ! empty( $comment->user_id ) ) {
			$author = (int) $comment->user_id;

			$users = array_filter( $users, function ($user) use ($author) {
				return $user->ID !== $author;
			} );
		}

		// Sanitize the HTML into text
		$message->set_text( $this->get_comment_content_as_text( $comment ) );
		$message->set_html( $this->get_comment_content_as_html( $comment ) );

		$subject = apply_filters('bb_pvm_email_subject', 'Re: [' . get_option( 'blogname' ) . '] ' . html_entity_decode( get_the_title( $post ), ENT_QUOTES ), $id, $post->ID);
		$message->set_subject( $subject );

		$message->set_reply_address_handler( function ( WP_User $user, pvm_Message $message ) use ( $comment ) {
			return pvm::get_reply_address( 'comment_' . $comment->comment_ID, $user );
		} );

		$options = array();

		if ( $this->handler->supports_message_ids() ) {
			$options['references']  = $this->get_references_for_comment( $comment );
			$options['message-id']  = $this->get_message_id_for_comment( $comment );

			if ( ! empty( $comment->comment_parent ) ) {
				$parent = get_comment( $comment->comment_parent );
				$options['in-reply-to'] = $this->get_message_id_for_comment( $parent );
			}
			else {
				$options['in-reply-to'] = $this->get_message_id_for_post( $post );
			}
		}
		else {
			$message_ids = get_post_meta( $id, self::MESSAGE_ID_KEY, $responses );
			$options['in-reply-to'] = $message_ids;
		}

		$message->set_options( $options );
		$this->send_mail( $users, $message );
		//$this->handler->send_mail( $users, $message );

		return true;
	}

	protected function get_comment_content_as_text( $comment ) {
		$content = apply_filters( 'comment_text', get_comment_text( $comment ) );

		// Sanitize the HTML into text
		$content = apply_filters( 'bb_pvm_html_to_text', $content );

		// Build email
		$text = $content . "\n\n" . $this->get_text_footer( get_comment_link( $comment ) );

		/**
		 * Filter the email content
		 *
		 * Use this to change document formatting, etc
		 *
		 * @param string $text Text content
		 * @param WP_Post $post Post the content is generated from
		 */
		return apply_filters( 'pvm.connector.wordpress.comment_content_text', $text, $comment );
	}

	protected function get_comment_content_as_html( $comment ) {
		$content = apply_filters( 'comment_text', get_comment_text( $comment ) );

		$text = $content . "\n\n" . $this->get_html_footer( get_comment_link( $comment ) );

		/**
		 * Filter the email content
		 *
		 * Use this to add tracking codes, metadata, etc
		 *
		 * @param string $text HTML content
		 * @param WP_Post $post Post the content is generated from
		 */
		return apply_filters( 'pvm.connector.wordpress.comment_content_html', $text, $comment );
	}

	/**
	 * Get the Message ID for a post
	 *
	 * @param WP_Post $post Post object
	 * @return string Message ID
	 */
	protected function get_message_id_for_post( WP_Post $post ) {
		$left = 'pvm/' . $post->post_type . '/' . $post->ID;
		$right = parse_url( home_url(), PHP_URL_HOST );

		$id = sprintf( '<%s@%s>', $left, $right );

		/**
		 * Filter message IDs for posts
		 *
		 * @param string $id Message ID (conforming to RFC5322 Message-ID semantics)
		 * @param WP_Post $post Post object
		 */
		return apply_filters( 'pvm.connector.wordpress.post_message_id', $id, $post );
	}

	/**
	 * Get the Message ID for a comment
	 *
	 * @param stdClass $comment Comment object
	 * @return string Message ID
	 */
	protected function get_message_id_for_comment( $comment ) {
		$post = get_post( $comment->comment_post_ID );
		$type = $comment->comment_type;
		if ( empty( $type ) ) {
			$type = 'comment';
		}

		$left = 'pvm/' . $post->post_type . '/' . $post->ID . '/' . $type . '/' . $comment->comment_ID;
		$right = parse_url( home_url(), PHP_URL_HOST );

		$id = sprintf( '<%s@%s>', $left, $right );

		/**
		 * Filter message IDs for posts
		 *
		 * @param string $id Message ID (conforming to RFC5322 Message-ID semantics)
		 * @param stdClass $post Post object
		 */
		return apply_filters( 'pvm.connector.wordpress.comment_message_id', $id, $comment );
	}

	/**
	 * Get the References for a comment
	 *
	 * @param stdClass $comment Comment object
	 * @return string Message ID
	 */
	protected function get_references_for_comment( $comment ) {
		$references = array();
		if ( ! empty( $comment->comment_parent ) ) {
			// Add parent's references
			$parent = get_comment( $comment->comment_parent );
			$references = array_merge( $references, $this->get_references_for_comment( $parent ) );

			// Add reference to the parent itself
			$references[] = $this->get_message_id_for_comment( $parent );
		}
		else {
			// Parent is a post
			$parent = get_post( $comment->comment_post_ID );
			$references[] = $this->get_message_id_for_post( $parent );
		}

		return $references;
	}

	/**
	 * Get all subscribers for post notifications
	 *
	 * @param WP_Post $post Post being checked
	 * @return WP_User[]
	 */
	protected function get_post_subscribers( WP_Post $post ) {
		$recipients = array();

		// Find everyone who has a matching preference, or who is using the
		// default (if it's on)
		$query = array(
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key' => $this->key_for_setting( 'notifications.post' ),
					'value' => 'all'
				),
			),
		);

		$default = $this->get_default_settings();
		if ( $default['post'] === 'all' ) {
			$query['meta_query'][] = array(
				'key' => $this->key_for_setting( 'notifications.post' ),
				'compare' => 'NOT EXISTS',
			);
		}

		$users = get_users( $query );
		if ( empty( $users ) ) {
			return array();
		}

		// Filter out any users without read access to the post
		$recipients = array_filter( $users, function ( WP_User $user ) use ( $post ) {
			return user_can( $user, 'read_post', $post->ID );
		} );

		return $recipients;
	}

	/**
	 * Get all subscribers for comment notifications
	 *
	 * @param stdClass $comment Comment being checked
	 * @return WP_User[]
	 */
	public function get_comment_subscribers( $comment ) {
		$recipients = array();

		// Find everyone who has a matching preference, or who is using the
		// default (if it's on)
		$query = array(
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key' => $this->key_for_setting( 'notifications.comment' ),
					'value' => 'all'
				),
			),
		);

		$default = $this->get_default_settings();
		if ( $default['post'] === 'all' ) {
			$query['meta_query'][] = array(
				'key' => $this->key_for_setting( 'notifications.comment' ),
				'compare' => 'NOT EXISTS',
			);
		}


		$users = get_users( $query );
		if ( empty( $users ) ) {
			return array();
		}

		// Also grab everyone if they're in the thread and subscribed to
		// same-thread comments
		$sibling_authors = $this->get_thread_subscribers( $comment );
		$users = array_merge( $users, $sibling_authors );

		// Trim to unique authors using IDs as key
		$subscribers = array();
		foreach ( $users as $user ) {
			if ( isset( $subscribers[ $user->ID ] ) ) {
				// Already handled
				continue;
			}

			if ( ! user_can( $user, 'read_post', $comment->comment_post_ID ) ) {
				// No access, skip
				continue;
			}

			$subscribers[ $user->ID ] = $user;
		}

		return $subscribers;
	}

	/**
	 * Get subscribers for the thread that a comment is in
	 *
	 * Gets subscribers to the thread (i.e. parent comment authors who are
	 * subscribed) as well as subscribers to all threads (i.e. comment authors
	 * who are subscribed to all comments on the post)
	 *
	 * @param stdClass $comment Comment being checked
	 * @return array
	 */
	protected function get_thread_subscribers( $comment ) {
		$sibling_comments = get_comments( array(
			'post_id'         => $comment->comment_post_ID,
			'comment__not_in' => $comment->comment_ID,
			'type'            => 'comment'
		) );
		if ( empty( $sibling_comments ) ) {
			return array();
		}

		$users = array();
		$indexed = array();
		foreach ( $sibling_comments as $sibling ) {
			// Re-index by ID for later usage
			$indexed[ $sibling->comment_ID ] = $sibling;

			// Grab just comments with author IDs
			if ( empty( $sibling->user_id ) ) {
				continue;
			}

			// Skip duplicate parsing
			if ( isset( $users[ $sibling->user_id ] ) ) {
				continue;
			}

			$pref = get_user_meta( $sibling->user_id, $this->key_for_setting( 'notifications.comment' ), true );
			$users[ $sibling->user_id ] = ( $pref === 'participant' );
		}

		// Now, find users in the thread
		$sibling = $comment;
		while ( ! empty( $sibling->comment_parent ) ) {
			$parent_id = $sibling->comment_parent;
			if ( ! isset( $indexed[ $parent_id ] ) ) {
				break;
			}

			$sibling = $indexed[ $parent_id ];
			if ( ! isset( $sibling->user_id ) ) {
				continue;
			}

			$pref = get_user_meta( $sibling->user_id, $this->key_for_setting( 'notifications.comment' ), true );
			if ( $pref ) {
				$users[ $sibling->user_id ] = true;
			}
		}

		$subscribers = array();
		foreach ( $users as $user => $subscribed ) {
			if ( ! $subscribed ) {
				continue;
			}

			$subscribers[] = get_userdata( $user );
		}
		return $subscribers;
	}

	public function handle_insert( $value, pvm_Reply $reply ) {
		//error_log("Wordpress connector hadle");
		if ( ! empty( $value ) ) {
			return $value;
		}
		$comment_parent = null;
        //error_log("Reply_post WP:".$reply->post);
		//$debug_export = var_export($reply, true);
        //error_log("Reply WP  -> ".$debug_export);
        if (strpos($reply->post,"_"))
            list( $type, $parent_id ) = explode( '_', $reply->post, 2 );
		else {
			$type='bbpress';
			$parent_id=$reply->post;
		}
		switch ( $type ) {
			case 'post':
				$post = get_post( $parent_id );
				break;

			case 'comment':
				$comment_parent = get_comment( $parent_id );
				if ( empty( $comment_parent ) ) {
					return $value;
				}

				$post = get_post( $comment_parent->comment_post_ID );
				break;

			default:
				return $value;
		}

		if ( ! $this->is_allowed_type( $post->post_type ) ) {
			return $value;
		}

		$user = $reply->get_user();

		if ( ! $reply->is_valid() ) {
			pvm::notify_invalid( $user,$reply->from, get_permalink($post), $post->post_title );
			return new WP_Error( 'pvm.connector.wordpress.invalid_reply' );
		}

		$data = array(
			'comment_post_ID'      => $post->ID,
			'user_id'              => $user->ID,
			'comment_author'       => $user->display_name,
			'comment_author_email' => $user->user_email,
			'comment_author_url'   => $user->user_url,

			'comment_content'  => $reply->parse_body(),
		);
		if ( ! empty( $comment_parent ) ) {
			$data['comment_parent'] = $comment_parent->comment_ID;
		}
                //$debug_export = var_export($data, true);
                //throw new Exception ($debug_export);
		return wp_insert_comment( $data );
	}

	/**
	 * Get available settings for notifications
	 *
	 * @return array
	 */
	public function get_available_settings() {
		return array(
			'post' => array(
				'all' => __( 'All new posts', 'pvm' ),
				''    => __( 'No notifications', 'pvm' ),
			),

			'comment' => array(
				'all'         => __( 'All new comments', 'pvm' ),
				'participant' => __( "New comments on posts I've commented on", 'pvm' ),
				'replies'     => __( 'Replies to my comments', 'pvm' ),
				''            => __( 'No notifications', 'pvm' )
			),
		);
	}

	public function get_available_settings_short() {
		return array(
			'post' => array(
				'all' => __( 'All', 'pvm' ),
				''    => __( 'None', 'pvm' ),
			),

			'comment' => array(
				'all'         => __( 'All', 'pvm' ),
				'participant' => __( "Participant", 'pvm' ),
				'replies'     => __( 'Replies', 'pvm' ),
				''            => __( 'None', 'pvm' )
			),
		);
	}

	/**
	 * Get default notification settings
	 *
	 * @return array Map of type => pref value
	 */
	protected function get_default_settings() {
		$keys = array(
			'post'    => 'all',
			'comment' => 'all',
		);
		$defaults = array();

		foreach ( $keys as $key => $hardcoded_default ) {
			$option_key = $this->key_for_setting( 'notifications.' . $key );
			$value = pvm::get_option( $option_key, null );

			$defaults[ $key ] = isset( $value ) ? $value : $hardcoded_default;
		}

		return $defaults;
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
		return pvm_Manager::key_for_setting( 'wordpress', $key, $site_id );
	}

	protected function print_field( $field, $settings, $is_defaults_screen = false ) {
		$defaults = $this->get_default_settings();

		$site_id = get_current_blog_id();
		$default = isset( $defaults[ $field ] ) ? $defaults[ $field ] : false;
		$current = isset( $settings[ $field ] ) ? $settings[ $field ] : $default;

		$notifications = $this->get_available_settings();

		foreach ( $notifications[ $field ] as $value => $title ) {
			$maybe_default = '';
			if ( ! $is_defaults_screen && $value === $default ) {
				$maybe_default = '<strong>' . esc_html__( ' (default)', 'pvm' ) . '</strong>';
			}

			printf(
				'<label><input type="radio" name="%s" value="%s" %s /> %s</label><br />',
				esc_attr( $this->key_for_setting( 'notifications.' . $field ) ),
				esc_attr( $value ),
				checked( $value, $current, false ),
				esc_html( $title ) . $maybe_default
			);
		}
	}

	public function output_settings( $user = null ) {
		// Are we on the notification defaults screen?
		$is_defaults_screen = empty( $user );

		// Grab defaults and currently set
		$settings = $is_defaults_screen ? $this->get_default_settings() : $this->get_settings_for_user( $user->ID );

		?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Posts', 'pvm' ) ?></th>
				<td>
					<?php $this->print_field( 'post', $settings, $is_defaults_screen ) ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Comments', 'pvm' ) ?></th>
				<td>
					<?php $this->print_field( 'comment', $settings, $is_defaults_screen ) ?>
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
