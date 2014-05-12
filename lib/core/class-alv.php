<?php

/**
 * Copernicus Admin List View class file
 *
 * @package Copernicus
 * @subpackage Copernicus Theme
 * @author Piotr Soluch
 */

/**
 * Admin List View class
 *
 * @package Copernicus
 * @subpackage Copernicus Theme
 * @author Piotr Soluch
 */
class CP_Alv {

	// part of config with all alvs
	private $alv = array();
	private $mb = array();
	
	private $alv_fields = array();
	private $mb_fields = array();

	/**
	 * Class constructor
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function __construct() {
		// initialize the custom post types
		$this->_init();
	}

	/**
	 * Initiate the theme
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function _init() {

		if (isset(CP::$config['alv'])) {
			$this->alv = CP::$config['alv'];
			
			if (isset (CP::$config['mb'])) {
			
				// get meta box configuration
				$this->mb = CP::$config['mb'];
			}

			if (is_admin()) {
			
				// Modify the title bars
				$this->modify_list_views();
				
				add_action('manage_posts_custom_column', array($this, 'custom_columns'), 10, 2);
				add_action('manage_pages_custom_column', array($this, 'custom_columns'), 10, 2);
				
				add_filter('pre_get_posts', array($this, 'set_list_views_order'));
			}
		}
	}

	/**
	 * Take the alvs from config and create alv
	 *
	 * @access type public
	 * @return type null doesn't return a value
	 * @author Piotr Soluch
	 */
	public function modify_list_views() {

		// if there are alvs
		if (is_array($this->alv)) {

			// for each alv
			foreach ($this->alv AS $alv) {

				// if alv is active
				if ($alv['settings']['active']) {
					
					if (isset($_GET['post_type']) && $_GET['post_type'] == $alv['settings']['post_type']) {
						
						$this->alv_fields = $alv['fields'];
						$this->get_mb_fields($alv['settings']['post_type']);

						// create alv
						add_filter('manage_edit-' . $alv['settings']['post_type'] . '_columns', array($this, 'modify_list_view'));
					}
				}
			}
		}
	}
	
	/**
	 * Create a custom post type
	 *
	 * @access type public
	 * @return type null doesn't return a value
	 * @author Piotr Soluch
	 */
	public function modify_list_view($columns) {
		$new_columns = array();
		
		if (isset($columns['cb']))
			$new_columns['cb'] = $columns['cb'];
		
		foreach ($this->alv_fields as $key => $field) {
			switch($field) {
				case 'title':
				case 'date':
				case 'author':
					$field_name = ucfirst($field);
					break;
				case 'ID':
					$field_name = $field;
					break;
				case 'menu_order':
					$field_name = "Order";
					break;
				case 'featured_image':
					$field_name = "Image";
					break;
				case (preg_match('/image:(.*)/', $field, $matches) ? true : false) :
					$field_name = 'Image';
					break;
				case (preg_match('/taxonomy:(.*)/', $field, $matches) ? true : false) :
					$taxonomy = get_taxonomy($matches[1]);
					$field_name = $taxonomy->labels->name;
					break;
				default:
					$field_name = $this->mb_fields[$field]['name'];
					break;
			}
			$new_columns[$field] = $field_name;
			
		}
		
		return $new_columns;
	}
	
	/**
	 * 
	 * @param type $post_type
	 */
	private function get_mb_fields($post_type) {
		
		$fields = array();
		
		foreach ($this->mb as $mb) {
			
			if (is_array($mb['fields'])) {
				foreach ($mb['fields'] as $field) {
					$fields[$field['id']] = $field;
				}
			}
		}
		$this->mb_fields = $fields;
	}

	/**
	 * 
	 * @global type $CP_Mb
	 * @param type $column
	 * @param type $post_id
	 */
	public function custom_columns($column, $post_id) {
		global $CP_Mb;
		
		switch($column) {
			case 'ID':
				echo $post_id;
				break;
			case 'menu_order':
				$post = get_post($post_id);
				echo $post->menu_order;
				break;
			case 'featured_image':
				$post_thumbnail_id = get_post_thumbnail_id($post_id);
				
				if ($post_thumbnail_id) {
					global $CP_Image;
					$params = array(
						'id' => $post_thumbnail_id,
						'w' => 50,
						'h' => 50,
						'zc' => 1,
						'q' => 70
					);
					echo $CP_Image->image($params);
				}
				break;
			case (preg_match('/taxonomy:(.*)/', $column, $matches) ? true : false) :
				$terms = get_the_terms( $post_id, $matches[1] );
				if (!isset($terms->errors) && $terms) {
					foreach ($terms as $key => $term) {
						echo $term->name;
						if ($key != end(array_keys($terms))) {
							echo ', ';
						}
					}
				}
				else {
					echo ' ';
				}
				break;
			case (preg_match('/image:(.*)/', $column, $matches) ? true : false) :
				$value = get_post_meta($post_id, $matches[1], 1);
				if (isset($value[0])) {
					global $CP_Image;
					$params = array(
						'id' => $value[0],
						'w' => 50,
						'h' => 50,
						'zc' => 1,
						'q' => 70
					);
					echo $CP_Image->image($params);
				}
				else {
					echo ' ';
				}
				break;
			default:
				$value = get_post_meta($post_id, $column, 1);
				if ($value) {
					$field = $this->mb_fields[$column];
					echo $CP_Mb->get_value($field, $value);
				}
				break;
		}
	}

	/**
	 * 
	 * @param type $wp_query
	 */
	public function set_list_views_order($wp_query) {
		// Get the post type from the query  
		$post_type = $wp_query->query['post_type'];

		// if there are alvs
		if (is_array($this->alv) && ! isset($_GET['orderby'])) {

			// for each alv
			foreach ($this->alv AS $alv) {

				// if alv is active
				if ($alv['settings']['post_type'] == $post_type) {
					
					if (isset($alv['settings']['orderby'])) {
						
						$orderby = $alv['settings']['orderby'];

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
								$wp_query->set('orderby', $orderby);
								break;
							default:
								$wp_query->set('orderby', 'meta_value');
								$wp_query->set('meta_key', $orderby);
								break;
						}
					}

					if (isset($alv['settings']['order']))
						$wp_query->set('order', $alv['settings']['order']);
				}
			}
		}
	}
}