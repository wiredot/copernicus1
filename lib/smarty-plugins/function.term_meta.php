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
function smarty_function_term_meta($params, $template) {

	// if key is not defined, return nothing
	if (!isset($params['key']) || !isset($params['id'])) {
		return null;
	}

	$meta = get_option( 'cp_term_meta_'.$params['id'] );

	if (!$meta || !isset($meta[$params['key']])) {
		return null;
	}

	$metaValue = $meta[$params['key']];

	if (isset($params['assign']) && $params['assign']) {
		$template->assign($params['assign'], $post_meta);
		return;
	}

	if (is_array($metaValue)) {
		if (count($metaValue) == 1) {
			return reset($metaValue);
		}
		return null;
	}

	return $metaValue;
}
