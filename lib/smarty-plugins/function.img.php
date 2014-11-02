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
function smarty_function_img($params, $template) {
    
    // default params
	$default_params = array(
		'image_id' => null,
		'id' => null
	);
    
    // merge default params with the provided ones
	$params = array_merge($default_params, $params);
	
	if ( ! $params['image_id'] ) {
		$id = get_the_id();
		if ( ! $id ) {
			return null;
		}

		$params['image_id'] =  get_post_thumbnail_id( $id );
	}

	if ( ! $params['image_id'] ) {
		return null;
	}

	if ( ! isset($params['alt']) ) {
		$params['alt'] = get_post_meta( $params['image_id'], '_wp_attachment_image_alt', true );
	}

	if ( ! isset($params['title']) ) {
		$params['title'] = get_the_title( $params['image_id'] );
	}

	$attributes = array(
		'alt' => $params['alt'],
		'title' => $params['title']
	);

	$image = new CP_Imagenew($params['image_id'], $attributes);
	return $image->get_image_tag($params['id']);
}