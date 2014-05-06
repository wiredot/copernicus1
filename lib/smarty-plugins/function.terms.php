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
function smarty_function_terms($params, $template) {

	// if taxonomies are not defined, return nothing
	if (!isset($params['taxonomies'])) {
		return null;
	}

	$default_args = array();

	$args = array();

	if (isset($params['args'])) {
		$args = array_merge($default_args, $params['args']);
	}

	if (!isset($params['assign']) || !$params['assign']) {
		$params['assign'] = $params['taxonomies'];
	}

	$terms = get_terms($params['taxonomies'], $args);
	
	$terms_array = array();
	
	if(!isset($terms->errors) && $terms) {

		foreach ($terms as $key => $term) {
			$terms[$key]->link = get_term_link( $term );
			$terms_array[$key] = get_object_vars($terms[$key]);
		}
	}
	
	$template->assign($params['assign'], $terms_array);
	return null;
}
