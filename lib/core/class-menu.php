<?php

/**
 * Copernicus meta box class file
 *
 * @package Copernicus
 * @subpackage Copernicus Theme
 * @author Piotr Soluch
 */

/**
 * meta box class
 *
 * @package Copernicus
 * @subpackage Copernicus Theme
 * @author Piotr Soluch
 */
class CP_Menu {

	// all meta boxes
	private $menu = array();

	/**
	 * Class constructor
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function __construct() {
		
		// initialize the meta boxes
		$this->_init();
	}

	/**
	 * Initiate the meta boxes
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function _init() {
		
		// setup custom fields for nav menu
		add_filter('wp_setup_nav_menu_item', array($this, 'add_custom_nav_fields'));
		
		// update custom fields on save
		add_action('wp_update_nav_menu_item', array( $this, 'update_custom_nav_fields'), 10, 3 );
		
		// custom walker
		add_filter('wp_edit_nav_menu_walker', array( $this, 'edit_walker'), 10, 2);
		
		add_filter('wp_nav_menu_objects', array($this, 'nav_menu_translate'));
		
		

		if (isset (CP::$config['nav_menu'])) {
			
			// get meta box configuration
			$this->register_nav_menus(CP::$config['nav_menu']);
		}
	}

	public function edit_walker($walker,$menu_id) {
	    return 'Walker_Nav_Menu_Edit_Copernicus';
	}

	private function register_nav_menus($nav_menus) {
		foreach ($nav_menus as $menu) {
			register_nav_menu( $menu['location'], $menu['description'] );
		}
	}

	public function get_nav_menu($location) {
		if (isset (CP::$config['nav_menu'])) {
			foreach (CP::$config['nav_menu'] as $menu) {
				if ($menu['location'] == $location) {
					return $menu;
				}
			}
		}

		return null;
	}

	public function get_page_menu($id) {
		if (isset (CP::$config['page_menu'])) {
			foreach (CP::$config['page_menu'] as $menu) {
				if ($menu['id'] == $id) {
					return $menu;
				}
			}
		}

		return null;
	}

	public function get_page_list($id) {
		if (isset (CP::$config['page_list'])) {
			foreach (CP::$config['page_list'] as $menu) {
				if ($menu['id'] == $id) {
					return $menu;
				}
			}
		}

		return null;
	}


/* -------------- custom nav menu fields -------------- */

	public function add_custom_nav_fields( $menu_item ) {
		global $CP_Language;
		$languages = $CP_Language->get_languages();
		if ($languages) {
			foreach ($languages as $language) {
				if ($language['postmeta_suffix']) {
					$title_field = 'title'.$language['postmeta_suffix'];
					$attr_title_field = 'attr_title'.$language['postmeta_suffix'];
					$menu_item->$title_field = get_post_meta( $menu_item->ID, '_menu_item_'.$title_field, true );
					$menu_item->$attr_title_field = get_post_meta( $menu_item->ID, '_menu_item_'.$attr_title_field, true );
				}
			}
		}

		return $menu_item;
	}

	public function update_custom_nav_fields( $menu_id, $menu_item_db_id, $args ) {
		global $CP_Language;
		$languages = $CP_Language->get_languages();
		
		if ($languages) {
			foreach ($languages as $language) {
				if ($language['postmeta_suffix']) {
					$title = 'title'.$language['postmeta_suffix'];
					$attr_title = 'attr_title'.$language['postmeta_suffix'];
					
					if ( !isset($_REQUEST['menu-item-'.$title]) ) {
						$title_value = get_post_meta($args['menu-item-object-id'], 'post_title'.$language['postmeta_suffix'], true);
					} else if ( is_array( $_REQUEST['menu-item-'.$title]) ) {
						$title_value = $_REQUEST['menu-item-'.$title][$menu_item_db_id];
					}

					if (isset($title_value)) {
						update_post_meta( $menu_item_db_id, '_menu_item_'.$title, $title_value );
					}
					
					if ( isset($_REQUEST['menu-item-'.$attr_title]) && is_array( $_REQUEST['menu-item-'.$attr_title]) ) {
						$attr_title_value = $_REQUEST['menu-item-'.$attr_title][$menu_item_db_id];
						update_post_meta( $menu_item_db_id, '_menu_item_'.$attr_title, $attr_title_value );
					}

					unset($title_value);
				}
			}
		}
	}

	public function nav_menu_translate($output) {
		//print_r($output);
		foreach ($output as $key => $value) {
			if (LANGUAGE_SUFFIX != '') {
				$title_field = 'title'.LANGUAGE_SUFFIX;
				$attr_title_field = 'attr_title'.LANGUAGE_SUFFIX;
				if (isset($output[$key]->$title_field) && !empty($output[$key]->$title_field)) {
					$output[$key]->title = $output[$key]->$title_field;
				}
				if (isset($output[$key]->$attr_title_field) && !empty($output[$key]->$attr_title_field)) {
					$output[$key]->attr_title = $output[$key]->$attr_title_field;
				}
			}
		}
		return $output;
	}
}

include_once( 'edit_custom_walker.php' );
