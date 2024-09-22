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
class CP_Spt {

	// part of config with all cpts
	private $spt = array();

	/**
	 * Class constructor
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'support' ) );
	}

	/**
	 *
	 */
	public function support() {
		$this->support_type( 'page', 'title' );
		$this->support_type( 'page', 'editor' );

		$this->support_type( 'post', 'title' );
		$this->support_type( 'post', 'editor' );
	}

	public function support_type( $post_type, $field ) {
		global $CP_Language;

		if ( ! $this->is_supported( $post_type, $field ) ) {
			$this->remove_support( $post_type, $field );
		} else if ( $CP_Language->get_language_count() > 1 && $this->is_to_translate( $post_type, $field ) ) {
			$this->remove_support( $post_type, $field );
		}
	}

	public function remove_support( $post_type, $field ) {
		remove_post_type_support( $post_type, $field );
	}

	public function is_supported( $post_type, $field ) {
		if ( isset( CP::$config['spt'][ $post_type ]['support'][ $field ] ) && ! CP::$config['spt'][ $post_type ]['support'][ $field ] ) {
			return false;
		}

		return true;
	}

	public function is_to_translate( $post_type, $field ) {
		if ( isset( CP::$config['spt'][ $post_type ]['translate'][ $field ] ) && ! CP::$config['spt'][ $post_type ]['translate'][ $field ] ) {
			return false;
		}

		return true;
	}

	// class end
}
