<?php

/**
 * Copernicus Theme Framework cleanup class file
 *
 * @package Copernicus
 * @subpackage Copernicus Theme Framework
 * @author Piotr Soluch
 */

/**
 * Cleanup class
 *
 * @package Copernicus
 * @subpackage Copernicus Theme Framework
 * @author Piotr Soluch
 */
class CP_Cleanup {

	var $cleanup = array();

	/**
	 * Class constructor
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function __construct() {

		if ( isset( CP::$config['cleanup'] ) ) {
			$this->cleanup = CP::$config['cleanup'];

			$this->admin_bar();

			add_filter( 'init', array( $this, 'clean_up' ) );

			add_filter( 'widgets_init', array( $this, 'unregister_widgets' ) );
		}
	}

	/**
	 *
	 */
	private function admin_bar() {
		if ( isset( $this->cleanup['admin']['bar'] ) && ! $this->cleanup['admin']['bar'] ) {
			add_filter( 'show_admin_bar', '__return_false' );
		}
	}

	/**
	 * Remove some template elements
	 *
	 * @access type private
	 * @return type null no return
	 * @author Piotr Soluch
	 */
	public function clean_up() {
		if ( ! $this->cleanup['meta']['generator'] ) {
			remove_action( 'wp_head', 'wp_generator' );
		}

		if ( ! $this->cleanup['meta']['rsd'] ) {
			remove_action( 'wp_head', 'rsd_link' );
		}

		if ( ! $this->cleanup['meta']['wlwmanifest'] ) {
			remove_action( 'wp_head', 'wlwmanifest_link' );
		}

		if ( ! $this->cleanup['meta']['index_rel'] ) {
			remove_action( 'wp_head', 'index_rel_link' );
		}

		if ( ! $this->cleanup['meta']['feed_links_extra'] ) {
			remove_action( 'wp_head', 'feed_links_extra', 3 );
		}

		if ( ! $this->cleanup['meta']['feed_links'] ) {
			remove_action( 'wp_head', 'feed_links', 2 );
		}

		if ( ! $this->cleanup['meta']['parent_post_rel'] ) {
			remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
		}

		if ( ! $this->cleanup['meta']['start_post_rel'] ) {
			remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
		}

		if ( ! $this->cleanup['meta']['adjacent_posts_rel'] ) {
			remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 );
		}

		if ( ! $this->cleanup['js']['l10n'] ) {
			wp_deregister_script( 'l10n' );
		}
	}

	/**
	 *
	 */
	public function unregister_widgets() {
		if ( isset( $this->cleanup['widget'] ) ) {
			foreach ( $this->cleanup['widget'] as $widget => $status ) {
				if ( ! $status ) {
					unregister_widget( $widget );
				}
			}
		}
	}

	// class end
}
