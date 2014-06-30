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
//remove_filter('template_redirect', 'redirect_canonical');

		add_action('init', array($this, 'generate_rewrite_rules'));

		add_filter('home_url', array($this, 'home_url'));

		if (isset(CP::$config['rewrite_tag'])) {
			$this->rewrite_tag = CP::$config['rewrite_tag'];

			add_action('init', array($this, 'add_rewrite_tags'));
		}

		if (isset(CP::$config['rewrite_rule'])) {
			$this->rewrite_rule = CP::$config['rewrite_rule'];

			add_action('init', array($this, 'add_rewrite_rules'));
		}
	}

	public function home_url($url) {
		global $CP_Language;

		$language = $CP_Language->get_current_language();
		
		if (isset($language['prefix']) && $language['prefix']) {
			$wpurl = get_bloginfo( 'wpurl' );
			$url = preg_replace('/'.str_replace('/', '\/', $wpurl).'/', $wpurl.'/'.$language['prefix'], $url);
		}
		//new dBug($url);

		return $url;
	}

	function generate_rewrite_rules() {
		global $CP_Language, $wpdb, $wp_query, $wp_rewrite;

		$rules = $wp_rewrite->wp_rewrite_rules();
		if (is_admin()) {
			//new dBug($rules);
		}

		//$wp_rewrite->flush_rules( );

		$pages = $wpdb->get_results("
			SELECT ID FROM ".$wpdb->posts." WHERE post_status = 'publish'
		", ARRAY_A);

		$languages = $CP_Language->get_languages();
		
		$wpurl = get_bloginfo( 'wpurl' );

		add_rewrite_tag('%langid%','(.*)', 'langid=');

		foreach ($pages as $key => $value) {
			$url = str_replace($wpurl , '', get_permalink( $value['ID'] ));
			if(substr($url, -1) == '/') {
			    $url = substr($url, 0, -1);
			}

			if (is_array($languages)) {
				foreach ($languages as $language) {
					if (isset($language['prefix']) && $language['prefix']) {
						$post_type = get_post_type( $value['ID'] );
						if ($post_type == 'page') {
							add_rewrite_rule('^'.$language['prefix'].$url.'/?$','index.php?page_id='.$value['ID'].'&langid='.$language['prefix'],'top');
						} else {
							add_rewrite_rule('^'.$language['prefix'].$url.'/?$','index.php?p='.$value['ID'].'&post_type='.$post_type.'&langid='.$language['prefix'],'top');
						}
					}
				}
			}
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
