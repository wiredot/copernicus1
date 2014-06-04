<?php

/**
 * Copernicus language class file
 *
 * @package Copernicus
 * @subpackage Copernicus Theme
 * @author Piotr Soluch
 */

/**
 * language class
 *
 * @package Copernicus
 * @subpackage Copernicus Theme
 * @author Piotr Soluch
 */
class CP_Language {

	private $current_language = array();

	/**
	 * Class constructor
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function __construct() {

		// initialize the meta boxes
		$this->_init();
	}

	/**
	 * Initiate the meta boxes
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function _init() {

		if ( isset( $_GET['lang'] ) ) {
			add_action('init', array($this, 'switch_language'));
		}

		add_action('get_pages', array($this, 'pages_translate'));
		
		// get current language
		$current_language = $this->get_current_language();

		$this->define_current_language( $current_language );
	}

	function pages_translate($output) {
		foreach ($output as $key => $value) {
			if (LANGUAGE_SUFFIX != '') {
				$post_meta = get_post_meta($value->ID, 'post_title'.LANGUAGE_SUFFIX, true);
				if ($post_meta) {
					$output[$key]->post_title = $post_meta;
				}
			}
		}
		return $output;
	}

	private function define_current_language( $current_language ) {
		$this->current_language = $current_language;
		define( 'LANGUAGE', $current_language['code'] );
		define( 'LANGUAGE_SUFFIX', $current_language['postmeta_suffix'] );
		setlocale(LC_ALL, $current_language['iso'].'.UTF8', $current_language['iso']);
	}

	public function get_languages( $status = 1 ) {
		if (!isset(CP::$config['language']))
			return null;

		$languages = CP::$config['language'];

		if ( $status != 'all' ) {

			foreach ( $languages as $key => $language ) {
				if ( $language['status'] != $status ) {
					unset( $languages[$key] );
				}
			}
		}

		return $languages;
	}

	public function get_language( $code = '' ) {
		if (isset(CP::$config['language'])) {
			foreach ( CP::$config['language'] as $language ) {
				if ( $code ) {
					if ( $code == $language['code'] ) {
						return $language;
					}
				}
			}
		}

		return null;
	}

	public function get_current_language() {
		if ( isset( $_SESSION['language'] ) ) {
			return $this->get_language( $_SESSION['language'] );
		}

		if ( isset ( $_COOKIE['language'] ) ) {
			return $this->get_language( $_COOKIE['language'] );
		}

		return $this->get_default_language();
	}

	public function get_default_language() {
		if ( $this->current_language )
			return $this->current_language;

		if (!isset(CP::$config['language']))
			return null;

		foreach ( CP::$config['language'] as $language ) {
			if ( $language['default'] ) {
				return $language;
			}
		}

		return null;
	}

	public function get_language_count() {
		return count($this->get_languages());
	}

	public function switch_language() {
		$language = $_GET['lang'];

		$this->change_language($language);
	}

	/**
	 * change language and redirecto to a previous page
	 *
	 * @param string  $language language code
	 * @return none
	 */
	public function change_language( $language ) {
		$this->set_current_language( $language );

		if (isset($_SERVER['HTTP_REFERER'])) {
			$reload = $_SERVER['HTTP_REFERER'];
		}
		else {
			$reload = esc_url ( home_url('/') );
		}

		wp_redirect( $reload );
		exit;
	}

	private function set_current_language( $current_language, $remember = true ) {
		// set session for current language
		$_SESSION['language'] = $current_language;

		if ( $remember ) {
			$expire = 60 * 60 * 24 * 31; // a month

			// set cookie for current language
			setcookie( 'language', $current_language, time() + $expire );
		}
	}
}
