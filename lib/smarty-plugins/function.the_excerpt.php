<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsFunction
 */

/**
 * WP get_the_content function
 *
 * Type:     function
 * Name:     get_the_content
 * Purpose:  print out a bloginfo information
 *
 */
function smarty_function_the_excerpt( $params, $template ) {
	// default params
	$default_params = array(
		'id' => null,
	);

	// merge default params with the provided ones
	$params = array_merge( $default_params, $params );

	if ( $params['id'] ) {

		if ( isset( $params['lang'] ) ) {
			global $CP_Language;
			$language = $CP_Language->get_language( $params['lang'] );
			if ( $language ) {
				if ( ! $language['default'] ) {
					$excerpt = get_post_meta( $params['id'], 'post_excerpt' . $language['postmeta_suffix'], true );
				}
			}
		} else if ( LANGUAGE_SUFFIX != '' ) {
			$excerpt = get_post_meta( $params['id'], 'post_excerpt' . LANGUAGE_SUFFIX, true );
		}

		if ( $excerpt ) {
			return $excerpt;
		}

		$the_post = get_post( $params['id'] );
		return $the_post->post_excerpt;
	}

	if ( LANGUAGE_SUFFIX != '' ) {
		$excerpt = get_post_meta( get_the_ID(), 'post_excerpt' . LANGUAGE_SUFFIX, true );

		if ( $excerpt ) {
			return $excerpt;
		}
	}

	return get_the_excerpt();
}
