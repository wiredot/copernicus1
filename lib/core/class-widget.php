<?php

/**
 * Widget class file
 *
 * @package Copernicus
 * @author Piotr Soluch
 */

/**
 * Widget class
 *
 * @package Copernicus
 * @author Piotr Soluch
 */
class CP_Widget {

	var $widgets;

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
	 * Initiate
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function _init() {
		
		if (isset (CP::$config['widget'])) {
			$this->widgets = CP::$config['widget'];
		}
		
		// create user roles
		add_action('widgets_init', array($this, 'add_widgets'));
	}

	/**
	 * Add widgets
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function add_widgets() {

		// if there are user roles
		if (is_array($this->widgets)) {

			// for each user role
			foreach ($this->widgets AS $widget) {

				// if user role is active
				if ($widget['active'])

				// create meta box groups
					$this->add_widget($widget);
			}
		}
	}

	public function add_widget($widget) {
		register_sidebar( $widget );
	}

}

?>