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
function smarty_function_wp_redirect($params, $template) {
	if (isset($params['id']) && $params['id']) {
		global $post;
		if ($post->ID != $params['id']) {
			$link = get_permalink($params['id']);
			echo '<script>window.location.href = "'.$link.'";</script>';
			exit;
		}
	} else if (isset($params['href']) && $params['href']) {
		echo '<script>window.location.href = "'.$params['href'].'";</script>';
		exit;
	}
	return null;
	exit;
}
