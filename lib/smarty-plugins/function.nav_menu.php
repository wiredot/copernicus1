<?php
/**
 * Smarty plugin
 *
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
function smarty_function_nav_menu( $params, $template ) {
	global $CP_Menu;

	// default params
	$default_params = array(
		'theme_location'  => '',
		'menu'            => '',
		'container'       => 'div',
		'container_class' => '',
		'container_id'    => '',
		'menu_class'      => 'menu',
		'menu_id'         => '',
		'echo'            => false,
		'fallback_cb'     => 'wp_page_menu',
		'before'          => '',
		'after'           => '',
		'link_before'     => '',
		'link_after'      => '',
		'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
		'depth'           => 0,
		'walker'          => ''
	);

	if (isset($params['location'])) {
		$config_params = $CP_Menu->get_nav_menu($params['location']);
		if ($config_params && is_array($config_params)) {
			$default_params = array_merge($default_params, $config_params['args']);
			$default_params['theme_location'] = $params['location'];
		}
	}

    // merge default params with the provided ones
	$params = array_merge($default_params, $params);

	$params['echo'] = false;

	return wp_nav_menu( $params );
}
