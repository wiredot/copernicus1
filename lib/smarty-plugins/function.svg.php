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
	// $svg = new SimpleXMLElement( file_get_contents(get_bloginfo('template_directory').'/'.$params['file']) );

	// $svg->registerXPathNamespace('svg', 'http://www.w3.org/2000/svg');
	// $svg->registerXPathNamespace('xlink', 'http://www.w3.org/1999/xlink');


	// $result = $svg->xpath('/svg:svg');
	
	//print_r($result);
	$svg = '<svg class="fill-red">
    <use xlink:href="/wp-content/themes/wd-media-szukajacboga-pl'.$params['file'].'"></use>
</svg>';
  return $svg;
	return '<img src="'.get_bloginfo('template_url').'/'.$params['file'].'">'; 
}
