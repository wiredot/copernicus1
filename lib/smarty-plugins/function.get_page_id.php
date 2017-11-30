<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage PluginsFunction
 */

/**
 * WP bloginfo function
 *
 * Type:     function<br>
 * Name:     bloginfo<br>
 * Purpose:  print out a bloginfo information
 *
 */
function smarty_function_get_page_id( $params, $template ) {
	global $wpdb;

	if ( isset( $params['name'] ) ) {
		$post_id = $wpdb->get_var( '
			SELECT min(ID) 
			FROM ' . $wpdb->posts . ", 
			WHERE post_name = '" . $params['name'] . "' 
				AND post_status = 'publish'
			" );

		return $post_id;
	}

	if ( isset( $params['template'] ) ) {
		$post_id = $wpdb->get_var( '
			SELECT min(post_id) 
			FROM ' . $wpdb->postmeta . ', ' . $wpdb->posts . " 
			WHERE ID = post_id
				AND post_status = 'publish' 
				AND meta_key = '_cp_template' 
				AND meta_value = '" . $params['template'] . "' 
				AND post_status = 'publish'
		" );

		return $post_id;
	}

	return get_the_ID();
}
