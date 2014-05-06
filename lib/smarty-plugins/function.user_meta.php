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
function smarty_function_user_meta($params, $template) {
	// default params
	$default_params = array(
		'id' => get_current_user_id(),
		'key' => '',
		'assign' => null
	);

	// merge default params with the provided ones
	$params = array_merge($default_params, $params);

	if (!$params['id']) {
		return null;
	}

	$user_meta = get_user_meta( $params['id'], $params['key'], true );

	if (isset($params['assign']) && $params['assign']) {
		$template->assign($params['assign'], $user_meta);
		return;
	}
	
	return $user_meta;
}
