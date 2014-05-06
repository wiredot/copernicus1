<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage PluginsFunction
 */

/**
 * WP bloginfo function
 *
 * Type:     function
 * Name:     bloginfo
 * Purpose:  print out a bloginfo information
 *
 */
function smarty_function_page_menu($params, $template) {
	global $CP_Menu;

	// default params
	$default_params = array(
        'depth'       => 0,
		'sort_column' => 'menu_order, post_title',
		'menu_class'  => 'menu',
		'include'     => '',
		'exclude'     => '',
		'echo'        => false,
		'show_home'   => false,
		'link_before' => '',
		'link_after'  => '',
		// additional functionality not available in standard wp_page_menu
		'container' => 'div',
		'container_id' => '',
		'container_class' => '',
		'menu_id' => ''
	);

	if (isset($params['id'])) {
		$config_params = $CP_Menu->get_page_menu($params['id']);

		if ($config_params && is_array($config_params)) {
			$default_params = array_merge($default_params, $config_params['args']);
		}
	}
    
    // merge default params with the provided ones
	$params = array_merge($default_params, $params);

	$params['echo'] = false;

	$params['new_menu_class'] = $params['menu_class'];
	$params['menu_class'] = '';

    $navigation = wp_page_menu($params);

    $navigation = preg_replace('/<div [a-z="]+>/', '', $navigation);
	$navigation = preg_replace('/<\/div>/', '', $navigation);

	if ($params['container']) {
		if ($params['container_id']) {
			$params['container_id'] = ' id="'.$params['container_id'].'"';
		}
		if ($params['container_class']) {
			$params['container_class'] = ' class="'.$params['container_class'].'"';
		}
		$navigation = '<'.$params['container'].$params['container_id'].$params['container_class'].'>'.$navigation.'</'.$params['container'].'>';
	}

	if ($params['menu_id']) {
		$params['menu_id'] = ' id="'.$params['menu_id'].'"';
	}
	if ($params['new_menu_class']) {
		$params['new_menu_class'] = ' class="'.$params['new_menu_class'].'"';
	}

	$navigation = preg_replace('/<ul>/', '<ul'.$params['menu_id'].''.$params['new_menu_class'].'>', $navigation, 1);

    return $navigation;
}
