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
function smarty_function_the_slug($params, $template) {
    
    // default params
	$default_params = array(
		'id' => get_the_ID()
	);
    
    // merge default params with the provided ones
	$params = array_merge($default_params, $params);

	$the_post = get_post($params['id']); 
	return $the_post->post_name;
}