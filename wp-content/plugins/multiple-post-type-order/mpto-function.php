<?php
// add admin notice success
function mpto_admin_notice__success() {
    if (!isset($_SESSION)) {
        @session_start();
    }
    if (isset($_SESSION['notices']) && !empty($_SESSION['notices']) && ($_SESSION['notices'] != '')) {
        if ($_SESSION['notices']['type'] == 'success') {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e($_SESSION['notices']['msg'], 'sample-text-domain'); ?></p>
            </div>
            <?php
            $_SESSION['notices']['type'] = '';
        }
    }
}
add_action('admin_notices', 'mpto_admin_notice__success');

// add admin error notice
function mpto_my_error_notice() {
    if (!isset($_SESSION)) {
        @session_start();
    }
    if (isset($_SESSION['notices']) && !empty($_SESSION['notices']) && ($_SESSION['notices'] != '')) {
        if ($_SESSION['notices']['type'] == 'error') {
            ?>
            <div class="error notice">
                <p><?php _e($_SESSION['notices']['msg'], 'my_plugin_textdomain'); ?></p>
            </div>
            <?php
            $_SESSION['notices']['type'] = '';
        }
    }
}
add_action('admin_notices', 'mpto_my_error_notice');

// limit in content word
function get_excerpt($limit, $source = null,$more = 'more'){

    if($source == "content" ? ($excerpt = get_the_content()) : ($excerpt = get_the_excerpt()));
    if($excerpt == ''){return '';}
    $excerpt = preg_replace(" (\[.*?\])",'',$excerpt);
    $excerpt = strip_shortcodes($excerpt);
    $excerpt = strip_tags($excerpt);
    $excerpt = substr($excerpt, 0, $limit);
    $excerpt = substr($excerpt, 0, strripos($excerpt, " "));
    $excerpt = trim(preg_replace( '/\s+/', ' ', $excerpt));
    $excerpt = $excerpt.' ... <a style="color: #fff;" href="'.get_permalink($post->ID).'">'.$more.'</a>';
    return $excerpt;
}

?>