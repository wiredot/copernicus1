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
		$the_post = get_post( $params['id'] );
		return $the_post->post_excerpt;
	}

	return get_the_excerpt();
}
