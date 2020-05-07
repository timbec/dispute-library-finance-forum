<?php 

function ju_recent_recipes_tab() {
    bp_core_load_template(
        apply_filters( 'bp_core_template_plugin', '' )
    );
}

function ju_buddypress_recent_posts_title() { ?>
    <div class="text-center">My Recipes</div>
<?php }


function ju_buddypress_posts_content() {
    $profile_user_id    = bp_displayed_user_id(); 

    if( !profile_user_id ) {
        return; 
    }

    $posts              = new WP_Query([
        'author'            => $profile_user_id, 
        'posts_per_page'    => 10, 
        'post_type'         => 'recipe'
    ]); 

    if( $posts->have_posts() ) {
        while( $posts->have_posts() ) {
            $post->the_post(); 

            get_template_part( 'partials/post/content-excerpt' ); 
        }
        wp_reset_postdata(); 
    } 
}