<?php

use Assetic\AssetWriter;
use Assetic\AssetManager;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Filter\JSMinFilter as JSMinFilter;

class CP_Js {
	
	/**
	 * 
	 */
	public function __construct() {
		add_filter('wp_enqueue_scripts', array($this,'add_js_files'));
		add_filter('admin_enqueue_scripts', array($this,'add_js_files_in_admin'));
		// add_filter('init', array($this,'add_rewrite_rules'));
	}

	public function show_file($id) {
		header('Content-Type: text/js');
		$js_file = WP_CONTENT_DIR.'/cache/js/scripts-'.$id.'.js';
		if (file_exists($js_file)) {
			echo file_get_contents($js_file);
		}
		exit;
	}

	public function add_rewrite_rules() {
		add_rewrite_tag( '%cp_show_js%', '(.+)' );
		add_rewrite_rule( 'content/themes/'.get_template().'/assets/js/scripts-([^/.]+).js', 'index.php?cp_show_js=$matches[1]', 'top' );
	}

	public function add_js_files_in_admin() {
		$this->add_js_files('admin');
	}

	/**
	 * 
	 */
	public function add_js_files($mode = 'front') {
		global $wp_query;
		if ($mode != 'admin') {
			$mode = 'front';
		}

		if (isset($wp_query->query_vars['cp_show_js'])) {
			$this->show_file($wp_query->query_vars['cp_show_js']);
		}

		if (isset(CP::$config['js']) && CP::$config['js']) {

			foreach (CP::$config['js'] as $key => $js) {
				if (($mode == 'front' && $js['front']) || ($mode == 'admin' && $js['admin'])) {
					$this->get_js_file($key, $js);
				}
			}
		}
	}

	/**
	 * 
	 */
	public function get_js_file($name, $js) {
		if ( ! isset($js['plugin'])) {
			$js['plugin'] = null;
		}
			
		if (isset($js['url']) && $js['url']) {
			$this->add_js($name, $js['url'], $js['dependencies'], '', $js['footer']);
		} else if(isset($js['scripts']) && $js['scripts']) {
			
			if ( defined('CP_DEV') && CP_DEV) {
				foreach ($js['scripts'] as $js_name => $js_link) {
					$this->add_js($js_name, get_template_directory_uri().'/'.$js_link, $js['dependencies'], '', $js['footer']);
				}
			} else {
				$script = $this->combine_js_files($name, $js['scripts'], $js['plugin']);
				$this->add_js($name, $script, $js['dependencies'], '', $js['footer']);
			}
		}
	}

	/**
	 * 
	 */
	public function combine_js_files($name, $scripts, $plugin = '') {
		$update_js_details = 0;
		$js_details = $this->get_js_details($name);
		$js_assets = array();

		$all_checksums = '';

		$script_dir = get_template_directory();

		if ($plugin) {
			$script_dir = $plugin;
		}

		foreach ($scripts as $key => $script) {
			$script_file = $script_dir.'/'.$script;

			if (file_exists($script_file)) {
				$file_checksum = md5_file($script_file);
				if ( ! isset($js_details[$key]) || $js_details[$key] != $file_checksum ) {
					$update_js_details = 1;
				}
				$js_details[$key] = $file_checksum;
				$all_checksums.= $file_checksum;
				$js_assets[] = new FileAsset($script_file);
			}
		}

		$new_js_file = $name.'-'.md5($all_checksums).'.js';
		$combined_js = content_url().'/cache/js/'.$new_js_file;

		if ($update_js_details || ! file_exists(WP_CONTENT_DIR.'/cache/js/'.$new_js_file)) {
			if (WP_DEBUG) {
				$js = new AssetCollection(
					$js_assets
				);
			} else {
				$js = new AssetCollection(
					$js_assets,
				array(
				    new JSMinFilter(),
				));
			}
			
			$js->setTargetPath($new_js_file);

			$am = new AssetManager();
			$am->set('js', $js);

			$writer = new AssetWriter(WP_CONTENT_DIR.'/cache/js');
			$writer->writeManagerAssets($am);

			$this->update_js_details($name, $js_details);
		}

		return $combined_js;
	}

	/**
	 * 
	 */
	public function add_js($name, $file, $dependencies, $version, $footer) {
		wp_deregister_script($name);
		wp_register_script($name, $file, $dependencies, $version, $footer);
		wp_enqueue_script($name);
	}

	/**
	 * 
	 */
	public function get_js_details($name) {
		$js_details = get_option( 'cp_js_'.$name );
		return $js_details;
	}

	/**
	 * 
	 */
	public function update_js_details($name, $js_details) {
		update_option( 'cp_js_'.$name, $js_details );
	}

// class end
}