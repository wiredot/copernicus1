<?php

/**
 * Copernicus permalinks class file
 *
 * @package Copernicus
 * @subpackage Copernicus Theme
 * @author Piotr Soluch
 */

/**
 * permalinks class
 *
 * @package Copernicus
 * @subpackage Copernicus Theme
 * @author Piotr Soluch
 */
class CP_Permalink {
	
	var $rewrite_tag = array();
	var $rewrite_rule = array();
	
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

		if (isset(CP::$config['rewrite_tag'])) {
			$this->rewrite_tag = CP::$config['rewrite_tag'];

			add_action('init', array($this, 'add_rewrite_tags'));
		}

		if (isset(CP::$config['rewrite_rule'])) {
			$this->rewrite_rule = CP::$config['rewrite_rule'];

			add_action('init', array($this, 'add_rewrite_rules'));
		}
	}

	function add_rewrite_tags() {
		global $wp_rewrite;
	
	// add rewrite tokens
	$keytag_token = '%tagggg%';
	$wp_rewrite->add_rewrite_tag( $keytag_token, '(.+)', 'tagggg=' );
		// if there are rewrite_rules
		if (is_array($this->rewrite_tag)) {

			// for each rewrite_tag
			foreach ($this->rewrite_tag AS $rewrite_tag) {
				add_rewrite_tag(
					$rewrite_tag['tag'], 
					$rewrite_tag['rewrite'], 
					$rewrite_tag['query']
				);
			}
		}
	}

	function add_rewrite_rules() {
		
		// if there are rewrite_rules
		if (is_array($this->rewrite_rule)) {

			// for each rewrite_rule
			foreach ($this->rewrite_rule AS $rewrite_rule) {
				add_rewrite_rule(
					$rewrite_rule['rule'],
					$rewrite_rule['rewrite'],
					$rewrite_rule['position']
				);
			}
		}
	}
}
