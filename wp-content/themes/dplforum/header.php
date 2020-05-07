<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />

	<!-- Stylesheets
	============================================= -->
	<?php wp_head(); ?>

	<meta name="viewport" content="width=device-width, initial-scale=1" />
</head>

<body <?php body_class( 'stretched no-transition' ); ?>>

    <!-- Document Wrapper
    ============================================= -->
    <div id="wrapper" class="clearfix">
        <!-- Top Bar
        ============================================= -->
        <div id="top-bar" class="dark">

            <div class="container clearfix">

                <div class="col_half nobottommargin">

                    <!-- Top Links
                    ============================================= -->
                    <div class="top-links">
                        <?php

                        if( has_nav_menu( 'secondary' ) ){
                            wp_nav_menu([
                                'theme_location'        =>  'secondary',
                                'container'             =>  false,
                                'fallback_cb'           =>  false,
                                'depth'                 =>  1,
                                // 'walker'                =>  new JU_Custom_Nav_Walker()
                            ]);
                        }

                        ?>
                    </div><!-- .top-links end -->

                </div>

                <div class="col_half fright col_last nobottommargin">

                    <!-- Top Social
                    ============================================= -->
                    <div id="top-social">
                        <ul>
                            <?php

                            if( get_theme_mod( 'ju_facebook_handle' ) ){
                                ?><li><a href="https://facebook.com/<?php echo get_theme_mod( 'ju_facebook_handle' ); ?>" class="si-facebook"><span class="ts-icon"><i class="icon-facebook"></i></span><span class="ts-text">Facebook</span></a></li><?php
                            }

                            if( get_theme_mod( 'ju_twitter_handle' ) ){
                                ?> <li><a href="https://twitter.com/<?php echo get_theme_mod( 'ju_twitter_handle' ); ?>" class="si-twitter"><span class="ts-icon"><i class="icon-twitter"></i></span><span class="ts-text">Twitter</span></a></li><?php
                            }

                            if( get_theme_mod( 'ju_instagram_handle' ) ){
                                ?><li><a href="https://instagram.com/<?php echo get_theme_mod( 'ju_instagram_handle' ); ?>" class="si-instagram"><span class="ts-icon"><i class="icon-instagram2"></i></span><span class="ts-text">Instagram</span></a></li><?php
                            }

                            if( get_theme_mod( 'ju_email' ) ){
                                ?><li><a href="mailto:<?php echo get_theme_mod( 'ju_email' ); ?>" class="si-email3"><span class="ts-icon"><i class="icon-email3"></i></span><span class="ts-text"><?php echo get_theme_mod( 'ju_email' ); ?></span></a></li><?php
                            }

                            if( get_theme_mod( 'ju_phone_number' ) ){
                                ?><li><a href="tel:+<?php echo get_theme_mod( 'ju_phone_number' ); ?>" class="si-call"><span class="ts-icon"><i class="icon-call"></i></span><span class="ts-text">+<?php echo get_theme_mod( 'ju_phone_number' ); ?></span></a></li><?php
                            }
                            
                            ?>
                        </ul>
                    </div><!-- #top-social end -->

                </div>

            </div>

        </div><!-- #top-bar end -->

        <!-- Header
        ============================================= -->
        <header id="header" class="sticky-style-2">

            <div class="container clearfix">

                <!-- Logo
                ============================================= -->
                <div id="logo">
                    <?php

                    if( has_custom_logo() ){
                        the_custom_logo();
                    }else{
                        ?>
                        <a href="<?php echo home_url( '/' ); ?>" class="standard-logo"><?php bloginfo( 'name' ); ?></a>
                        <?php
                    }

                    ?>
                </div><!-- #logo end -->

                <div class="top-advert">
                    <?php 
                    if( function_exists( 'quads_ad' ) ){
                        echo quads_ad([ 'location' => 'udemy_header' ]);
                    }
                    ?>
                </div>

            </div>

            <div id="header-wrap">

                <!-- Primary Navigation
                ============================================= -->
                <nav id="primary-menu" class="style-2">

                    <div class="container clearfix">

                        <div id="primary-menu-trigger"><i class="icon-reorder"></i></div>

                        <?php

                        if( has_nav_menu( 'primary' ) ){
                            wp_nav_menu([
                                'theme_location'        =>  'primary',
                                'container'             =>  false,
                                'fallback_cb'           =>  false,
                                'depth'                 =>  4,
                                // 'walker'                =>  new JU_Custom_Nav_Walker()
                            ]);
                        }

                        ?>

                        <?php

                        if( get_theme_mod( 'ju_header_show_cart' ) ){
                            ?>
                            <!-- Top Cart
                            ============================================= -->
                            <div id="top-cart">
                                <a href="#" id="top-cart-trigger"><i class="icon-shopping-cart"></i><span>5</span></a>
                                <div class="top-cart-content">
                                    <div class="top-cart-title">
                                        <h4>Shopping Cart</h4>
                                    </div>
                                    <div class="top-cart-items">
                                        <div class="top-cart-item clearfix">
                                            <div class="top-cart-item-image">
                                                <a href="#"><img src="images/shop/small/1.jpg" /></a>
                                            </div>
                                            <div class="top-cart-item-desc">
                                                <a href="#">Blue Round-Neck Tshirt</a>
                                                <span class="top-cart-item-price">$19.99</span>
                                                <span class="top-cart-item-quantity">x 2</span>
                                            </div>
                                        </div>
                                        <div class="top-cart-item clearfix">
                                            <div class="top-cart-item-image">
                                                <a href="#"><img src="images/shop/small/6.jpg"/></a>
                                            </div>
                                            <div class="top-cart-item-desc">
                                                <a href="#">Light Blue Denim Dress</a>
                                                <span class="top-cart-item-price">$24.99</span>
                                                <span class="top-cart-item-quantity">x 3</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="top-cart-action clearfix">
                                        <span class="fleft top-checkout-price">$114.95</span>
                                        <button class="button button-3d button-small nomargin fright">
                                            View Cart
                                        </button>
                                    </div>
                                </div>
                            </div><!-- #top-cart end -->
                            <?php
                        }

                        if( get_theme_mod( 'ju_header_show_search' ) ){
                            ?>
                            <!-- Top Search
                            ============================================= -->
                            <div id="top-search">
                                <a href="#" id="top-search-trigger">
                                    <i class="icon-search3"></i><i class="icon-line-cross"></i>
                                </a>
                                <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
                                    <input type="text" name="s"
                                        class="form-control" 
                                        placeholder="<?php _e( 'Type &amp; Hit Enter..', 'udemy' ); ?>"
                                        value="<?php the_search_query(); ?>">
                                </form>
                            </div><!-- #top-search end -->
                            <?php
                        }

                        ?>
                        
                    </div>

                </nav><!-- #primary-menu end -->

            </div>

        </header><!-- #header end -->