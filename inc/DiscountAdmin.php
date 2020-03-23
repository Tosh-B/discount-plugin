<?php

add_filter('show_admin_bar', '__return_false');
add_action('admin_init', 'vl_init');
add_action('admin_menu', 'add_vl_plugin_init');
$options = get_option('vl_options'); 
 
function vl_init() {
    register_setting('vl_theme_options', 'vl_options', 'vl_validate_options');
}

function add_vl_plugin_init(){
	add_menu_page( 'Discount Settings', 'Discount Settings', 'manage_options', 'discount-options', 'vl_options', '', null );

}

function vl_validate_options($input) {
    return $input;
}  

function vl_options() {
    global $options;
    ?>
    <div class="wrap">
        <h2><?php _e('Discount Settings') ?></h2>
        <form method="post" action="options.php">
    <?php settings_fields('vl_theme_options'); ?>
            <table class="form-table">
				<tr>
                    <th scope="row"><?php _e('Discount when client register') ?></th>
                    <td>
                        <input type="text" size="70" name="vl_options[vl_register]" value="<?php echo $options['vl_register']; ?>" />
                    </td>
				</tr>
				<tr>
                    <th scope="row"><?php _e('Discount when client returned to the site. Time period (00:00 - 23:59)') ?></th>
                    <td>
                        <input type="text" size="70" name="vl_options[vl_period]" value="<?php echo $options['vl_period']; ?>" />
                    </td>
				</tr>
				<tr>
                    <th scope="row"><?php _e('Discount when client exit site') ?></th>
                    <td>
                        <input type="text" size="70" name="vl_options[vl_exit]" value="<?php echo $options['vl_exit']; ?>" />
                    </td>
				</tr>
            </table>
            <p class="submit">
                <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>
        </form>
    </div>
    <?php }
	
	