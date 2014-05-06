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
function smarty_function_post_thumbnail_id($params, $template) {
    
    // default params
	$default_params = array(
		'id' => null
	);
    
    // merge default params with the provided ones
	$params = array_merge($default_params, $params);
	
	$post_thumbnail_id = get_post_thumbnail_id($params['id']);
	
	if ($post_thumbnail_id)
		return $post_thumbnail_id;
	
    return null;
    
}