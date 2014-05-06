<?php
/**
 * Smarty plugin
 *
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
function smarty_function_the_content( $params, $template ) {
	global $more;
	$more = 1;

	// default params
	$default_params = array(
		'more_link_text' => null,
		'stripteaser' => false,
		'id' => null,
		'html' => false,
		'excerpt' => false
	);
    
    // merge default params with the provided ones
	$params = array_merge($default_params, $params);

	$content = '';

	if ($params['excerpt']) {
		$more = 0;
	}

	if (!$params['more_link_text']) {
		add_filter( 'the_content_more_link',  'return_null');
	}

	if (LANGUAGE_SUFFIX != '') {
		if (!$params['id']) {
			$params['id'] = get_the_ID();
		}
		$content = get_post_meta($params['id'], 'content' . LANGUAGE_SUFFIX, true);
	}
	else {
		if ($params['id']) {
			$current_page = get_post($params['id'], ARRAY_A);

			if ($current_page) {
				$content = $current_page['post_content'];
			}
		}
	}

	if (!$content) {
		$content = get_the_content($params['more_link_text'], $params['stripteaser']);
	}

	if ($params['excerpt'] || $params['stripteaser']) {
		$content_parts = explode("<!--more-->", $content);
		if ($params['excerpt'] && isset($content_parts[0])) {
			$content = $content_parts[0];
		} else if($params['stripteaser'] && isset($content_parts[1])) {
			$content = $content_parts[1];
		}
	}

	
	if ($params['html']) {
		return apply_filters('the_content', $content);
	}
	
	return $content;
}

function return_null() {
	return null;
}
