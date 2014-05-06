<?php

class CP_Smarty {

	public $smarty;
	
	function __construct() {
		$this->_init();
	}

	private function _init() {
		// load smarty
		CP::load_library(CP_PATH.'/lib/smarty/Smarty.class.php');
		
		$template_dirs[] = get_template_directory() . '/templates/';
		$plugins_dirs[] = get_template_directory() . '/lib/smarty-plugins/';
		$template_dirs[] = CP_PATH . '/templates/';
		$plugins_dirs[] = CP_PATH.'/lib/smarty-plugins/';
		
		if (isset(CP::$config['plugin'])) {
			foreach (CP::$config['plugin'] as $plugin) {
				if (isset($plugin['directory'])) {
					$template_dirs[] = $plugin['directory'] . '/templates/';
					$plugins_dirs[] = $plugin['directory'] . '/lib/smarty-plugins/';
				}
			}
		}

		$this->smarty = new Smarty();
		$this->smarty->addPluginsDir($plugins_dirs);
		$this->smarty->setTemplateDir($template_dirs);
		$this->smarty->setCompileDir(WP_CONTENT_DIR . '/smarty/templates_c/');
		$this->smarty->setCacheDir(WP_CONTENT_DIR . '/smarty/cache/');
		$this->smarty->registerFilter('pre', array($this, 'block_looop_literal'));
		if (WP_DEBUG) {
			$this->smarty->force_compile = true;
		}
	}

	function block_looop_literal($tpl_source, $template) {
    	$tpl_source = preg_replace("/({looop .*})/", '$1{literal}', $tpl_source);
    	$tpl_source = preg_replace("/({\/looop})/", '{/literal}$1', $tpl_source);

    	return $tpl_source;
	}
}