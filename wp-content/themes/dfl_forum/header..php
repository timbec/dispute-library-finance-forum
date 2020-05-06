<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package DFL_Forum
 */

?>
<!-- For testing login between DFL and Forum -->
<?php
if($_SESSION) {
	foreach($_SESSION as $key=>$value)
		{
		echo "$key - $value<br />";
		var_dump("$key - $value<br />"); 
		}
}
?>

<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'dfl_forum' ); ?></a>

	<header id="masthead" class="site-header">
		<div class="site-header__topnav">
			<ul>
				<li class="site-header__topnav--nyulogo">
					<a href="">NYU Logo</a>
				</li>
				<li>
					<button>Upload</button>
				</li>
				<li>
					member name
				</li>
			</ul>
		</div>
		<div class="site-header__logo">
			<?php
			the_custom_logo();
			if ( is_front_page() && is_home() ) :
				?>
				<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
				<?php
			else :
				?>
				<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
				<?php
			endif;
			$dfl_forum_description = get_bloginfo( 'description', 'display' );
			if ( $dfl_forum_description || is_customize_preview() ) :
				?>
				<p class="site-description"><?php echo $dfl_forum_description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
		</div><!-- .site-header__logo -->

		<nav id="site-navigation" class="site-header__navigation">
			<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'dfl_forum' ); ?></button>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'menu-1',
					'menu_id'        => 'primary-menu',
				)
			);
			?>
		</nav><!--.site-header__navigation -->
	</header><!--.site-header-->
