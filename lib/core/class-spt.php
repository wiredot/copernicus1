<?php

/**
 * Copernicus Custom Post Type class file
 *
 * @package Copernicus
 * @subpackage Copernicus Theme
 * @author Piotr Soluch
 */

/**
 * Custom Post Type class
 *
 * @package Copernicus
 * @subpackage Copernicus Theme
 * @author Piotr Soluch
 */
class CP_Spt {

	// part of config with all cpts
	private $spt = array();

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
		add_action('init', array($this, 'support'));
		
	}	

	function support() {
		global $CP_Language;

		// if more than 1 active language
		if ($CP_Language->get_language_count() > 1) {
			remove_post_type_support('page', 'title');
			remove_post_type_support('page', 'editor');
			
			remove_post_type_support('post', 'title');
			remove_post_type_support('post', 'editor');
		}
	}
}
