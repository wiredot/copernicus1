<?php

/**
 * Copernicus Custom Post Type class file
 *
 * @package Copernicus
 * @subpackage Copernicus Theme
 * @author Piotr Soluch
 */

/**
 * Custom Post Type class
 *
 * @package Copernicus
 * @subpackage Copernicus Theme
 * @author Piotr Soluch
 */
class CP_Taf {

	// part of config with all cpts
	private $taf = array();

	/**
	 * Class constructor
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function __construct() {
		if (isset(CP::$config['taf']) && is_array(CP::$config['taf'])) {

			$this->taf = CP::$config['taf'];

			// for each taxonomy
			foreach(CP::$config['taf'] AS $taf) {

				// if taxonomy is active
				if ($taf['settings']['active']) {
					add_action( $taf['settings']['taxonomy_id'].'_edit_form_fields', array($this, 'show_taxonomy_custom_fields'), 10, 2 );

					add_action( 'edited_'.$taf['settings']['taxonomy_id'], array($this, 'save_taxonomy_custom_fields'), 10, 2 );  
				}
			}
		}
	}

	/**
	 * 
	 */
	public function show_taxonomy_custom_fields($taxonomy, $taxonomy_id) {
		global $CP_Language, $CP_Field;
		$return = '';

		$term_id = $taxonomy->term_id;
		$term_meta = get_option( 'cp_term_meta_'.$term_id );

		$fields = $this->get_taxonomy_custom_fields($taxonomy_id);

		$languages = $CP_Language->get_languages();

		if (!is_array($fields)) {
			return null;
		}

		foreach ($fields as $field) {

			$return.= '<tr class="form-field">';
	    		$return.= '<th scope="row" valign="top">';
			    	$return.= '<label for="cp_term_meta['.$field['id'].']">'.$field['name'].'</label>';
		    	$return.= '</th>';
		    	$return.= '<td>';

		    		if (count($languages) > 1 && isset($field['translate']) && $field['translate']) {
		    			$return.= '<div class="cp-langs" id="langs_term_'.$field['id'].'">';

		    			if (!isset($_COOKIE['langs_term_'.$field['id']])) {
							$active = $languages[0]['code'];
						}
						else {
							$active = $_COOKIE['langs_term_'.$field['id']];
						}
						
						foreach ($languages as $language) {
							$return.= '<span id="_'.$field['id'].'_'.$language['code'].'" class="option';
							if ($active == $language['code'])
								$return.= ' active';
							$return.= '">'.$language['name'].'</span>';
						}

						$return.= '<div class="langs_list">';

						foreach ($languages as $language) {
				
							$return.= '<div id="div_'.$field['id'].'_'.$language['code'].'"';
							if ($active == $language['code'])
								$return.= ' class="active"';
							$return.= '>';
							//$return.= $this->show_taxonomy_custom_field($term_meta, $field, $language);

							$suffix = '';
							if (isset($language['postmeta_suffix'])) {
								$suffix = $language['postmeta_suffix'];
							}
							$value = '';
							if (isset($term_meta[$field['id'].$suffix])) {
								$value = $term_meta[$field['id'].$suffix];
							}
							$return.= $CP_Field->show_field($field, 'cp_term_meta['.$field['id'].$suffix.']', 'cp_term_meta['.$field['id'].']'.$suffix, $value);
							$return.= '</div>';
						}

		    			$return.= '</div>';
		    			$return.= '</div>';
		    		}
		    		else {
	    				//$return.= $this->show_taxonomy_custom_field($term_meta, $field);
	    				$value = '';
	    				if (isset($term_meta[$field['id']])) {
							$value = $term_meta[$field['id']];
						}
						$return.= $CP_Field->show_field($field, 'cp_term_meta_'.$field['id'].'', 'cp_term_meta['.$field['id'].']', $value);
		    			
		    		}
	    			$return.= '<br />';
	        	
	        		if (isset($field['description']) && $field['description']) 
	        			$return.= '<span class="description">'.$field['description'].'</span>';
	    		$return.= '</td>';
	    	$return.= '</tr>';
		}

		echo $return;
	}

	/**
	 * 
	 */
	public function get_taxonomy_custom_fields($taxonomy_id) {
		if (is_array(CP::$config['taf'])) {

			// for each taxonomy
			foreach(CP::$config['taf'] AS $taf) {

				// if taxonomy is active
				if ($taf['settings']['taxonomy_id'] == $taxonomy_id) {
					return $taf['fields'];
				}
			}
		}

		return null;
	}

	/**
	 * 
	 */
	public function save_taxonomy_custom_fields($term_id, $term_taxonomy_id) {

		$values = array();
		if ( isset($_POST['cp_term_meta']) ) {
			$values = $_POST['cp_term_meta'];
		}

		if (isset($_POST['taxonomy'])) {
			$taxonomy = $_POST['taxonomy'];

			$term_meta = array();

			foreach ($this->taf as $taf) {
				if ($taf['settings']['taxonomy_id'] == $taxonomy) {

					foreach ($taf['fields'] as $key => $field) {
						$key = $field['id'];

						if ($field['type'] == 'editor'){
							$values[$key] = stripslashes($values[$key]);
						}

						if ($field['type'] == 'upload') {
							$term_meta[$key] = $this->save_user_field_upload($key);
							$this->update_taxonomy_meta($term_id, $key, serialize($term_meta[$key]));
						} else {
							if ( ! isset($values[$key]) ) {
								$this->update_taxonomy_meta($term_id, $key, '');
								$term_meta[$key] = '';
							} else {
								$this->update_taxonomy_meta($term_id, $key, $values[$key]);
								$term_meta[$key] = $values[$key];
							}
						}
					}
				}
			}

			if ( get_option( 'cp_term_meta_'.$term_id ) !== false ) {
				update_option( 'cp_term_meta_'.$term_id, $term_meta );
			} else {
				add_option( 'cp_term_meta_'.$term_id, $term_meta, null, 'no' );
			}
		}
	}

	public function save_user_field_upload($key) {
		// Get the posted data
		$meta_value = ( isset($_POST['cp_term_meta'][$key]) ? $_POST['cp_term_meta'][$key] : '' );
		
		$meta = array();

		if (isset($meta_value['id'])) {
			foreach ($meta_value['id'] as $key => $id) {
				
				$meta[] = $id;

				$title = '';
				$caption = '';
				
				if (isset($meta_value['title'][$key])) {
					$title = $meta_value['title'][$key];
				}

				if (isset($meta_value['caption'][$key])) {
					$caption = $meta_value['caption'][$key];
				}

				wp_update_post( array(
					'ID' => $id,
					'post_title' => $title,
					'post_excerpt' => $caption,
				) );

				if (isset($meta_value['alt'][$key])) {
					update_post_meta( $id, 'alt', $meta_value['alt'][$key] );
				} else {
					delete_post_meta( $id, 'alt' );
				}
			}
		}
		return $meta;
	}

	public function get_taxonomy_meta($term_id, $meta_key) {
		global $wpdb;

		$exists = $wpdb->get_var("SELECT 1 FROM " . $wpdb->prefix . "termmeta LIMIT 1");

		if ($exists === FALSE) {
			return;
		}

		$sql = $wpdb->prepare("
			SELECT meta_value
			FROM " . $wpdb->prefix . "termmeta
			WHERE term_id = %d
				AND meta_key = %s
		",
			$term_id,
			$meta_key
		);

		$meta_value = $wpdb->get_var($sql);

		return $meta_value;
	}

	public function update_taxonomy_meta($term_id, $meta_key, $meta_value) {
		global $wpdb;

		$term_meta = get_option( 'cp_term_meta_'.$term_id );
		
		if ( $term_meta ) {
			$term_meta[$meta_key] = $meta_value;
			update_option( 'cp_term_meta_'.$term_id, $term_meta );
		} else {
			$term_meta = array($meta_key => $meta_value);
			add_option( 'cp_term_meta_'.$term_id, $term_meta, null, 'no' );
		}

		$exists = $wpdb->get_var("SELECT 1 FROM " . $wpdb->prefix . "termmeta LIMIT 1");

		if ($exists === FALSE) {
			return;
		}

		$sql = $wpdb->prepare("
			SELECT count(*)
			FROM " . $wpdb->prefix . "termmeta
			WHERE term_id = %d
				AND meta_key = %s
		",
			$term_id,
			$meta_key
		);

		$exists = $wpdb->get_var($sql);
		
		if ( ! $exists) {
			return $this->add_taxonomy_meta($term_id, $meta_key, $meta_value);
		}

		return $wpdb->update(
			$wpdb->prefix . "termmeta",
			array(
				'meta_value' => $meta_value
			),
			array(
				'term_id' => $term_id,
				'meta_key' => $meta_key
			)
		);
	}

	public function add_taxonomy_meta($term_id, $meta_key, $meta_value) {
		global $wpdb;

		$term_meta = array($meta_key => $meta_value);
		add_option( 'cp_term_meta_'.$term_id, $term_meta, null, 'no' );

		$exists = $wpdb->get_var("SELECT 1 FROM " . $wpdb->prefix . "termmeta LIMIT 1");

		if ($exists === FALSE) {
			return;
		}

		return $wpdb->insert(
			$wpdb->prefix . "termmeta",
			array(
				'meta_value' => $meta_value,
				'term_id' => $term_id,
				'meta_key' => $meta_key
			)
		);
	}

// class end
}
