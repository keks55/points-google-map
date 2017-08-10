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

//register_activation_hook(__FILE__, 'create_table');
//register_deactivation_hook(__FILE__, 'delete_table');

$options_page = 'amp-gmap.php';

/*
 * Функция, добавляющая страницу в пункт меню Настройки
 */
function plugin_options() {
	global $options_page;
	add_options_page( 'AMP Google map', 'AMP Google map', 'edit_pages', $options_page, 'plugin_option_page');  
}
add_action('admin_menu', 'plugin_options');

/**
 * Возвратная функция (Callback)
 */ 
function plugin_option_page(){
	global $options_page;
	?><div class="wrap">
		<h2>Дополнительные параметры сайта</h2>
		<form method="POST" action="<?php admin_url('options-general.php?page=amp-gmap/amp-gmap.php'); ?>">
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
			<!--<input type="hidden" name="action" value="add_items"> // for post method-->
			<?php //submit_button(); ?>
			<p class="submit">  
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />  
			</p>
		</form>
		<?php 
		if(isset($_POST["name"])) echo $_POST["name"];
		?>
	</div><?php
}

/*
 * Регистрируем настройки
 * Мои настройки будут храниться в базе под названием true_options (это также видно в предыдущей функции)
 */
/*function plugin_option_settings() {
	global $options_page;
	// Присваиваем функцию валидации ( true_validate_settings() )
	register_setting( 'plugin_options', 'plugin_options', 'validate_settings' ); // true_options
 
	// Добавляем секцию
	add_settings_section( 'section_1', 'Текстовые поля ввода', '', $options_page );
 
	// Создадим текстовое поле в первой секции
	$field_params = array(
		'type'      => 'text', // тип
		'id'        => 'name'
		//'desc'      => 'Пример обычного текстового поля.', 
	);
	add_settings_field( 'my_text_field', 'Name', 'option_display_settings', $options_page, 'section_1', $field_params );
}
add_action( 'admin_init', 'plugin_option_settings' );*/

/*
 * Функция проверки правильности вводимых полей
 */
function validate_settings($input) {
	foreach($input as $k => $v) {
		$valid_input[$k] = trim($v);
 
		/* Вы можете включить в эту функцию различные проверки значений, например
		if(! задаем условие ) { // если не выполняется
			$valid_input[$k] = ''; // тогда присваиваем значению пустую строку
		}
		*/
	}
	return $valid_input;
}
?>
