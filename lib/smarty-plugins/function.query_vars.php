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
function smarty_function_query_vars($params, $template) {
	global $wp_query;

	if (!isset($params['key'])) {
		return null;
	}

	if (isset($wp_query->query_vars[$params['key']])) {
		return $wp_query->query_vars[$params['key']];
	}

	return null;
}
