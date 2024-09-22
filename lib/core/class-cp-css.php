<?php

use Assetic\AssetWriter;
use Assetic\AssetManager;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Filter\CssMinFilter as CssMinFilter;

class CP_Css {

	/**
	 *
	 */
	public function __construct() {
		add_filter( 'wp_enqueue_scripts', array( $this, 'add_css_files' ) );
		// add_filter('init', array($this,'add_rewrite_rules'));
	}

	public function add_rewrite_rules() {
		add_rewrite_tag( '%cp_show_css%', '(.+)' );
		add_rewrite_rule( 'content/themes/' . get_template() . '/assets/css/style-([^/.]+).css', 'index.php?cp_show_css=$matches[1]', 'top' );
	}

	public function show_file( $id ) {
		header( 'Content-Type: text/css' );
		$css_file = WP_CONTENT_DIR . '/cache/css/style-' . $id . '.css';
		if ( file_exists( $css_file ) ) {
			echo file_get_contents( $css_file );
		}
		exit;
	}

	/**
	 *
	 */
	public function add_css_files() {
		global $wp_query;
		if ( isset( $wp_query->query_vars['cp_show_css'] ) ) {
			$this->show_file( $wp_query->query_vars['cp_show_css'] );
		}

		if ( isset( CP::$config['css'] ) && CP::$config['css'] ) {

			foreach ( CP::$config['css'] as $key => $css ) {
				$this->get_css_file( $key, $css );
			}
		}
	}

	/**
	 *
	 */
	public function get_css_file( $name, $css ) {
		if ( ! isset( $css['plugin'] ) ) {
			$css['plugin'] = null;
		}

		if ( isset( $css['url'] ) && $css['url'] ) {
			$this->add_css( $name, $css['url'], $css['dependencies'], '', $css['media'] );
		} else if ( isset( $css['links'] ) && $css['links'] ) {

			if ( defined( 'CP_DEV' ) && CP_DEV ||  defined( 'CP_DEBUG' ) && CP_DEBUG  ) {
				foreach ( $css['links'] as $css_name => $css_link ) {
					$this->add_css( $css_name, get_template_directory_uri() . '/' . $css_link, $css['dependencies'], '', $css['media'] );
				}
			} else {
				$link = $this->combine_css_files( $name, $css['links'], $css['plugin'] );
				$this->add_css( $name, $link, $css['dependencies'], '', $css['media'] );
			}
		}
	}

	/**
	 *
	 */
	public function combine_css_files( $name, $scripts, $plugin = null ) {
		$update_css_details = 0;
		$css_details = $this->get_css_details( $name );
		$css_assets = array();

		$all_checksums = '';

		$script_dir = get_template_directory();

		if ( $plugin ) {
			$script_dir = $plugin;
		}

		foreach ( $scripts as $key => $script ) {
			$script_file = $script_dir . '/' . $script;
			if ( file_exists( $script_file ) ) {
				$file_checksum = md5_file( $script_file );

				if ( ! isset( $css_details[ $key ] ) || $css_details[ $key ] != $file_checksum ) {
					$update_css_details = 1;
				}

				$css_details[ $key ] = $file_checksum;
				$all_checksums .= $file_checksum;
				$css_assets[] = new FileAsset( $script_file );
			}
		}

		$new_css_file = $name . '-' . md5( $all_checksums ) . '.css';
		$combined_css = content_url() . '/cache/css/' . $new_css_file;
		;

		if ( $update_css_details || ! file_exists( WP_CONTENT_DIR . '/cache/css/' . $new_css_file ) ) {
			$css = new AssetCollection(
				$css_assets,
				array(
					new CssMinFilter(),
				)
			);

			$css->setTargetPath( $new_css_file );

			$am = new AssetManager();
			$am->set( 'css', $css );

			$writer = new AssetWriter( WP_CONTENT_DIR . '/cache/css' );
			$writer->writeManagerAssets( $am );

			$this->update_css_details( $name, $css_details );
		}

		return $combined_css;
	}

	/**
	 *
	 */
	public function add_css( $name, $file, $dependencies, $version, $media ) {
		wp_register_style( $name, $file, $dependencies, $version, $media );
		wp_enqueue_style( $name );
	}

	/**
	 *
	 */
	public function get_css_details( $name ) {
		$css_details = get_option( 'cp_css_' . $name );
		return $css_details;
	}

	/**
	 *
	 */
	public function update_css_details( $name, $css_details ) {
		update_option( 'cp_css_' . $name, $css_details );
	}

	// class end
}
