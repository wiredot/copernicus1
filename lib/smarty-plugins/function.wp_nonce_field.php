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
function smarty_function_wp_nonce_field( $params, $template ) {
	$default_params = array(
		'action' => '-1',
		'name' => '_wpnonce',
		'referer' => true,
		'echo' => false,
	);

	$params = array_merge( $default_params, $params );

	$nonce = wp_nonce_field( $params['action'], $params['name'], $params['referer'], $params['echo'] );

	return $nonce;
}
