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
function smarty_function_the_author_user_login($params, $template) {

	$user = get_userdata($params['id']);
	return $user->data->user_login;
}
