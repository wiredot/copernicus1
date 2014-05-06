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
function smarty_function_option($params, $template) {
	
    $options = get_option( $params['option']);

    if (isset($params['key'])) {
    	if (isset($options[$params['key']])) {
    		$option = $options[$params['key']];
    	}
    } else {
    	$option = $options;
    }

    if (isset($params['assign'])) {
    	$template->assign($params['assign'], $option);
    	return null;
    }

    return $option;
}