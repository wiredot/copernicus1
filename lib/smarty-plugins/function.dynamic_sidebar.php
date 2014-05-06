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
function smarty_function_dynamic_sidebar($params, $template) {

	// default params
	$default_params = array(
		'index' => 1
	);
    
    // merge default params with the provided ones
	$params = array_merge($default_params, $params);
	
    dynamic_sidebar( $params['index'] );
}
