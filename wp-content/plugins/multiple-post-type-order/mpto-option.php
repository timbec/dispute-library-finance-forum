<?php
// All option
function mpto_plugin_options() {

    $mpto_options = unserialize(get_option('mpto_options'));
    $order_options = unserialize(get_option('order_options'));
    ?>
    <div id="cpto" class="wrap"> 
        <div id="icon-settings" class="icon32"></div>
        <h2><?php _e('General Settings', 'multiple-post-type-order') ?></h2>
        <form id="form_data" name="form" method="post">   
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row" ><label><?php _e('Show / Hide Re-Order Interface', 'multiple-post-type-order') ?></label></th>
                        <th scope="row" ><label><?php _e('Re-Order no. of List', 'multiple-post-type-order') ?></label></th>
                    </tr>
    <?php
    $post_types = get_post_types();
    foreach (@$post_types as $post_type_name) {
        //ignore list
        $ignore_post_types = array(
            'reply',
            'topic',
            'report',
            'status',
            'shop_order',
            'shop_coupon',
            'shop_webhook',
            'attachment',
            'popup_theme',
            'acf'
        );

        if (in_array($post_type_name, $ignore_post_types))
            continue;

        $post_type_data = get_post_type_object($post_type_name);
        if ($post_type_data->show_ui === FALSE)
            continue;
        ?>
                        <tr valign="top">
                            <td>
                                <p><label>
                                        <select name="show_reorder_interfaces[<?php echo $post_type_name ?>]">
                                            <option value="hide" <?php if ($mpto_options[$post_type_name] == 'hide') {
                    echo ' selected="selected"';
                } ?>><?php _e("Hide", 'mpto') ?></option>
                                            <option value="show" <?php if ($mpto_options[$post_type_name] == 'show') {
                    echo ' selected="selected"';
                } ?>><?php _e("Show", 'mpto') ?></option>
                                        </select> &nbsp;&nbsp;<?php echo $post_type_data->labels->singular_name ?>
                                    </label>
                            </td>
                            <td>
                                <label>
                                    <input type="number" name="re-re-no-of-order[<?php echo $post_type_name ?>]" value="<?php if ($order_options[$post_type_name] == '') {
                    echo '1';
                } else {
                    echo $order_options[$post_type_name];
                } ?>">
                                    &nbsp;&nbsp;<?php echo $post_type_data->labels->singular_name ?>
                                </label>
                            </td>
                        </tr>
    <?php } ?>



                </tbody>
            </table>
			
            <p class="submit">
                <button onclick="fn_option_save();" class="button button-primary button-large" type="button" name="Save-Settings"><?php 
                            _e('Save Settings', 'multiple-post-type-order') ?></button>
            </p>
			<?php wp_nonce_field('mpto_form_submit','mpto_form_nonce'); ?>
            <input type="hidden" name="form_submit" value="true" />
            <input type="hidden" name="action" value="mpto_save_option"/>

        </form>

        <br />
        <script type="text/javascript">
            function fn_option_save() {
                var form_data = jQuery('#form_data').serialize();
                jQuery.ajax({
                    type: "POST",
                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                    data: form_data,
                    success: function (data) {
                        window.location.reload(true);
                    }
                    //window.location.reload();
                });
                return false;
            }
        </script>
    </div>        
    <?php
}

/*  Message store in session */
add_action('wp_ajax_mpto_save_option', 'mpto_save_option');
function mpto_save_option() {
    session_start();
    if (isset($_POST['form_submit']) &&  wp_verify_nonce($_POST['mpto_form_nonce'],'mpto_form_submit')) {
        $options = isset($_POST['show_reorder_interfaces']) ? sanitize_text_field(serialize($_POST['show_reorder_interfaces'])) : '';
        update_option('mpto_options', $options);
        $order_options = isset($_POST['re-re-no-of-order']) ? sanitize_text_field(serialize($_POST['re-re-no-of-order'])) : '';
        update_option('order_options', $order_options);
        $_SESSION['notices'] = array('type' => 'success', 'msg' => 'Setting saved successfully.');
    } else {
        $_SESSION['notices'] = array('type' => 'error', 'msg' => 'Failed to saved setting.');
    }
}
?>