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
function smarty_function_user($params, $template) {
	if (!isset($params['id'])) {
		return null;
	}
	
	$user = get_userdata($params['id']);
	
	if ($user) {
		switch($params['key']) {
			case 'id':
				return $user->ID;
				break;
			case 'username':
				return $user->user_login;
				break;
			case 'email':
				return $user->user_email;
				break;
			case 'first_name':
				return $user->user_firstname;
				break;
			case 'last_name':
				return $user->user_lastname;
				break;
			case 'display_name':
				return $user->display_name;
				break;
		}
	}

	return null;
}
