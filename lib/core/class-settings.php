<?php

class CP_Settings {

	private $settings = array();

	function __construct() {


		$this->_init();
	}

	function _init() {

		if (isset(CP::$config['settings'])) {
			$this->settings = CP::$config['settings'];

			//new dBug($this->settings);
			add_action( 'admin_init', array($this, 'register_settings') );
			add_action( 'admin_menu', array(&$this, 'admin_menu') );
		}
	}

	function register_settings() {
		if (isset($this->settings)) {

			foreach ($this->settings as $key => $setting) {
				if ($setting['active']) {
					foreach ($setting['tabs'] as $tkey => $tab) {
						if ($tab['active']) {
							register_setting( $key.'_'.$tkey, $key.'_'.$tkey );
						}
					}
				}
			}
		}
	}

	function admin_menu() {
		if (isset($this->settings)) {

			foreach ($this->settings as $key => $setting) {
				if ($setting['active']) {
					add_options_page($setting['name'], $setting['name'], $setting['capability'], $key, array(&$this, 'template_options_page'));
				}
			}
		}
	}

	function template_options_page() {
		global $CP_Field;

		if (!isset($_GET['page'])) {
			return null;
		}

		$settings_id = $_GET['page'];
		if (!isset($this->settings[$settings_id])) {
			return null;
		}

		$settings = $this->settings[$settings_id];
		
		$current_tab = array_shift(array_keys($settings['tabs']));
		if (isset($_GET['tab'])) {
			$current_tab = $_GET['tab'];
		}

		$current_settings = $settings['tabs'][$current_tab];
		$values = get_option( $settings_id.'_'.$current_tab );

		echo '<div class="wrap">';
		echo '<h2>'.$settings['name'].' › ';

		echo $settings['tabs'][$current_tab]['name'];

		echo '</h2>';
		
		echo '<ul class="subsubsub">';
		foreach ($settings['tabs'] as $key => $tab) {
			if ($tab['active']) {
				echo '<li><a href="?page='.$settings_id.'&tab='.$key.'"';
				if ($key == $current_tab) {
					echo ' class="current"';
				}
				echo '>'.$tab['name'].'</a>';
				if ($key != key(array_slice($settings['tabs'], -1, 1, true))) {
					echo ' | ';
				}
				echo '</li>';
			}
		}
		echo '</ul>';

		echo '<form action="options.php" method="post">';
		echo '<table class="form-table"><tbody>';

		if (isset($current_settings['fields']) && is_array($current_settings['fields'])) {
			
			foreach ($current_settings['fields'] as $key => $field) {
				echo '<tr valign="top">';
				echo '<th scope="row"><label for="cp_'.$settings_id.'_'.$current_tab.'_'.$field['id'].'">'.$field['name'].'</labe></th>';
				echo '<td>';
				if (!isset($values[$field['id']])) {
					$values[$field['id']] = '';
				}
				echo $CP_Field->show_field($field, $settings_id.'_'.$current_tab.'_'.$field['id'], $settings_id.'_'.$current_tab.'['.$field['id'].']', $values[$field['id']]);
				echo '</td>';
				echo '</tr>';
			}
		}
		echo '</tbody></table>';
		echo '<input type="submit" name="submit" id="submit" class="button button-primary" value="Save changes">';
		settings_fields($settings_id.'_'.$current_tab);
		echo '</form>';
		echo '</div>';
	}
}