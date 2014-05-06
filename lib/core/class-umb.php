<?php

/**
 * My Story user meta box
 *
 * @package My Story
 * @subpackage My Story Theme
 * @author Piotr Soluch
 */

/**
 * user meta box class
 *
 * @package My Story
 * @subpackage My Story Theme
 * @author Piotr Soluch
 */
class CP_Umb {
	
	var $umb = array();
	
	/**
	 * Class constructor
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function __construct() {

		// initialize the custom post types
		$this->_init();
	}

	/**
	 * Initiate taxonomies
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function _init() {
		
		if (isset (CP::$config['umb'])) {
			$this->umb = CP::$config['umb'];
			
			// create taxonomies
			add_action('edit_user_profile', array($this, 'add_user_meta_boxes'));
			add_action('show_user_profile', array($this, 'add_user_meta_boxes'));
			add_action('profile_update', array($this, 'update_user_meta'));
			//add_action('show_user_profile', array($this, 'add_user_meta_boxes'));
		}
	}
	
	/**
	 * Start adding user meta boxes
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function add_user_meta_boxes($user) {

		// if there are meta boxes
		if (is_array($this->umb)) {

			// for each meta box
			foreach ($this->umb AS $umb) {

				// if meta box is active
				if ($umb['settings']['active']) {
					// create meta box groups
					$this->add_user_meta_box_group($umb, $user->ID);
				}
			}
		}
	}
	
	/**
	 * Add user meta box group
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @param $umb array all details of user meta box
	 * @author Piotr Soluch
	 */
	function add_user_meta_box_group($umb, $user_id) {
		global $CP_Field;
		
		if (!isset($umb['fields']) || !is_array($umb['fields'])) {
			return null;
		}

		$return = '<table class="form-table"><tbody>';

		foreach ($umb['fields'] as $field) {
			$return.= '<tr><th>';
			$return.= '<label for="cp_user_meta_'.$field['id'].'">'.$field['name'].'</label></th><td>';
			$return.= $CP_Field->show_field( $field, 'cp_user_meta_'.$field['id'], 'cp_user_meta_'.$field['id'], get_user_meta( $user_id, $field['id'], true ) );
			$return.= '</td></tr>';
		}

		$return.= '</tbody></table>';

		echo $return;
	}

	public function update_user_meta($user_id) {
		// if there are meta boxes
		if (is_array($this->umb)) {

			// for each meta box
			foreach ($this->umb AS $umb) {

				// if meta box is active
				if ($umb['settings']['active']) {

					if (isset($umb['fields']) && is_array($umb['fields'])) {
						
						foreach ($umb['fields'] as $field) {

							update_user_meta( $user_id, $field['id'], $_POST['cp_user_meta_'.$field['id']] );
						}
					}
				}
			}
		}
	}
}