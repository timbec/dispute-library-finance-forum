<?php get_header(); 

?>
<!-- Page Title
============================================= -->
<section id="page-title">
    <div class="container clearfix">
        <h1><?php single_post_title(); ?></h1>
        <span>
            <?php 
            
            if( function_exists( 'the_subtitle' ) ){
                the_subtitle(); 
            }
            
            ?>
        </span>
    </div>
</section><!-- #page-title end -->

<!-- Content
============================================= -->
<section id="content">

    <div class="content-wrap">
        <div class="container clearfix">
            <?php

            while( have_posts() ){
                the_post();

                the_content(); 
                } ?>
        </div>
    </div>

</section><!-- #content end -->

<?php get_footer(); ?>