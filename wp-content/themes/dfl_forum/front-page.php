<?php

/**
 * Template Name: bbPress - Forums (Index)
 *
 * @package bbPress
 * @subpackage Theme
 */

get_header(); 

?>
    
    <div id="site-content" class="container">
        <main id="primary" class="site-main">
        <?php do_action( 'bbp_before_main_content' ); ?>

        <?php do_action( 'bbp_template_notices' ); ?>

        <?php while ( have_posts() ) : the_post(); ?>

            <div id="forum-front" class="bbp-forum-front">
                <h1 class="entry-title"><?php the_title(); ?></h1>
                <div class="entry-content">

                    <?php the_content(); ?>

                    <!-- Why is this 'archive-forum'? -->
                    <?php bbp_get_template_part( 'content', 'archive-forum' ); ?>

                </div>
            </div><!-- #forum-front -->

        <?php endwhile; ?>

        <?php do_action( 'bbp_after_main_content' ); ?>

        </main><!-- #main -->

</div><!--#site-content-->
<?php get_footer(); ?>