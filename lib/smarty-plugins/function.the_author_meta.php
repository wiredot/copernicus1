<?php
/**
 * Smarty plugin
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
function smarty_function_the_author_meta( $params, $template ) {
	if ( ! isset( $params['id'] ) ) {
		$author = get_user_by( 'slug', get_query_var( 'author_name' ) );
		$params['id'] = $author->ID;
	}

	if ( $params['field'] == 'id' ) {
		return $params['id'];
	}

	return get_the_author_meta( $params['field'], $params['id'] );
}
