<?php

use Assetic\AssetWriter;
use Assetic\AssetManager;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Filter\CssMinFilter as CssMinFilter;

class CP_Cssnew {
	
	function __construct() {
		add_filter('wp_enqueue_scripts', array($this,'add_css_files'));
	}

	public function add_css_files() {

		if (isset(CP::$config['css_new']) && CP::$config['css_new']) {

			foreach (CP::$config['css_new'] as $key => $css) {
				$this->get_css_file($key, $css);
			}
		}
	}

	public function get_css_file($name, $css) {
		if (isset($css['url']) && $css['url']) {
			$link = $css['url'];
		} else if(isset($css['links']) && $css['links']) {
			$link = $this->combine_css_files($name, $css['links']);
		}

		$this->add_css($name, $link, $css['dependencies'], '', $css['media']);
	}

	public function combine_css_files($name, $scripts) {
		$update_css_details = 0;
		$css_details = $this->get_css_details($name);
		$css_new_details = array();
		$css_assets = array();

		$all_checksums = '';

		$script_dir = get_template_directory();

		foreach ($scripts as $key => $script) {
			$script_file = $script_dir.'/'.$script;
			if (file_exists($script_file)) {
				$file_checksum = md5_file($script_file);
				if ( ! isset($css_details[$key]) || $css_details[$key] != $file_checksum ) {
					$update_css_details = 1;
				}
				$css_new_details[$key] = $file_checksum;
				$all_checksums.= $file_checksum;
				$css_assets[] = new FileAsset($script_file);
			}
		}

		$new_css_file = $name.'-'.md5($all_checksums).'.css';
		$combined_css = content_url().'/cache/css/'.$new_css_file;

		if ($update_css_details || ! file_exists(WP_CONTENT_DIR.'/cache/css/'.$new_css_file)) {
			$css = new AssetCollection(
				$css_assets,
			array(
			    new CssMinFilter(),
			));
			$css->setTargetPath($new_css_file);

			$am = new AssetManager();
			$am->set('css', $css);

			$writer = new AssetWriter(WP_CONTENT_DIR.'/cache/css');
			$writer->writeManagerAssets($am);

			$this->update_css_details($name, $css_new_details);
		}

		return $combined_css;
	}

	public function add_css($name, $file, $dependencies, $version, $media) {
		wp_register_style($name, $file, $dependencies, $version, $media);
		wp_enqueue_style($name);
	}

	public function get_css_details($name) {
		$css_details = get_option( 'cp_css_'.$name );
		return $css_details;
	}

	public function update_css_details($name, $css_details) {
		update_option( 'cp_css_'.$name, $css_details );
	}
}