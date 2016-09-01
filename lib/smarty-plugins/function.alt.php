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
function smarty_function_alt($params, $template) {
	if ( ! isset($params['id'])) {
		$id = get_the_id();
	} else {
		$id = $params['id'];
	}

	return get_post_meta( $id, '_wp_attachment_image_alt', true );
}