<?php
/*
Plugin Name: AMP Google map
Plugin URI: 
Description: Add marker point to Google map
Author: Alex Semenov <alexbalance@gmail.com>
Author URI: 
Version: 1.0

Plugin is distrubuted according to the terms of GNU General Public License.
*/ 

register_activation_hook(__FILE__, 'create_table');
register_deactivation_hook(__FILE__, 'delete_table');


function create_table() {
    global $wpdb;
  	$version = get_option( 'plugin_version', '1.0' );
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'markers';
	if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		$sql ="CREATE TABLE `markers` (
				  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				  `name` VARCHAR( 60 ) NOT NULL ,
				  `address` VARCHAR( 80 ) NOT NULL ,
				  `lat` FLOAT( 10, 6 ) NOT NULL ,
				  `lng` FLOAT( 10, 6 ) NOT NULL ,
				  `type` VARCHAR( 30 ) NOT NULL ,
				  UNIQUE KEY id (id)
				) $charset_collate;
				";
	}
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

function delete_table() {
    // not used now
}

function custom_table_example_install_data()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'cte'; // do not forget about tables prefix

    $wpdb->insert($table_name, array(
        'name' => 'Alex',
        'email' => 'alex@example.com',
        'age' => 25
    ));
    $wpdb->insert($table_name, array(
        'name' => 'Maria',
        'email' => 'maria@example.com',
        'age' => 22
    ));
}
// create custom plugin settings menu
function amp_create_menu() {
	add_options_page('AMP Google map', 'AMP Google map', 'edit_pages', __FILE__, 'plugin_options_page');
	add_action( 'admin_init', 'plugin_settings' );
}
add_action('admin_menu', 'amp_create_menu');
 


function plugin_settings() {
	//register our settings
	register_setting( 'amp-settings-group', 'option1' );
	register_setting( 'amp-settings-group', 'option2' );

}
//INSERT INTO `markers` (`name`, `address`, `lat`, `lng`, `type`) VALUES ('Love.Fish', '580 Darling Street, Rozelle, NSW', '-33.861034', '151.171936', 'restaurant');
function plugin_options_page() {
?>
<div class="wrap">
<h2>AMP Google map</h2>

<form id="form" method="POST" >
    <?php settings_fields( 'amp-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Option 1</th>
        <td><input type="text" name="option1" value="<?php echo get_option('option1'); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Option 2</th>
        <td><input type="text" name="option2" value="<?php echo get_option('option2'); ?>" /></td>
        </tr>
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>
<?php } ?>