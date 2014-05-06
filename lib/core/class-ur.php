<?php

/**
 * My Story user roles class file
 *
 * @package My Story
 * @subpackage My Story Theme
 * @author Piotr Soluch
 */

/**
 * user roles class
 *
 * @package My Story
 * @subpackage My Story Theme
 * @author Piotr Soluch
 */
class CP_Ur {

	var $ur;
	
	var $wp_ur;

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
	 * Initiate users
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function _init() {
		
		if (isset (CP::$config['ur'])) {
			$this->ur = CP::$config['ur'];
		}
		
		$wp_roles = new WP_Roles();

		foreach ($wp_roles->roles as $key => $value) {
			$this->wp_ur[] = $key;
		}
		
		// create user roles
		add_action('admin_init', array($this, 'add_user_roles'));
	}

	/**
	 * Start adding meta boxes
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function add_user_roles() {

		// if there are user roles
		if (is_array($this->ur)) {

			// for each user role
			foreach ($this->ur AS $role) {

				// if user role is active
				if ($role['settings']['active']){
					// create meta box groups
					$this->add_user_role($role);
				} else {
					$this->remove_user_role($role);
				}
			}
		}
	}

	public function add_user_role($role) {
		if (!in_array($role['settings']['id'], $this->wp_ur)) {
			add_role($role['settings']['id'], $role['labels']['name'], array());
		}
	}

	public function remove_user_role($role) {
		if (in_array($role['settings']['id'], $this->wp_ur)) {
			remove_role($role['settings']['id']);
		}
	}
}
