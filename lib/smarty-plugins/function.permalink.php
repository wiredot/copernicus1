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
function smarty_function_permalink($params, $template) {
    
    // default params
	$default_params = array(
		'id' => null
	);
    
    // merge default params with the provided ones
	$params = array_merge($default_params, $params);

	return get_permalink($params['id']);
}
