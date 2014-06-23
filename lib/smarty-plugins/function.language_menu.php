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

	$current_language = $CP_Language->get_current_language();

	$wpurl = get_bloginfo( 'wpurl' );
	
	$menu = '';

	$id = get_the_ID();

	foreach ($languages as $key => $language) {
		$menu.= '<li>';

		$permalink = get_permalink($id);

		if (isset($current_language['prefix'])) {

			if (isset($language['prefix']) && $language['prefix']) {
				$new_url = $wpurl.'/'.$language['prefix'].'/';
			} else {
				$new_url = $wpurl;
			}

			$permalink = str_replace($wpurl.'/'.$current_language['prefix'], $new_url, $permalink);
		}

		$menu.= '<a href="'.$permalink.'"';
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