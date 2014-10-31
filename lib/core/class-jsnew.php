<?php

use Assetic\AssetWriter;
use Assetic\AssetManager;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Filter\JSMinFilter as JSMinFilter;

class CP_Jsnew {
	
	function __construct() {
		add_filter('wp_enqueue_scripts', array($this,'add_js_files'));
	}

	public function add_js_files() {

		if (isset(CP::$config['js_new']) && CP::$config['js_new']) {

			foreach (CP::$config['js_new'] as $key => $js) {
				$this->get_js_file($key, $js);
			}
		}
	}

	public function get_js_file($name, $js) {
		if (isset($js['url']) && $js['url']) {
			$script = $js['url'];
		} else if(isset($js['scripts']) && $js['scripts']) {
			$script = $this->combine_js_files($name, $js['scripts']);
		}

		$this->add_js($name, $script, $js['dependencies'], '', $js['footer']);

	}

	public function combine_js_files($name, $scripts) {
		$update_js_details = 0;
		$js_details = $this->get_js_details($name);
		$js_new_details = array();
		$js_assets = array();

		$all_checksums = '';

		$script_dir = get_template_directory();

		foreach ($scripts as $key => $script) {
			$script_file = $script_dir.'/'.$script;
			if (file_exists($script_file)) {
				$file_checksum = md5_file($script_file);
				if ( ! isset($js_details[$key]) || $js_details[$key] != $file_checksum ) {
					$update_js_details = 1;
				}
				$js_new_details[$key] = $file_checksum;
				$all_checksums.= $file_checksum;
				$js_assets[] = new FileAsset($script_file);
			}
		}

		$new_js_file = $name.'-'.md5($all_checksums).'.js';
		$combined_js = content_url().'/cache/js/'.$new_js_file;

		if ($update_js_details || ! file_exists(WP_CONTENT_DIR.'/cache/js/'.$new_js_file)) {
			$js = new AssetCollection(
				$js_assets,
			array(
			    new JSMinFilter(),
			));
			$js->setTargetPath($new_js_file);

			$am = new AssetManager();
			$am->set('js', $js);

			$writer = new AssetWriter(WP_CONTENT_DIR.'/cache/js');
			$writer->writeManagerAssets($am);

			$this->update_js_details($name, $js_new_details);
		}

		return $combined_js;
	}

	public function add_js($name, $file, $dependencies, $version, $footer) {
		wp_deregister_script($name);
		wp_register_script($name, $file, $dependencies, $version, $footer);
		wp_enqueue_script($name);
	}

	public function add_jsaa() {

		$theme = $ct = wp_get_theme();
		$themeVersion = $theme->get('Version');

		$update_js_details = 0;
		$js_details = $this->get_js_details();
		$js_new_details = array();
		
		if (isset(CP::$config['js']) && CP::$config['js']) {
			
			foreach (CP::$config['js'] as $js) {
				
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
		if ($update_js_details) {
			$this->update_js_details($js_new_details);
		}
	}

	public function get_js_details($name) {
		$js_details = get_option( 'cp_js_'.$name );
		return $js_details;
	}

	public function update_js_details($name, $js_details) {
		update_option( 'cp_js_'.$name, $js_details );
	}
}