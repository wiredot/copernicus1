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
 * Name:     get_the_title
 * Purpose:  print out a title
 *
 */
function smarty_function_the_author_id($params, $template) {

	// default params
	$default_params = array(
		'id' => null,
	);

	// merge default params with the provided ones
	$params = array_merge($default_params, $params);
	global $wpdb;


	$post = get_post($params['id']);
	return $post->post_author;
}
