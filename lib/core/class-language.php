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

	/**
	 *
	 */
	private $current_language = array();

	/**
	 * Class constructor
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'define_current_language' ) );
		add_action( 'get_pages', array( $this, 'pages_translate' ) );
	}

	/**
	 *
	 */
	public function get_current_language() {
		$user_id = get_current_user_id();
		$user_language = get_user_meta( $user_id, 'language', true );
		if ( $user_language ) {
			$language = $this->get_language( $user_language );
			if ( $language ) {
				return $language;
			}
		}
		$uri = $_SERVER['REQUEST_URI'];
		if ( preg_match( '/^\/[a-z]{2}\//', $uri, $matches ) ) {
			$language = $this->get_language( str_replace( '/', '', substr( $uri, 0, 4 ) ) );
			if ( $language ) {
				return $language;
			}
		}

		return $this->get_default_language();
	}

	/**
	 *
	 */
	public function define_current_language() {
		$current_language = $this->get_current_language();
		$this->current_language = $current_language;
		define( 'LANGUAGE', $current_language['code'] );
		define( 'LANGUAGE_SUFFIX', $current_language['postmeta_suffix'] );
		setlocale( LC_COLLATE, $current_language['iso'] . '.UTF8', $current_language['iso'] );
		setlocale( LC_TIME, $current_language['iso'] . '.UTF8', $current_language['iso'] );
		setlocale( LC_MESSAGES, $current_language['iso'] . '.UTF8', $current_language['iso'] );
	}

	/**
	 *
	 */
	public function get_default_language() {
		if ( $this->current_language ) {
			return $this->current_language;
		}

		if ( ! isset( CP::$config['language'] ) ) {
			return null;
		}

		foreach ( CP::$config['language'] as $language ) {
			if ( $language['default'] ) {
				return $language;
			}
		}

		return null;
	}

	/**
	 *
	 */
	public function pages_translate( $output ) {
		foreach ( $output as $key => $value ) {
			if ( LANGUAGE_SUFFIX != '' ) {
				$post_meta = get_post_meta( $value->ID, 'post_title' . LANGUAGE_SUFFIX, true );
				if ( $post_meta ) {
					$output[ $key ]->post_title = $post_meta;
				}
			}
		}
		return $output;
	}

	/**
	 *
	 */
	public function get_languages( $status = 1 ) {
		if ( ! isset( CP::$config['language'] ) ) {
			return null;
		}

		$languages = CP::$config['language'];

		if ( $status != 'all' ) {

			foreach ( $languages as $key => $language ) {
				if ( $language['status'] != $status ) {
					unset( $languages[ $key ] );
				}
			}
		}

		return $languages;
	}

	/**
	 *
	 */
	public function get_language( $code = '' ) {
		if ( isset( CP::$config['language'] ) ) {
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

	/**
	 *
	 */
	public function get_language_count() {
		return count( $this->get_languages() );
	}

	// class end
}
