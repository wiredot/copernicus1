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
function smarty_function_get_term_link( $params, $template ) {
	$link = get_term_link( intval( $params['term'] ), $params['taxonomy'] );
	return $link;
}
