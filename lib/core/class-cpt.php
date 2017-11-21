<?php

/**
 * Copernicus Custom Post Type class file
 *
 * @package Copernicus
 * @subpackage Copernicus Theme
 * @author Piotr Soluch
 */

/**
 * Custom Post Type class
 *
 * @package Copernicus
 * @subpackage Copernicus Theme
 * @author Piotr Soluch
 */
class CP_Cpt {

	// part of config with all cpts
	private $cpt = array();

	/**
	 * Class constructor
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function __construct() {

		if ( isset( CP::$config['cpt'] ) && is_array( CP::$config['cpt'] ) ) {
			$this->cpt = CP::$config['cpt'];

			// create custom post type
			add_action( 'init', array( $this, 'create_post_types' ) );
			add_action( 'admin_head', array( $this, 'add_menu_icons_styles' ) );
		}
	}

	/**
	 * Take the cpts from config and create cpt
	 *
	 * @access type public
	 * @return type null doesn't return a value
	 * @author Piotr Soluch
	 */
	public function create_post_types() {
		// for each cpt
		foreach ( $this->cpt as $key => $cpt ) {

			$cpt['name'] = $key;

			// if cpt is active
			if ( isset( $cpt['settings']['active'] ) && $cpt['settings']['active'] ) {
				// create cpt
				$this->create_post_type( $cpt );
			}
		}
	}

	/**
	 * Create a custom post type
	 *
	 * @access type public
	 * @return type null doesn't return a value
	 * @author Piotr Soluch
	 */
	private function create_post_type( $cpt ) {
		global $CP_Language, $CP_Mb;
		// create an array for supported elements
		$supports = array();

		// if more than 1 active language
		if ( $CP_Language->get_language_count() > 1 ) {

			// if cpt supports title, remove standard title (a special one will be turned on)
			if ( isset( $cpt['support']['title'] ) && $cpt['support']['title'] && $this->is_to_translate( $cpt['name'], 'title' ) ) {
				$cpt['support']['title'] = false;
			}

			// if cpt supports editor, remove standard editor (a special one will be turned on)
			if ( $cpt['support']['editor'] && $this->is_to_translate( $cpt['name'], 'editor' ) ) {
				$cpt['support']['editor'] = false;
			}
		}

		// create a list of supported fields
		foreach ( $cpt['support'] as $key => $value ) {

			if ( $value ) {
				$supports[] = $key;
			}
		}

		// merge default and custom settings
		$settings = $cpt['settings'];
		$settings['supports'] = $supports;
		$settings['labels'] = $cpt['labels'];

		if ( ! count( $settings['supports'] ) ) {
			unset( $settings['supports'] );
			$settings['supports'] = false;
		}

		// register cpt
		register_post_type(
			$cpt['settings']['name'], $settings
		);
	}

	/**
	 *
	 * @param type $post_type
	 * @return int
	 */
	public function get_parent_page( $post_type ) {

		foreach ( $this->cpt as $cpt ) {
			if ( $cpt['settings']['name'] == $post_type ) {
				if ( isset( $cpt['settings']['parent_page'] ) ) {
					return $cpt['settings']['parent_page'];
				}
			}
		}

		return 0;
	}

	/**
	 *
	 */
	public function get_post_types() {
		$post_types = array();

		foreach ( $this->cpt as $cpt ) {
			if ( $cpt['settings']['active'] ) {
				$post_types[] = $cpt['settings']['name'];
			}
		}

		return $post_types;
	}

	/**
	 *
	 */
	public function add_menu_icons_styles() {
		echo '<style type="text/css" media="all">';
		if ( is_array( $this->cpt ) ) {

			// for each cpt
			foreach ( $this->cpt as $cpt ) {

				// if cpt is active
				if ( isset( $cpt['settings']['menu_icon_id'] ) && $cpt['settings']['menu_icon_id'] ) {
					echo '#adminmenu .menu-icon-' . $cpt['settings']['name'] . " div.wp-menu-image:before {content: '\\f" . $cpt['settings']['menu_icon_id'] . "';}";
				}
			}
		}
		echo '</style>';
	}

	/**
	 *
	 */
	public function is_supported( $post_type, $feature ) {

		foreach ( CP::$config['cpt'] as $key => $cpt ) {

			if ( $cpt['settings']['name'] == $post_type ) {
				if ( isset( $cpt['support'][ $feature ] ) && $cpt['support'][ $feature ] ) {
					return true;
				} else {
					return false;
				}
			}
		}

		return false;
	}

	public function is_to_translate( $post_type, $field ) {
		if ( isset( CP::$config['cpt'][ $post_type ]['translate'][ $field ] ) ) {
			if ( CP::$config['cpt'][ $post_type ]['translate'][ $field ] ) {
				return 1;
			} else {
				return 0;
			}
		}

		return 1;
	}
}
