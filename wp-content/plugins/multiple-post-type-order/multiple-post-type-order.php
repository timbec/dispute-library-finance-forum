<?php

/**

 Plugin Name: Multiple Post Type Order
 Plugin URI: http://wordpress.org/plugins/multiple-post-type-order/
 Description: Multiple Post Type Order plugin will generate multiple re-ordering interface for your same post types as well as individual custom post types as many times as you want.
 Version: 1.7.0
 Author: Satish & Dhaval
 Author URI:
 Text Domain: multiple-post-type-order
 License: GPL3

 Multiple Post Type Order is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 2 of the License, or
 any later version.
 
 Multiple Post Type Order is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.
 
 You should have received a copy of the GNU General Public License
 along with Multiple Post Type Order. If not, see https://wordpress.org/plugins/multiple-post-type-order/.
 */

// only include add-on once
define( 'MPTO_PATH', plugin_dir_path(__FILE__) );
require_once( MPTO_PATH . 'mpto-option.php' );
require_once( MPTO_PATH . 'mpto-function.php' );
require_once( MPTO_PATH . 'mpto-shortcode.php' );
require_once( MPTO_PATH . 'mpto-list.php' );

/**
 * Admin side js
 * Drag and Drop Custom MPTO JS
 */
define( 'MPTO_URL', plugins_url('', __FILE__));
function mpto_enqueue() {
    wp_enqueue_script('jquery-ui-sortable');
}
add_action('admin_enqueue_scripts', 'mpto_enqueue');

// Admin side css add 
function mpto_ev_load_custom_wp_admin_style() {
    wp_register_style('style', MPTO_URL . '/css/style.css', false, '1.0.0');
    wp_enqueue_style('style');
}
add_action('admin_enqueue_scripts', 'mpto_ev_load_custom_wp_admin_style');


// General Option
add_action('admin_menu', 'mpto_register_submenu_page');
function mpto_register_submenu_page() {
    $mpto_options = unserialize(get_option('mpto_options'));
    $order_options = unserialize(get_option('order_options'));
    if ($mpto_options != '') {
        foreach ($mpto_options as $key => $val) {
            if ($val == 'show') {
                if ($order_options[$key] != '') {
                    for ($i = 1; $i <= $order_options[$key]; $i++) {
                        if ($key != 'post') {
                            add_submenu_page('edit.php?post_type=' . $key, 'MPT Order ' . $i, 'MPT Order ' . $i, 'manage_options', 'mpto_list_' . $key . '-' . $i, 'mpto_list');
                        } else {
                            add_submenu_page('edit.php', 'MPT Order ' . $i, 'MPT Order ' . $i, 'manage_options', 'mpto_list_post' . '-' . $i, 'mpto_list');
                        }
                    }
                }
            }
        }
    }
    add_options_page('Multiple Post Type Order', 'Multiple Post Type Order', 'manage_options', 'mpto-options', 'mpto_plugin_options');
}

/* Add meta box */
add_action('add_meta_boxes', 'mpto_add_custom_field_metabox');
function mpto_add_custom_field_metabox() {
	// Add meta box  goes into our admin_init function
        $mpto_options = unserialize(get_option('mpto_options'));
        $post_type_name = get_post_type( get_the_ID());
        if((!empty($mpto_options)) && ($mpto_options != ''))
        {
            if($mpto_options[$post_type_name] == 'show')
            {
                add_meta_box(   'mpto_custom_field', __('Order Fields'),  'mpto_custom_field_metabox', $post_type_name, 'normal', 'high');
            }
        }
}
function mpto_custom_field_metabox($post) {
   $post_type_name = get_post_type( get_the_ID());
   $order_options = unserialize(get_option('order_options'));
   $no_of_order = 0;
   $no_of_order = $order_options[$post_type_name];
   if($no_of_order > 0)
   {
    for($i=1; $i<= $no_of_order; $i++)
    {
        ?>
        <input type="text" name="custom_order_type_snv_<?php echo $i;?>" value="<?php $orde_no = get_post_meta(get_the_ID(), 'custom_order_type_snv_'.$i, true);if($orde_no != ''){echo $orde_no;}else{echo '0';}?>">
        <?php 
    }
   }
}


// On post save, save data

add_action('save_post', 'mpto_save_postdata', 10, 2);

function mpto_save_postdata($post_id) {

   $post_type_name = get_post_type( get_the_ID());
   $order_options = unserialize(get_option('order_options'));
   $no_of_order = 0;
   $no_of_order = $order_options[$post_type_name];
   if($no_of_order > 0)
   {
    for($i=1; $i<= $no_of_order; $i++)
    {
        update_post_meta($post_id,'custom_order_type_snv_'.$i,intval($_POST['custom_order_type_snv_'.$i]));
    }
   }
}

/* Frontend CSS */
define( 'MPTO_DIR_URL', plugin_dir_url( __FILE__ ));
add_action('wp_enqueue_scripts', 'mpto_post_listing_styles');
/**
 * Post listing style sheets .
 */

function mpto_post_listing_styles() {

    wp_register_style('post_listing_css', MPTO_DIR_URL . 'css/post_listing_css.css');
    wp_enqueue_style('post_listing_css');
    wp_register_style('main_css_file', MPTO_DIR_URL . 'css/pmto_dem.css');
    wp_enqueue_style('main_css_file');
    wp_register_style('main1_css_file', MPTO_DIR_URL . 'css/normalize.css');
    wp_enqueue_style('main1_css_file');
    wp_register_style('main2_css_file', MPTO_DIR_URL . 'css/style_one.css');
    wp_enqueue_style('main2_css_file');
    wp_register_style('main3_css_file', MPTO_DIR_URL . 'css/style_two.css');
    wp_enqueue_style('main3_css_file');
    
}
?>