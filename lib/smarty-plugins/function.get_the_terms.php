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
function smarty_function_get_the_terms($params, $template) {
    $terms = get_the_terms($params['id'], $params['taxonomy']);
    $template->assign($params['out'], $terms);
}