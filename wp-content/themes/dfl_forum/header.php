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
// var_dump($_SESSION); 
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
	
	<script src="https://kit.fontawesome.com/16401a0251.js" crossorigin="anonymous"></script>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'dfl_forum' ); ?></a>

	<header id="masthead" class="site-header" role="banner">
		<div class="site-header__topnav">
			<ul class="container">
				<li class="site-header__topnav--nyulogo">
					<a href="">
						<img src="<?php bloginfo( 'template_url' ); ?>/img/nyu-logo.svg" alt="">
					</a>
				</li>

				<!-- Upper Menu Bar Buttons -->
				<li class="responsive-header-menu">
				<button class="responsive-header-menu__button" id="nav-click-1">
					<span class="responsive-header-menu__button--text">
						Menu
					</span>
					<i class="fas fa-align-justify"></i>
				</button>
				<button class="responsive-header-menu__button" id="nav-click-2">
					<span class="responsive-header-menu__button--text">
						Account
					</span>
					<i aria-hidden="true" class="fa fa-user fa-lg fa-fw"></i>
				</button>
			</li>
			<ul class="dropdown-menu">
				<li class="dropdown-menu__item">Home</li>
				<li class="dropdown-menu__item">Recently Added</li>
				<li class="dropdown-menu__item">All Documents</li>
				<li class="dropdown-menu__item">My Collections</li>
				<li class="dropdown-menu__item">Acknowledgements</li>
				<li class="dropdown-menu__item">Forum</li>
				<li class="dropdown-menu__item">Contact Us</li>
				<li class="dropdown-menu__item">Forum</li>
				<li class="dropdown-menu__item">Volunteer</li>
			</ul>
		

				
				<ul class="user-buttons">
					<li>
					<?php global $current_user; wp_get_current_user(); ?>
						<?php if ( is_user_logged_in() ) { ?>
						<a href="">
							<i aria-hidden="true" class="fa fa-user fa-lg fa-fw"></i>
						</a>
						<h3><?php echo $current_user->display_name; ?></h3>
					<?php } 
						else { wp_loginout(); } ?>
					</li>
					<li>
					<?php if ( is_user_logged_in() ) { ?>
				
						<a href="<?php echo wp_logout_url(); ?>">
						<i aria-hidden="true" class="fa fa-sign-out fa fw"></i>
						Logout
						</a>
					<?php } ?>
					</li>
				</ul>
			</ul>
		</div>
		<div class="container">
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
				endif; ?>

			</div><!-- .site-header__logo -->

			<!-- Responsive Header for Main Logo Bar -->
			<!-- <div class="responsive-header-menu">
				<button class="responsive-header-menu__button" id="nav-click-1">
					<span class="responsive-header-menu__button--text">
						Menu
					</span>
					<i class="fas fa-align-justify"></i>
				</button>
				<button class="responsive-header-menu__button" id="nav-click-2">
					<span class="responsive-header-menu__button--text">
						Account
					</span>
					<i aria-hidden="true" class="fa fa-user fa-lg fa-fw"></i>
				</button>
			</div> -->

			<nav id="site-navigation" class="site-header__navigation container">
				<ul>
					<li><a href="">Home</a></li>
					<li><a href="">Search Results</a></li>
					<li><a href="">All Documents</a></li>
					<!-- <li><a href="">My Collections</a></li> -->
					<li><a href="">Saved Search</a></li>
					<li><a href="http://localhost:8888/dispute-finance-library/plugins/new_home_page/pages/acknowledgements.php">Acknowledgements</a></li>
					<li><a href="">Forum</a></li>
					<li><a href="http://localhost:8888/dispute-finance-library/plugins/new_home_page/pages/contactus.php">Contact Us</a></li>
					<li><a href="http://localhost:8888/dispute-finance-library/plugins/new_home_page/pages/volunteer.php">Volunteer</a></li>
				</ul>
				
			</nav><!--.site-header__navigation -->
			<nav class="site-header__submenu">
			<?php
				// wp_nav_menu(
				// 	array(
				// 		'theme_location' => 'Primary',
				// 		'menu_id'        => 'primary-menu',
				// 	)
				// );
				?>
			</nav>
		</div><!--.container-->
	</header><!--.site-header-->
