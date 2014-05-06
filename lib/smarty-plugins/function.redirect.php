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
function smarty_function_redirect($params, $template) {
	if (isset($params['id']) && $params['id']) {
		global $post;
		if ($post->ID != $params['id']) {
			$link = get_permalink($params['id']);
			wp_redirect($link);
		}
	}
	exit;
}
