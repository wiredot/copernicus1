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
function smarty_function_menu_lang($params, $template) {

	$navigation = '';

	 if (isset(CP::$config['language'])) {
	 	$navigation.= '<ul>';
	 	foreach (CP::$config['language'] as $language) {
		 	$navigation.= '<li>';
		 	$navigation.= '<a href="/?lang='.$language['short_name'].'">'.$language['name'].'</a>';
	 		
		 	$navigation.= '</li>';
	 	}
	 	$navigation.= '</ul>';
	 }
	return $navigation;
}
