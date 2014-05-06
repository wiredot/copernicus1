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
    global $CP_Language;
	
	$languages = $CP_Language->get_languages();
	
	$menu = '';
	
	foreach ($languages as $key => $language) {
		$menu.= '<li>';
		$menu.= '<a href="/index.php?lang='.$language['code'].'"';
		if ($language['code'] == LANGUAGE) {
			$menu.= ' class="active"';
		}
		$menu.= '>';
		$menu.= $language['short_name'];
		$menu.= '</a>';
		$menu.= '</li>';
	}
	
	return $menu;
	
    
}