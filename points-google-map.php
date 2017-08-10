<?php
/*
Plugin Name: Points on Google map
Plugin URI: 
Description: Add points (places) to Google map
Author: Alex Semenov <alexbalance@gmail.com>
Author URI: keksus.com
Version: 1.0

Plugin is distrubuted according to the terms of GNU General Public License.
*/ 

// this is an include only WP file
if (!defined('ABSPATH')) {
  die;
}

register_activation_hook(__FILE__, 'create_table');
register_deactivation_hook(__FILE__, 'delete_table');

// Plugin pages
$all_points_page = 'points.php';
$add_point_page  = 'add_point.php';

/*
 * Add new markers to table
 * https://codex.wordpress.org/Class_Reference/wpdb
 */
 
function create_table() {
    global $wpdb;
  	$version = get_option('plugin_version', '1.0');
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix .'pgm';
	if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		$sql ="CREATE TABLE ". $table_name ." (
				  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				  `name` VARCHAR( 60 ) NOT NULL ,
				  `type` VARCHAR( 30 ) NOT NULL ,
				  `address` VARCHAR( 80 ) NOT NULL ,
				  `lat` FLOAT( 10, 6 ) NOT NULL ,
				  `lng` FLOAT( 10, 6 ) NOT NULL ,
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
/*
 * Add plugin page to settings menu
 */
add_action('admin_menu', 'points_menu');
function points_menu() {
	global $all_points_page;
	//add_options_page( 'MP Google map', 'MP Google map', 'manage_options', $all_points_page, 'pgm_option_page');  
	add_menu_page(__('Points on Google map', 'pgm'), __('Points on Google map', 'pgm'), 'activate_plugins', 'points', 'pgm_list_points');
    add_submenu_page('points', __('All points', 'pgm'), __('All points', 'pgm'), 'activate_plugins', 'points', 'pgm_list_points');
    add_submenu_page('points', __('Add new', 'pgm'), __('Add new', 'pgm'), 'activate_plugins', 'add_point', 'pgm_add_point');
}


/*
 * Callback function
 */ 
function pgm_add_point(){
	global $add_point_page;
	global $error;
	$ajax_nonce = wp_create_nonce('pgm-nonce');
	?>
	<script  type='text/javascript'>
		jQuery(document).ready(function($) {
			$('.pgm-ajax-form').on('submit', function(e) {
				e.preventDefault();
				jQuery.ajax({
					type: "post",
					url: ajaxurl,
					data: { 
						action: 'validate', 
						name: jQuery( '#name' ).val(), 
						type: jQuery( '#type' ).val(), 
						address: jQuery( '#address' ).val(), 
						lat: jQuery( '#lat' ).val(), 
						lng: jQuery( '#lng' ).val(), 
						//form_data: $('.pgm-ajax-form').serialize(),
						_ajax_nonce: '<?php echo $ajax_nonce; ?>' 
					},
					beforeSend: function() {
							jQuery("#loading").appendTo("#load").fadeIn('fast');
							jQuery("#formstatus").fadeOut("slow");
					},
					success: function(html){ 
						jQuery("#loading").appendTo("#load").fadeOut('slow');
						jQuery("#formstatus").html( html ); //show the html inside formstatus div
						jQuery("#formstatus").fadeIn("fast"); 
						//jQuery("#formstatus").fadeOut(5000); 	
					},
					error: function(xhr){
						alert('Error: ' + xhr.responseCode);
					}	             
				}); //close jQuery.ajax
				return false;
			});
			$(".button-reset").on("click", function(event) {
				jQuery("#formstatus").hide();
			});
		});
	</script>
	<?php
	//validate();
	?><div class="wrap">
		<h2><?php _e('Add new point on Google map', 'pgm'); ?></h2>
		<form id="#ajax-add-data" class="pgm-ajax-form" method="POST" action="<?php echo admin_url('admin-ajax.php'); ?>">
			<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
			<tbody>
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="name"><?php _e('Name', 'pgm')?></label>
					</th>
					<td>
						<input id="name" name="name" type="text" style="width: 95%" size="50" 
						placeholder="<?php _e('Name', 'pgm'); ?>" onfocus="this.placeholder = ''" 
						onblur="this.placeholder = '<?php _e('Name', 'pgm'); ?>'" required>
					</td>
				</tr>
				
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="name"><?php _e('Type', 'pgm')?></label>
					</th>
					<td>
						<input id="type" name="type" type="text" style="width: 95%" size="50" 
						placeholder="<?php _e('Type', 'pgm'); ?>" onfocus="this.placeholder = ''" 
						onblur="this.placeholder = '<?php _e('Type', 'pgm'); ?>'" required>
					</td>
				</tr>
				
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="name"><?php _e('Address', 'pgm')?></label>
					</th>
					<td>
						<input id="address" name="address" type="text" style="width: 95%" size="50" 
						placeholder="<?php _e('Address', 'pgm'); ?>" onfocus="this.placeholder = ''" 
						onblur="this.placeholder = '<?php _e('Address', 'pgm'); ?>'" required>
					</td>
				</tr>
				
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="name"><?php _e('Latitude point', 'pgm')?></label>
					</th>
					<td>
						<input id="lat" name="lat" type="text" style="width: 95%" size="50" 
						placeholder="<?php _e('Must be only number', 'pgm'); ?>" onfocus="this.placeholder = ''" 
						onblur="this.placeholder = '<?php _e('Must be only number', 'pgm'); ?>'" required>
					</td>
				</tr>
				
				<tr class="form-field">
					<th valign="top" scope="row">
						<label for="name"><?php _e('Longitude point', 'pgm')?></label>
					</th>
					<td>
						<input id="lng" name="lng" type="text" style="width: 95%" size="50" 
						placeholder="<?php _e('Must be only number', 'pgm'); ?>" onfocus="this.placeholder = ''" 
						onblur="this.placeholder = '<?php _e('Must be only number', 'pgm'); ?>'" required>
					</td>
				</tr>
				
			</tbody>
			</table>
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
			<div class="buttons">  
				<input class='button-reset' type='reset' name='clear' id='res' value='<?php _e('Clear', 'pgm'); ?>' /> 
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />  
				<span id='load'>
					<div id='loading'>LOADING!</div>
				</span>
			</div>
			
		</form>
		<div id='formstatus'></div>
	</div><?php	  
}

/*
 * Validation and purification of input data
 */
add_action( 'wp_ajax_validate', 'validate' );
function validate(){
	global $wpdb;
	$table_name = $wpdb->prefix .'pgm';
	check_ajax_referer( 'pgm-nonce' );
	if($_SERVER["REQUEST_METHOD"]=="POST"){	
	
		$name    = sanitize_text_field($_POST['name']);
		$type    = sanitize_text_field($_POST['type']);
		$address = sanitize_text_field($_POST['address']);
		$lat     = sanitize_text_field($_POST['lat']);
		$lng     = sanitize_text_field($_POST['lng']);

		if (ctype_digit($name)){
			echo '<div class="error" id="notice"><p>'. __( 'Name wrong format.', 'pgm' ) .'</p></div>';
			wp_die();
		}
	    elseif (ctype_digit($type)){
			echo '<div class="error" id="notice"><p>'. __( 'Type wrong format.', 'pgm' ) .'</p></div>';
			wp_die();
		}
		elseif (ctype_digit($address)){
			echo '<div class="error" id="notice"><p>'. __( 'Address wrong format.', 'pgm' ) .'</p></div>';
			wp_die();
		}
		elseif (!filter_var($lat,FILTER_VALIDATE_FLOAT)){
			echo '<div class="error" id="notice"><p>'. __( 'Latitude must be a number.', 'pgm' ) .'</p></div>';
			wp_die();
		}
		elseif (!filter_var($lng,FILTER_VALIDATE_FLOAT)){
			echo '<div class="error" id="notice"><p>'. __( 'Longitude must be a number.', 'pgm' ) .'</p></div>';
			wp_die();
		}
		/*else{
			echo '<div class="updated" id="notice"><p>'. __( 'Saved.', 'pgm' ) .'</p></div>';
			wp_die();
		}*/
		//add valid data to array
		$data = array(
			'name'    => $name,
			'type'    => $type,
			'address' => $address,
			'lat' 	  => $lat,
			'lng'     => $lng
		);
		
		//print_r($item);
		//$result = $wpdb->insert($wpdb->pgm, $data, array( '%s', '%s', '%s', '%f', '%f' ));
/*
define("DB_HOST", "localhost");
define("DB_LOGIN", "root");
define("DB_PASSWORD", "000");
define("DB_NAME", "wp41");
$conn = mysql_connect(DB_HOST, DB_LOGIN, DB_PASSWORD) or die("Не удалось соединиться с базой данных!");
mysql_query("SET NAMES 'utf8'");
mysql_select_db(DB_NAME) or die(mysql_error());
$sql = "INSERT INTO wp_pgm(name,type,address,lat,lng) 
						VALUES('$name','$type','$address','$lat','$lng')
					";
$result = mysql_query($sql) or die(mysql_error());
*/
/*
$sql = $wpdb->prepare("INSERT INTO wp_pgm (name,type,address,lat,lng) 
						VALUES('$name','$type','$address','$lat','$lng')
					");

		$result = $wpdb->query($sql);
*/
//$result = $wpdb->update($table_name, $data);
//$result = $wpdb->insert($wpdb->pgm, $data, array( '%s', '%s', '%s', '%f', '%f' ));
		$sql = $wpdb->prepare("INSERT INTO $table_name (name,type,address,lat,lng) 
								VALUES('%s','%s','%s','%f','%f')", $data
							 );
		$result = $wpdb->query($sql);
		if ($result){
			echo '<div class="updated" id="notice"><p>'. __( 'Saved.', 'pgm' ) .'</p></div>';
			wp_die();
		} else {
			echo '<div class="error" id="notice"><p>'. __( 'There was an error while saving items.', 'pgm' ) .'</p></div>';
		}
		//wp_redirect($_SERVER['HTTP_REFERER']); 
	}
}

add_action( 'admin_init','scripts');
function scripts() {
	wp_enqueue_style('pgm-style', plugins_url('css/pgm-style.css',__FILE__ ));
	//wp_enqueue_script('scripts', plugins_url('js/add_data.js',__FILE__ ));

	//wp_localize_script( 'register_script', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin_ajax.php' )));
}


/**
 * Defining Custom Table List
 * ============================================================================
 *
 * In this part you are going to define custom table list class,
 * that will display your database records in nice looking table
 *
 * http://codex.wordpress.org/Class_Reference/WP_List_Table
 * http://wordpress.org/extend/plugins/custom-list-table-example/
 */

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * pgm_List_Table class that will display our custom table
 * records in nice table
 */
class Show_List_Table extends WP_List_Table{
	
    // [REQUIRED] You must declare constructor and give some basic params
    function __construct(){
        global $status, $page;
        parent::__construct(array(
            'singular' => 'point',
            'plural' => 'points',
        ));
    }

    /**
     * [REQUIRED] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_default($item, $column_name){
        return $item[$column_name];
    }
	
	/**
     * [REQUIRED] this is how checkbox column renders
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }
	
    /**
     * [OPTIONAL] this is example, how to render column with actions,
     * when you hover row "Edit | Delete" links showed
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_name($item)
    {
        // links going to /admin.php?page=[your_plugin_page][&other_params]
        // notice how we used $_REQUEST['page'], so action will be done on curren page
        // also notice how we use $this->_args['singular'] so in this example it will
        // be something like &person=2
        $actions = array(
            'edit' => sprintf('<a href="?page=add_point&id=%s">%s</a>', $item['id'], __('Edit', 'pgm')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Delete', 'pgm')),
        );

        return sprintf('%s %s',
            $item['name'],
            $this->row_actions($actions)
        );
    }
	
    /**
     * [REQUIRED] This method return columns to display in table
     * you can skip columns that you do not want to show
     * like content, or description
     *
     * @return array
     */
    function get_columns(){
        $columns = array(
            'cb'   => '<input type="checkbox" />', //Render a checkbox instead of text
            'name' => __('Name', 'pgm'),
            'type' => __('Type', 'pgm'),
            'address' => __('Address', 'pgm'),
			'lat'  => __('Latitude point', 'pgm'),
            'lng'  => __('Longitude point', 'pgm')
        );
        return $columns;
    }
	
	/**
     * [OPTIONAL] Return array of bult actions if has any
     *
     * @return array
     */
    function get_bulk_actions(){
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    /**
     * [OPTIONAL] This method processes bulk actions
     * it can be outside of class
     * it can not use wp_redirect coz there is output already
     * in this example we are processing delete action
     * message about successful deletion will be shown on page in next part
     */
    function delete_point(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'pgm'; // do not forget about tables prefix

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }
    }
	/**
     * [REQUIRED] This is the most important method
     *
     * It will get rows from database and prepare them to be showed in table
     */
    function prepare_items(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'pgm'; 
        $per_page = 5; // constant, how much records will be shown per page

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
        //$this->process_bulk_action();

        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'name';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name 
														  ORDER BY $orderby $order 
														  LIMIT %d 
														  OFFSET %d", $per_page, $paged), ARRAY_A);

        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, 
            'per_page' => $per_page, 
            'total_pages' => ceil($total_items / $per_page) 
        ));
    }
}

/**
 * List page handler
 *
 * This function renders our custom table
 * Notice how we display message about successfull deletion
 * Actualy this is very easy, and you can add as many features
 * as you want.
 *
 * Look into /wp-admin/includes/class-wp-*-list-table.php for examples
 */

function pgm_list_points(){
    global $wpdb;
	global $all_points_page;
    $table = new Show_List_Table();
    $table->prepare_items();
	//print_r($table->prepare_items());

    $message = '';
    if ('delete' === $table->current_action()) {
		
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d', 'pgm'), count($_REQUEST['id'])) . '</p></div>';
    }
    ?>
<div class="wrap">

    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Points', 'pgm')?> <a class="add-new-h2"
                                 href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=add_point');?>"><?php _e('Add new', 'pgm')?></a>
    </h2>
    <?php echo $message; ?>

    <form id="persons-table" method="GET">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $table->display() ?>
    </form>

</div>
<?php
}
?>
