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
function smarty_function_the_date($params, $template) {
	
	// default params
	$default_params = array(
		'id' => null,
		'd' => get_option('date_format')
	);
    
    // merge default params with the provided ones
	$params = array_merge($default_params, $params);

	if ($params['id']) {
		$the_post = get_post($params['id']);
		return mysql2date($params['d'], $the_post->post_date);
	}

	return get_the_date($params['d']);
}