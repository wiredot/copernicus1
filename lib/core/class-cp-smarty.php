<?php
use Smarty\Smarty;

class CP_Smarty {
	public $smarty;

	/**
	 *
	 */
	public function __construct() {
		global $ccc;

		$template_dirs[] = get_stylesheet_directory() . '/cp-templates/';
		$plugins_dirs[]  = get_stylesheet_directory() . '/lib/smarty-plugins/';
		
		$template_dirs[] = CP_PATH . '/cp-templates/';
		$plugins_dirs[]  = CP_PATH . '/lib/smarty-plugins/';
		$classes_dirs    = CP_PATH . '/lib/smarty-classes/';

		if ( isset( CP::$config['plugin'] ) ) {
			foreach ( CP::$config['plugin'] as $plugin ) {
				if ( isset( $plugin['directory'] ) ) {
					$template_dirs[] = $plugin['directory'] . '/cp-templates/';
					$plugins_dirs[]  = $plugin['directory'] . '/lib/smarty-plugins/';
				}
			}
		}

		$this->smarty = new Smarty();
		$this->smarty->registerPlugin( 'modifier', 'floatval', 'floatval' );
		$this->smarty->registerPlugin( 'modifier', 'intval', 'intval' );
		$this->plugin_autoloader( $plugins_dirs );
		$this->smarty->setTemplateDir( $template_dirs );
		$this->smarty->setCompileDir( WP_CONTENT_DIR . '/smarty/templates_c/' );
		$this->smarty->setCacheDir( WP_CONTENT_DIR . '/smarty/cache/' );
		$this->smarty->registerFilter( 'pre', array( $this, 'block_loop_literal' ) );
		$this->wildcard_extension( $classes_dirs );
		$this->smarty->force_compile = true;
	}

	public function plugin_autoloader( $directories ) {
		if ( is_array( $directories ) ) {
			foreach ( $directories as $directory ) {
				$this->plugin_autoload_directory( $directory );
			}
		} else {
			$this->plugin_autoload_directory( $directories );
		}
	}

	private function plugin_autoload_directory( $directory ) {
		if ( ! file_exists( $directory ) ) {
			return;
		}
		$handle = opendir( $directory );
		while ( false !== ( $entry = readdir( $handle ) ) ) {
			if ( preg_match( '/^(block|function|modifier|tag).(.*)\.php/', $entry, $matches ) ) {
				// print_r( $matches );
				$this->load_smarty_plugin( $matches[2], $matches[1], $directory . $matches[0] );
			}
		}
	}

	private function load_smarty_plugin( $name, $type, $file ) {
		include_once $file;
		$class_name = 'smarty_' . $type . '_' . $name;
		switch ( $type ) {
			case 'function':
				$this->smarty->registerPlugin( Smarty::PLUGIN_FUNCTION, $name, $class_name );
				break;
			case 'modifier':
				$this->smarty->registerPlugin( Smarty::PLUGIN_MODIFIER, $name, $class_name );
				break;
			case 'block':
				$this->smarty->registerPlugin( Smarty::PLUGIN_BLOCK, $name, $class_name );
				break;
			default:
				return;
				break;
		}
	}

	private function wildcard_extension( $directory ) {
		$file = $directory.'class-smarty.wildcardextension.php';
		if ( ! file_exists( $file ) ) {
			return;
		}
		include_once $file;
		$this->smarty->addExtension( new WildcardExtension() );
	}

	/**
	 *
	 */
	public function block_loop_literal( $tpl_source, $template ) {
		$tpl_source = preg_replace( '/({loop .*})/', '$1{literal}', $tpl_source );
		$tpl_source = preg_replace( '/({loop})/', '$1{literal}', $tpl_source );
		$tpl_source = preg_replace( '/({\/loop})/', '{/literal}$1', $tpl_source );
		return $tpl_source;
	}

	public function fetch( $template ) {
		global $CP_Template;

		$template = $CP_Template->templateExists( $template );
		return $this->smarty->fetch( $template );
	}

	public function display( $template ) {
		global $CP_Template;

		$template = $CP_Template->templateExists( $template );
		echo $template;
		$this->smarty->display( $template );
	}

	// class end
}

function autoload_plugins( $show, $class_name ) {
	$function = str_replace( 'cp_autoload_', '', $class_name );
	if ( function_exists( $function ) ) {
		return call_user_func_array( $function, $show );
	}
	return null;
}
