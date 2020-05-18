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

<!--Return To Top-->
<div class="return-to-top-container">
<a href="javascript:" id="return-to-top">
	<span class="up-arrow">
		&#8593;
	</span>
</a>
</div>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'dfl_forum' ); ?></a>

	<header id="masthead" class="site-header" role="banner">
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

			<!--+++++++++++++++++++
				 Upper Menu Bar Buttons 
				 ++++++++++++++++++++++ -->
			<ul class="responsive-menu-user-info">
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
				<?php
				if( has_nav_menu( 'menu-1' ) ){
					wp_nav_menu([
						'theme_location'        =>  'menu-1',
						'container'             =>  false,
						'fallback_cb'           =>  false,
						'depth'                 =>  1
					]);
				}
				?>
			</ul>

			<ul class="dropdown-user-info">
				<div class="menu">
						<li class="menu-item">
						<?php global $current_user; wp_get_current_user(); ?>
							<?php if ( is_user_logged_in() ) { ?>
							<a href="">
								<i aria-hidden="true" class="fa fa-user fa-lg fa-fw"></i>
								<span class="user-name">
									<?php echo $current_user->display_name; ?>
								</span>
							</a>
						<?php } 
							else { wp_loginout(); } ?>
						</li>
						<li class="menu-item">
						<?php if ( is_user_logged_in() ) { ?>
					
							<a href="<?php echo wp_logout_url(); ?>">
							<i aria-hidden="true" class="fa fa-sign-out fa fw"></i>
							<span class="logout-text">Logout</span>
							</a>
						<?php } ?>
						</li>
					</div><!--.menu-->
				</ul><!--.dropdown-user-info--> 
		

				<!-- Make this an include -->
				<ul class="user-buttons">
					<li>
					<?php global $current_user; wp_get_current_user(); ?>
						<?php if ( is_user_logged_in() ) { ?>
						<a href="">
							<i aria-hidden="true" class="fa fa-user fa-lg fa-fw"></i>
							<span class="user-name">
								<?php echo $current_user->display_name; ?>
							</span>
						</a>
					<?php } 
						else { wp_loginout(); } ?>
					</li>
					<li>
					<?php if ( is_user_logged_in() ) { ?>
				
						<a href="<?php echo wp_logout_url(); ?>">
						<i aria-hidden="true" class="fa fa-sign-out fa fw"></i>
						<span class="logout-text">Logout</span>
						</a>
					<?php } ?>
					</li>
				</ul>
			</ul>

			<nav id="site-navigation" class="site-header__navigation container">
				<?php

					if( has_nav_menu( 'menu-1' ) ){
						wp_nav_menu([
							'theme_location'        =>  'menu-1',
							'container'             =>  false,
							'fallback_cb'           =>  false,
							'depth'                 =>  1,
							// 'walker'                =>  new JU_Custom_Nav_Walker()
						]);
					}

				?>
				
			</nav><!--.site-header__navigation -->
			<nav class="site-header__submenu">
			<?php

				if( has_nav_menu( 'menu-1' ) ){
					wp_nav_menu([
						'theme_location'        =>  'menu-1',
						'container'             =>  false,
						'fallback_cb'           =>  false,
						'depth'                 =>  1,
						// 'walker'                =>  new JU_Custom_Nav_Walker()
					]);
				}

			?>
			</nav>
		</div><!--.container-->
	</header><!--.site-header-->
