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
function smarty_function_language_menu($params, $template) {
    global $CP_Language, $CP_Permalink;
	
	$languages = $CP_Language->get_languages();
	
	$menu = '';

	$id = get_the_ID();

	foreach ($languages as $key => $language) {
		$menu.= '<li>';
		$menu.= '<a href="'.$CP_Permalink->get_permalink( $id, $language['prefix'] ).'"';
		if ($language['code'] == LANGUAGE) {
			$menu.= ' class="active"';
		}
		$menu.= '>';
		$menu.= $language['code'];
		$menu.= '</a>';
		$menu.= '</li>';
	}
	
	return $menu;
	
    
}