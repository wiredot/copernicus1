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
function smarty_function_the_post($params, $template) {

	// default params
	$default_params = array(
		'id' => get_the_ID(),
		'key' => ''
	);
    
    // merge default params with the provided ones
	$params = array_merge($default_params, $params);

	$the_post = get_post( $params['id'], $output = ARRAY_A );

	if (!$the_post) 
		return null;

	//new dBug($the_post);

	switch ($params['key']) {
		case 'ID':
		case 'post_parent':
			return $the_post[$params['key']];
			break;
	}
	
	return null;
}
