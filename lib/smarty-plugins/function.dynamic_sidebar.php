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
function smarty_function_dynamic_sidebar($params, $template) {
	if ( ! isset($params['index'])) {
		$params['index'] = '';
	}

	ob_start();
	dynamic_sidebar($params['index']);
	$out = ob_get_contents();
	ob_end_clean();
	
	return $out;
}
