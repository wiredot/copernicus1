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
function smarty_function_post_meta($params, $template) {

	$default_id = null;
	if (@get_the_ID()) {
		$default_id = get_the_ID();
	}

	// default params
	$default_params = array(
		'id' => $default_id,
		'key' => '',
		'html' => false,
		'shortcode' => false,
		'more_link_text' => 'read more',
		'stripteaser' => false,
		'excerpt' => false,
		'assign' => null
	);

    // merge default params with the provided ones
	$params = array_merge($default_params, $params);

	$post_meta = '';

	if (LANGUAGE_SUFFIX != '') {
		$post_meta = get_post_meta($params['id'], $params['key'] . LANGUAGE_SUFFIX);
	}

	if (!$post_meta) {
		$post_meta = get_post_meta($params['id'], $params['key']);
	}
	$post_meta = maybe_unserialize($post_meta);
	$post_meta = strip_array($post_meta);

	if ($params['html']) {
		$post_meta = apply_filters('the_content', $post_meta);
	}

	if ($params['shortcode']) {
		$post_meta = do_shortcode($post_meta);
	}
	
	if ($params['stripteaser'] || $params['excerpt']) {
		global $more;
		$more = 1;
		$post_parts = preg_split('/<!--more(.*?)?-->/', $post_meta);
		if ($params['excerpt']) {
			$post_meta = $post_parts[0];
			$post_meta.= ' <a href="'.get_permalink($params['id']).'" class=more-link>'.$params['more_link_text'].'</a>';
		}
		else {
			$post_meta = $post_parts[1];
		}
	}

	if (isset($params['assign']) && $params['assign']) {
		$template->assign($params['assign'], $post_meta);
		return;
	}

	// DEPRICIATED - will be removed
	if (isset($params['out']) && $params['out']) {
		$template->assign($params['out'], $post_meta);
		return;
	}
	
	return $post_meta;
}

function strip_array($array) {

	if (is_array($array)) {
		if (count($array) == 0) {
			return null;
		} else if (count($array) == 1) {
			$array = $array[key($array)];
			return $array;
		}
	}

	return $array;
}