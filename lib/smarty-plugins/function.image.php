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
    
    // default params
	$default_params = array(
		'size' => null,
		'id' => null
	);
    
    // merge default params with the provided ones
	$params = array_merge($default_params, $params);
	
	if ( ! $params['id'] ) {
		$id = get_the_id();
		if ( ! $id ) {
			return null;
		}

		$params['id'] =  get_post_thumbnail_id( $id );
	}


	if ( ! $params['id'] ) {
		return null;
	}

	if ( ! isset($params['alt']) ) {
		$params['alt'] = get_post_meta( $params['id'], '_wp_attachment_image_alt', true );
	}

	if ( ! isset($params['title']) ) {
		$params['title'] = get_the_title( $params['id'] );
	}

	if ( ! isset($params['class']) ) {
		$params['class'] = null;
	}

	if ( ! isset($params['link']) ) {
		$params['link'] = null;
	}

	$attributes = array(
		'alt' => $params['alt'],
		'title' => $params['title'],
		'class' => $params['class']
	);

	$size = $params['size'];
	$id = $params['id'];
	$link = $params['link'];

	unset($params['alt']);
	unset($params['size']);
	unset($params['class']);
	unset($params['title']);
	unset($params['id']);
	unset($params['link']);

	$image = new CP_Image($id, $attributes);

	if ($link) {
		return $image->get_image_link($size, $params);
	}
	
	return $image->get_image_tag($size, $params);
}