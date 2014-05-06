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
function smarty_function_current_user($params, $template) {
	if (!isset($params['key'])) {
		$params['key'] = 'id';
	}
	
	$current_user = wp_get_current_user();
	
	if ($current_user) {
		switch($params['key']) {
			case 'id':
				return $current_user->ID;
				break;
			case 'username':
				return $current_user->user_login;
				break;
			case 'email':
				return $current_user->user_email;
				break;
			case 'first_name':
				return $current_user->user_firstname;
				break;
			case 'last_name':
				return $current_user->user_lastname;
				break;
			case 'display_name':
				return $current_user->display_name;
				break;
		}
	}

	return null;
}
