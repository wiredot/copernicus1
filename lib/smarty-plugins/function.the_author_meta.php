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
function smarty_function_the_author_meta($params, $template) {

	// default params
	$default_params = array(
		'id' => null,
		'field' => 'ID'
	);

	// merge default params with the provided ones
	$params = array_merge($default_params, $params);
	
	return get_the_author_meta( $params['field'], $params['id'] );
}
