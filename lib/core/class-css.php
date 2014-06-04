<?php

class CP_Css {
	
	function __construct() {
		$this->_init();
	}

	private function _init() {
		// add css files
		add_filter('wp_enqueue_scripts', array($this,'add_css'));
	}

	public static function add_css() {
		global $wp_styles;
		
		$theme = $ct = wp_get_theme();
		$themeVersion = $theme->get('Version');

		if (isset(CP::$config['css']) && CP::$config['css']) {
			
			foreach (CP::$config['css'] as $css) {
				
				if ( (is_admin() && $css['admin']) || (!is_admin() && $css['front']) ) {

					if (!isset($css['version']) || !$css['version']) {
						$theme = $ct = wp_get_theme();
						$css['version'] = $themeVersion;
					}

					if (!$css['url'])
						$css['url'] = get_bloginfo('stylesheet_directory');

					wp_register_style($css['name'], $css['url'] . '/' . $css['filename'], $css['dependencies'], $css['version'], $css['media']);
					if ($css['condition'])
						$GLOBALS['wp_styles']->add_data($css['name'], 'conditional', $css['condition']);
					wp_enqueue_style($css['name']);
				}

			}
		}
	}
}