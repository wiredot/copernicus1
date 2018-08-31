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
function smarty_function_menu_lang( $params, $template ) {
	$navigation = '';

	// default params
	$default_params = array(
		'class' => null,
		'id' => null,
		'url' => get_site_url(),
	);

	// merge default params with the provided ones
	$params = array_merge( $default_params, $params );

	if ( isset( CP::$config['language'] ) ) {
		$navigation .= '<ul';

		if ( $params['id'] ) {
			$navigation .= ' id="' . $params['id'] . '"';
		}

		if ( $params['class'] ) {
			$navigation .= ' class="' . $params['class'] . '"';
		}

		$navigation .= '>';
		foreach ( CP::$config['language'] as $language ) {
			$navigation .= '<li>';
			$navigation .= '<a href="' . $params['url'] . '/' . $language['prefix'];
			if ( $language['prefix'] ) {
				$navigation .= '/';
			}
			$navigation .= '">' . $language['name'] . '</a>';

			$navigation .= '</li>';
		}
		 $navigation .= '</ul>';
	}
	return $navigation;
}
