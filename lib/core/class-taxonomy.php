<?php

/**
 * Copernicus taxonomy class file
 *
 * @package Copernicus
 * @subpackage Copernicus Theme
 * @author Piotr Soluch
 */

/**
 * taxonomy class
 *
 * @package Copernicus
 * @subpackage Copernicus Theme
 * @author Piotr Soluch
 */
class CP_Taxonomy {
	
	var $taxonomy = array();
	
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

		if (isset (CP::$config['taxonomy'])) {
			
			// create taxonomies
			add_action('after_setup_theme', array($this, 'create_taxonomies'));
			add_filter('pre_get_posts', array($this, 'order_posts'));
		}
	}
	
	/**
	 * Start adding taxonomies
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	function create_taxonomies() {
		
		// if there are taxonomies
		if (is_array(CP::$config['taxonomy'])) {

			// for each taxonomy
			foreach(CP::$config['taxonomy'] AS $taxonomy) {
				
				// if taxonomy is active
				if ($taxonomy['settings']['active'])

					// create meta box groups
					$this->add_taxonomy($taxonomy);
			}
		}
	}
	
	/**
	 * Add taxonomy
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	function add_taxonomy($taxonomy) {

		$taxonomy['args']['labels'] = $taxonomy['labels'];
		
		// do the registration
		register_taxonomy(
			$taxonomy['settings']['id'],
			$taxonomy['settings']['post_type'],
			$taxonomy['args']
		);
	}

	public function order_posts($wp_query) {
		if (is_array(CP::$config['taxonomy'])) {

			// for each alv
			foreach (CP::$config['taxonomy'] AS $taxonomy) {
				if (isset($taxonomy['settings']['id']) && isset($wp_query->query_vars[$taxonomy['settings']['id']])) {
					if (isset($taxonomy['args']['orderby'])) {
						$wp_query->set('orderby', $taxonomy['args']['orderby']);
					}
					if (isset($taxonomy['args']['order'])) {
						$wp_query->set('order', $taxonomy['args']['order']);
					}
					if (isset($taxonomy['args']['meta_key'])) {
						$wp_query->set('meta_key', $taxonomy['args']['meta_key']);
					}
				}
			}
		}
	}
}