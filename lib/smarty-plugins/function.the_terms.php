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
function smarty_function_the_terms($params, $template) {

	// if key is not defined, return nothing
	if (!isset($params['key'])) {
		return null;
	}

	// default params
	$default_params = array(
		'id' => get_the_ID(),
		'assign' => 'the_terms',
		'array_key' => 'all'
	);
    
    // merge default params with the provided ones
	$params = array_merge($default_params, $params);

	$the_terms = get_the_terms( $params['id'], $params['key'] );

	$the_terms_array = array();
    
    if(!isset($the_terms->errors) && $the_terms) {

        foreach ($the_terms as $key => $term) {
        	$the_terms[$key]->link = get_term_link( $term );
        	$term_array = get_object_vars($the_terms[$key]);
        	
        	if ($params['array_key'] == 'all') {
	            $the_terms_array[$key] = $term_array;
        	}
        	else {
        		if (isset($term_array[$params['array_key']])) {
        			$the_terms_array[] = $term_array[$params['array_key']];
        		}
        	}
        }
    }

	$template->assign($params['assign'], $the_terms_array);
    return null;
}