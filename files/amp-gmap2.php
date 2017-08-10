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

/**
 * Add new markers to table
 *
 * https://codex.wordpress.org/Class_Reference/wpdb
 */
function create_table() {
    global $wpdb;
  	$version = get_option( 'plugin_version', '1.0' );
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix .'markers';
	if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		$sql ="CREATE TABLE ". $table_name ." (
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


// create custom plugin settings menu
function create_menu() {
	add_options_page('AMP Google map', 'AMP Google map', 'edit_pages', __FILE__, 'plugin_options_page');
	add_action( 'admin_init', 'plugin_settings' );
	
	//add_menu_page('Page title', 'Top-level menu title', 8, __FILE__, 'my_magic_function');
	//add_submenu_page(__FILE__, 'Page title', 'Sub-menu title', 8, __FILE__, 'my_magic_function');
}
add_action('admin_menu', 'create_menu');
 
function plugin_settings() {
	//register our settings
	/*register_setting( 'amp-settings-group', 'name' );
	register_setting( 'amp-settings-group', 'address' );
	register_setting( 'amp-settings-group', 'lat' );
	register_setting( 'amp-settings-group', 'lng' );
	register_setting( 'amp-settings-group', 'type' );*/

}
/**
 * Add new markers to table
 *
 * https://codex.wordpress.org/Class_Reference/wpdb
 */
function add_data() {
	global $wpdb;
    $table_name = $wpdb->prefix . 'markers'; 
    $message = '';
    $notice = '';
	// form post values array
    $item = array(
		'name'    => $_POST['name'],
        'address' => $_POST['address'],
        'lat' 	  => $_POST['lat'],
		'lng'     => $_POST['lng'],
		'type'    => $_POST['type']
	);
	
	$result = $wpdb->insert($table_name, $item, array( '%s', '%s', '%f', '%f', '%s' ));
	if ($result){
		$message = __('Item was successfully saved', 'custom_table_example');
	} else {
		$message = __('There was an error while saving item', 'custom_table_example');
	}
    wp_redirect($_SERVER['HTTP_REFERER']);   

	        
}
//add_action( 'admin_post_add_items', 'add_data' );

function clear_data($item, $type="s"){
	switch($type){
		case "s":
			return mysql_real_escape_string(trim(strip_tags($item))); break;
		case "i":
			return (int)$item; break;               
	}
}

function plugin_options_page() {
?>
<div class="wrap">
<h2>Add new marker point</h2>

<?php //if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
<?php //endif;?>

<form id="form" action="<?php admin_url('options-general.php?page=amp-gmap/amp-gmap.php'); ?>" >
    <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
		<tbody>
		<tr class="form-field">
			<th valign="top" scope="row">
				<label for="name"><?php _e('Name', 'amp')?></label>
			</th>
			<td>
				<input id="name" name="name" type="text" style="width: 95%" size="50" 
				placeholder="<?php _e('Name', 'amp'); ?>" onfocus="this.placeholder = ''" onblur="this.placeholder = '<?php _e('Name', 'amp'); ?>'" required>
			</td>
		</tr>
		
		
		
		</tbody>
	</table>
	<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
    <!--<input type="hidden" name="action" value="add_items">-->
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
<?php
echo $_GET["name"];
?>
</div>
<?php } 

function validate_data($item){
    $messages = array();

    if (empty($item['name'])) $messages[] = __('Name is required', 'custom_table_example');
    if (empty($messages)) return true;
    return implode('<br />', $messages);
}

?>