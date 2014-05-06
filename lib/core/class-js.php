<?php

class CP_Js {
	
	function __construct() {
		$this->_init();
	}

	private function _init() {
		// add js files
		add_filter('wp_enqueue_scripts', array($this,'add_js'));
	}

	public static function add_js() {

		$theme = $ct = wp_get_theme();
		$themeVersion = $theme->get('Version');
		
		if (isset(CP::$config['js']) && CP::$config['js']) {
			
			foreach (CP::$config['js'] as $js) {

				if (!isset($js['version']) || !$js['version']) {
					$theme = $ct = wp_get_theme();
					$js['version'] = $themeVersion;
				}
				
				if ( (is_admin() && $js['admin']) || (!is_admin() && $js['front']) ) {
					
					if (!$js['url']) {
						$js['url'] = get_bloginfo ('stylesheet_directory');
					}

					if (!isset($js['dependencies'])) {
						$js['dependencies'] = '';
					} 
						
					wp_deregister_script($js['name']);
					wp_register_script($js['name'], $js['url'] . '/' . $js['filename'], $js['dependencies'], $js['version'], $js['footer']);
					wp_enqueue_script($js['name']);
				}
			}
		}
	}
}