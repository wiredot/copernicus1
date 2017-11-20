<?php

/**
 * Copernicus shortcodes class file
 *
 * @package Copernicus
 * @subpackage Copernicus Theme
 * @author Piotr Soluch
 */

/**
 * shortcodes class
 *
 * @package Copernicus
 * @subpackage Copernicus Theme
 * @author Piotr Soluch
 */
class CP_Sc {

	/**
	 * Class constructor
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function __construct() {
		add_shortcode( 'loop', array( $this, 'sh_loop' ) );
	}

	/**
	 *
	 */
	public function sh_loop( $atts, $content = null ) {
		global $CP_Loop;

		$loop = $CP_Loop->get_loop( $atts['name'] );

		if ( $content ) {
			$content = str_replace( '[', '{', $content );
			$content = str_replace( ']', '}', $content );
			$content = str_replace( '=>', ':', $content );
			$content = str_replace( '=&gt;', ':', $content );
			$content = str_replace( '\'', '"', $content );
			$content = str_replace( '’', '"', $content );
			$content = str_replace( '&#8217;', '"', $content );
			$content = str_replace( '&#8242;', '"', $content );

			$new_atts = json_decode( $content, true );

			global $CP_Loop;
			$loop['args'] = $CP_Loop->merge_attributes( $new_atts, $loop['args'] );

		}

		foreach ( $atts as $key => $att ) {
			if ( preg_match( '/args_[a-z_]+/', $key, $matches ) ) {
				$key = str_replace( 'args_', '', $matches[0] );
				$loop['args'][ $key ] = $att;
			} else {
				$loop[ $key ] = $att;
			}
		}

		return $CP_Loop->show_loop( $loop );
	}

	/**
	 *
	 */
	public function process_content( $content ) {
		$attr = array();
	}

	// class end
}
