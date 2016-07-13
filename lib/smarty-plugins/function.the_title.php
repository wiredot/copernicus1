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
function smarty_function_the_title($params, $template) {

	// default params
	$default_params = array(
		'id' => get_the_ID()
	);
    
    // merge default params with the provided ones
	$params = array_merge($default_params, $params);
	if (LANGUAGE_SUFFIX != '') {
		$post_title = get_post_meta($params['id'], 'post_title' . LANGUAGE_SUFFIX, true);

		if ($post_title) {
			return $post_title;
		}
	}
	
	return get_the_title($params['id']);
}
