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
function smarty_function_image($params, $template) {
	global $CP_Image;
	
	if ( ! isset($params['id']) || ! $params['id'] ) {
		$post_id = get_the_id();
		if ( ! $post_id ) {
			return null;
		}

		$thumbnail_id =  get_post_thumbnail_id( $id );

		if ($thumbnail_id) {
			$params['id'] = $thumbnail_id;
		} else {
			return null;
		}
	}

	return $CP_Image->get_image($params['id'], $params);
}