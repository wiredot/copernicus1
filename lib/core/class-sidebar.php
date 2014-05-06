<?php

/**
 * Copernicus Sidebar class file
 *
 * @package Copernicus
 * @subpackage Copernicus Theme
 * @author Piotr Soluch
 */

/**
 * Sidebar class
 *
 * @package Copernicus
 * @subpackage Copernicus Theme
 * @author Piotr Soluch
 */
class CP_Sidebar {

	// part of config with all alvs
	private $sidebars = array();

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
	 * Initiate the theme
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function _init() {

		if (isset(CP::$config['sidebar'])) {
			$this->sidebars = CP::$config['sidebar'];
		}
		
		add_action( 'widgets_init', array($this,'register_sidebars') );

	}
	
	function register_sidebars() {
		
		foreach ($this->sidebars as $sidebar) {
			register_sidebar($sidebar);
		}
	}
}