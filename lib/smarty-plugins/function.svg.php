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
function smarty_function_svg($params, $template) {

	$svg = '<svg';
	
	if (isset($params['class'])) {
		$svg.= ' class="'.$params['class'].'"';
	}
	
	$svg.= '><use xlink:href="/wp-content/themes/'.get_template().$params['file'].'"></use></svg>';
	return $svg;
}
