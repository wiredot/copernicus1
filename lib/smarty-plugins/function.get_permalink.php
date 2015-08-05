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
function smarty_function_get_permalink($params, $template) {
	if (isset($params['id'])) {
		$permalink = get_permalink($params['id']);
	} else {
		$permalink = get_permalink();
	}

	$permalink = str_replace("http://", "", $permalink);
	$permalink = str_replace("//", "/", $permalink);

	return 'http://'.$permalink;	
}