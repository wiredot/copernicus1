<?php

class CP_Theme {
	
	function __construct() {
		$this->_init();
	}

	private function _init() {
		$this->theme_support();
	}

	private static function theme_support() {
		
		// add theme support for custom menus
		if ( isset(CP::$config['theme_support']['menu']) && CP::$config['theme_support']['menu'] ) {
			add_theme_support('menus');
		}

		// add theme support for post thumbnail
		if ( isset(CP::$config['theme_support']['post_thumbnail']) && CP::$config['theme_support']['post_thumbnail']) {
			add_theme_support('post-thumbnails');
		}
		
		// add theme support for automatic feed links
		if ( isset(CP::$config['theme_support']['automatic_feed_links']) && CP::$config['theme_support']['automatic_feed_links']) {
			add_theme_support('automatic-feed-links');
		}
	}
}