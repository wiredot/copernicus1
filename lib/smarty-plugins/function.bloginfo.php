<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage PluginsFunction
 */

/**
 * WP bloginfo function
 *
 * Type:     function
 * Name:     bloginfo
 * Purpose:  print out a bloginfo information
 *
 */
function smarty_function_bloginfo($params, $template) {

	// default params
	$default_params = array(
		'show' => null
	);
    
    // merge default params with the provided ones
	$params = array_merge($default_params, $params);
	
    return bloginfo($params['show']);
}
