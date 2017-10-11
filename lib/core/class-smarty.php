<?php

class CP_Smarty {

	public $smarty;
	
	/**
	 * 
	 */
	public function __construct() {
		global $ccc;
		
		$template_dirs[] = get_stylesheet_directory() . '/templates/';
		$plugins_dirs[] = get_stylesheet_directory() . '/lib/smarty-plugins/';
		
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
		$this->smarty->registerFilter('pre', array($this, 'block_loop_literal'));
		$this->smarty->registerDefaultPluginHandler(array($this, 'default_plugin_handler'));
		$this->smarty->force_compile = true;
	}

	/**
	 * 
	 */
	public function block_loop_literal($tpl_source, $template) {
    	$tpl_source = preg_replace("/({loop .*})/", '$1{literal}', $tpl_source);
    	$tpl_source = preg_replace("/({loop})/", '$1{literal}', $tpl_source);
    	$tpl_source = preg_replace("/({\/loop})/", '{/literal}$1', $tpl_source);
    	return $tpl_source;
	}

	/**
	 * 
	 */
	public function default_plugin_handler($name, $type, $template, &$callback, &$script, &$cacheable) {
		switch ($type) {
			case Smarty::PLUGIN_FUNCTION:
			
				if (function_exists($name)) {
					
					$cachable = false;

					if ( ! class_exists('cp_autoload_'.$name)) {

						eval("class cp_autoload_".$name."{ static function function_autoloader(\$show){ return autoload_plugins(\$show, __CLASS__); } };");
					}

					$callback = array('cp_autoload_'.$name, 'function_autoloader');
					return true;
				}
			return false;
		}
		return false;
	}

	public function fetch($template) {
		global $CP_Template;

		$template = $CP_Template->templateExists($template);
		return $this->smarty->fetch($template);
	}

	public function display($template) {
		global $CP_Template;

		$template = $CP_Template->templateExists($template);
		echo $template;
		$this->smarty->display($template);
	}

// class end
}

function autoload_plugins($show, $class_name) {
	$function = str_replace('cp_autoload_', '', $class_name);
	if (function_exists($function)) {
		return call_user_func_array($function, $show);
	}
	return null;
}