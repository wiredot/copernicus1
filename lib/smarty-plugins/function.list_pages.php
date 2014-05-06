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
function smarty_function_list_pages($params, $template) {
	global $CP_Menu;

	// default params
	$default_params = array(
		'depth'        => 0,
		'show_date'    => '',
		'date_format'  => get_option('date_format'),
		'child_of'     => 0,
		'exclude'      => '',
		'include'      => '',
		'title_li'     => __('Pages'),
		'echo'         => 1,
		'authors'      => '',
		'sort_column'  => 'menu_order, post_title',
		'link_before'  => '',
		'link_after'   => '',
		'walker'       => '',
		'post_type'    => 'page',
        'post_status'  => 'publish' 
	);

	if (isset($params['id'])) {
		$config_params = $CP_Menu->get_page_list($params['id']);

		if ($config_params && is_array($config_params)) {
			$default_params = array_merge($default_params, $config_params['args']);
		}
	}
    
    // merge default params with the provided ones
	$params = array_merge($default_params, $params);


	$params['echo'] = false;
	unset($params['id']);

    $navigation = wp_list_pages($params);

    return $navigation;
}
