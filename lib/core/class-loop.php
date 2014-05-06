<?php

/**
 * Copernicus meta box class file
 *
 * @package Copernicus
 * @subpackage Copernicus Theme
 * @author Piotr Soluch
 */

/**
 * meta box class
 *
 * @package Copernicus
 * @subpackage Copernicus Theme
 * @author Piotr Soluch
 */
class CP_Loop {

	// all meta boxes
	private $loop = array();

	/**
	 * Class constructor
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function __construct() {
		
		// initialize the meta boxes
		$this->_init();
	}

	/**
	 * Initiate the meta boxes
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function _init() {

		if (isset (CP::$config['loop'])) {
			
			// get meta box configuration
			$this->loop = CP::$config['loop'];
		}
	}

	public function get_loop($name) {
		foreach ($this->loop as $loop) {
			if ($loop['name'] == $name)
				return $loop;
		}
		
		return null;
	}
	
	public function show_loop($loop) {
		global $post, $pages, $CP_Smarty;
		
		$main_post = $post;
		$main_pages = $pages;
		
		$return = '';
		
		if (isset($loop['args']['orderby'])) {
			$orderby = $loop['args']['orderby'];
			switch($orderby) {
				case 'none':
				case 'ID':
				case 'author':
				case 'title':
				case 'name':
				case 'date':
				case 'modified':
				case 'parent':
				case 'rand':
				case 'comment_count':
				case 'menu_order':
					$loop['args']['orderby'] = $orderby;
					break;
				default:
					$loop['args']['orderby'] = 'meta_value';
					$loop['args']['meta_key'] = $orderby;
					break;
			}
		}
		
		$WP_loop = new WP_Query( $loop['args'] );
		$key = 0;
		
		while ( $WP_loop->have_posts() ) : $WP_loop->the_post();
			$CP_Smarty->smarty->assign('key', $key);
			$return.= $CP_Smarty->smarty->fetch($loop['template']);;
			$key++;
		endwhile;
		
		$post = $main_post;
		$pages = $main_pages;
		
		if (isset($loop['wrapper']) && $loop['wrapper']) {
			$return = str_replace('|', $return, $loop['wrapper']);
		}
		
		return $return;
	}

	function merge_attributes($new_arguments, $old_arguments) {
		
		foreach ($new_arguments as $key => $arg) {
			// for meta query arguments
			if ($key == 'meta_query') {

				// if the loop in config has NO meta_query
				if (!isset($old_arguments[$key])) {
					$old_arguments[$key] = $arg;
				}

				// if the loop in config has meta_query
				else {

					foreach ($arg AS $arg_key => $arg_value) {
						
						$added = 0;

						foreach ($old_arguments[$key] as $loop_key => $loop_value) {
							
							if ($arg_value['key'] == $loop_value['key']) {
								//new dBug($arg_value);
								//new dBug($old_arguments[$key][$loop_key]);
								$new_arg = array_merge($old_arguments[$key][$loop_key], $arg_value);
								//new dBug($new_arg);
								$old_arguments[$key][$loop_key] = $new_arg;
								
								$added = 1;
							}
						}
						
						if (! $added) {
							$old_arguments[$key][] = $arg_value;
						}
					}
				}
			}

			else {
				$old_arguments[$key] = $arg;
			}
		}

		return $old_arguments;
	}
	
}