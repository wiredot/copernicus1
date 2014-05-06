<?php

/**
 * Copernicus Theme Framework cleanup class file
 *
 * @package Copernicus
 * @subpackage Copernicus Theme Framework
 * @author Piotr Soluch
 */

/**
 * Cleanup class
 *
 * @package Copernicus
 * @subpackage Copernicus Theme Framework
 * @author Piotr Soluch
 */
class CP_Cleanup {

	var $cleanup = array();

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

		if (isset (CP::$config['cleanup'])) {
			$this->cleanup = CP::$config['cleanup'];

			$this->admin_bar();
			
			add_filter('init', array($this,'clean_up'));
			add_filter('the_content_more_link', array($this,'remove_more_jump_link'));
			add_filter('widgets_init', array($this,'unregister_widgets'));
			//add_filter('nav_menu_css_class', array($this,'special_nav_class'), 10, 2);
			//add_filter('nav_menu_item_id', array($this,'special_nav_id'), 10, 2);
			//add_filter('wp_get_nav_menu_items', array($this,'nav_menu_items'), 10, 2);
		}
	}
	
	function nav_menu_items($items) {
		if (is_array($items)) {
			
			foreach ($items as $key => $value) {

				if ($value->object_id == 134) {

					if (empty($_SESSION['story'])) {
						global $MS_C_User;
						$story = $MS_C_User->get_user_story('publish');
						$items[$key]->url = $story['url'];
						$items[$key]->object_id = $story['id'];
					} else {
						$story = $_SESSION['story'];
						//grab story from $_SESSION rather than database, so as to not change post
						$items[$key]->url = $_SESSION['story']['url'];
						$items[$key]->object_id = $_SESSION['story']['id'];
					}

					if (strpos($story['url'], $_SERVER['REQUEST_URI'])) {
						$items[$key]->classes[] = 'active';
					}
				}
			}
		}
		return $items;
	}

	function special_nav_class($classes, $item) {
		if (is_array($classes)) {
			foreach ($classes as $key => $value) {
				if ($value == 'current_page_item') $classes[$key] = 'active';
				else if (preg_match('/item/', $value)) {
					unset ($classes[$key]);
				}
			}
		}
		
		return $classes;
	}
	
	function special_nav_id($id, $item) {
		return '';
	}

	

	/**
	 * Remove some template elements
	 *
	 * @access type private
	 * @return type null no return
	 * @author Piotr Soluch
	 */
	public function clean_up() {
		if (!$this->cleanup['meta']['generator'])
			remove_action('wp_head', 'wp_generator');
		if (!$this->cleanup['meta']['rsd'])
			remove_action('wp_head', 'rsd_link');
		if (!$this->cleanup['meta']['wlwmanifest'])
			remove_action('wp_head', 'wlwmanifest_link');
		if (!$this->cleanup['meta']['index_rel'])
			remove_action('wp_head', 'index_rel_link');
		if (!$this->cleanup['meta']['feed_links_extra'])
			remove_action('wp_head', 'feed_links_extra', 3);
		if (!$this->cleanup['meta']['feed_links'])
			remove_action('wp_head', 'feed_links', 2);
		if (!$this->cleanup['meta']['parent_post_rel'])
			remove_action('wp_head', 'parent_post_rel_link', 10, 0);
		if (!$this->cleanup['meta']['start_post_rel'])
			remove_action('wp_head', 'start_post_rel_link', 10, 0);
		if (!$this->cleanup['meta']['adjacent_posts_rel'])
			remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);

		if (!$this->cleanup['js']['l10n'])
			wp_deregister_script('l10n');
	}
	
	private function admin_bar() {

		if (!$this->cleanup['admin']['bar'])
			add_filter('show_admin_bar', '__return_false');
	}
	
	function remove_more_jump_link($link) { 
		$offset = strpos($link, '#more-');
		if ($offset) {
			$end = strpos($link, '"',$offset);
		}
		if ($end) {
			$link = substr_replace($link, '', $offset, $end-$offset);
		}
		return $link;
	}

	function unregister_widgets() {
		if (isset($this->cleanup['widget'])) {
			foreach ($this->cleanup['widget'] as $widget => $status) {
				if (!$status) {
					unregister_widget($widget);
				}
			}
		}
	}
}
