<?php
/**
 * DFL_Forum functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package DFL_Forum
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

if ( ! function_exists( 'dfl_forum_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function dfl_forum_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on DFL_Forum, use a find and replace
		 * to change 'dfl_forum' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'dfl_forum', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'menu-1' => esc_html__( 'Primary', 'dfl_forum' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		// Set up the WordPress core custom background feature.
		add_theme_support(
			'custom-background',
			apply_filters(
				'dfl_forum_custom_background_args',
				array(
					'default-color' => 'ffffff',
					'default-image' => '',
				)
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 250,
				'width'       => 250,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);
	}
endif;
add_action( 'after_setup_theme', 'dfl_forum_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function dfl_forum_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'dfl_forum_content_width', 640 );
}
add_action( 'after_setup_theme', 'dfl_forum_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function dfl_forum_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'dfl_forum' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'dfl_forum' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'dfl_forum_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function dfl_forum_scripts() {

	// wp_enqueue_style( 'dfl_forum-style', get_stylesheet_uri(), array(), _S_VERSION );
	
	wp_enqueue_style( 'dfl_forum-style', get_stylesheet_directory_uri() . '/css/style.css' );
	wp_style_add_data( 'dfl_forum-style', 'rtl', 'replace' );

	wp_enqueue_script( 'dflforum-js', get_template_directory_uri() . '/js/scripts.js', array('jquery'), $theme_version, true );
	wp_script_add_data( 'dflforum-js', 'async', true );


}

add_action( 'wp_enqueue_scripts', 'dfl_forum_scripts' );

/**
 * Hide Top Menu Bar to all but Administrators
 */
function remove_admin_bar() {
	if (!current_user_can('administrator') && !is_admin()) {
	show_admin_bar(false);
	}
}
add_action('after_setup_theme', 'remove_admin_bar');

show_admin_bar(false); 


/**
 * Add Login Bar to top header
 * 
 */


/**
 * Implement the Custom Header feature.
 */
//require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
// if ( defined( 'JETPACK__VERSION' ) ) {
// 	require get_template_directory() . '/inc/jetpack.php';
// }

// Set the private API key for the user (from the user account page) and the user we're accessing the system as.
$private_key="fcac3864781599ed5faacb35b60b7950e0dc73b4d0296da078593f1b0bb582ed
";
$user="tbeckett";

// Search for 'user'
// $query="user=" . $user . "&function=do_search¶m1=user";
// Search for 'user'
$query="user=" . $user . "&function=do_search%user";

//$query="user=" . $user . "&function=create_resource&param1=1&param2=&param3=" . urlencode("http://www.montala.com/img/slideshow/montala-bg.jpg") . "&param4=&param5=&param6=&param7=" . urlencode(json_encode(array(1=>"Foo",8=>"Bar"))); 

// Sign the query using the private key
$sign=hash("sha256",$private_key . $query);

// Make the request and output the JSON results.
// $rs_response = file_get_contents("https://disputefinancinglibrary.org/api/?" . $query . "&sign=" . $sign);

// echo '<pre>'; 
// var_dump('From RS Directly: ' . $rs_response); 
// echo '</pre>'; 


$response = wp_remote_get("http://localhost:8888/rs_version_83/api/?" . $query . "&sign=" . $sign);
// echo '<pre>'; 
// var_dump($response); 
// echo '</pre>'; 
if ( is_wp_error( $response ) ) {
   echo 'There be errors, yo!';
} else {
   $body = wp_remote_retrieve_body( $response );
   $data = json_decode( $body );
}

if ( $data->Data ) {
   echo 'We got data, yo!';
   echo '<pre>'; 
   var_dump($data->Data);
   echo '</pre>';  
}