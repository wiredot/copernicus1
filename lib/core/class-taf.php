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
	private $cpt = array();

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

		if (isset(CP::$config['taf']) && is_array(CP::$config['taf'])) {

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

	function show_taxonomy_custom_fields($taxonomy, $taxonomy_id) {
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

	function get_taxonomy_custom_fields($taxonomy_id) {
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

	function save_taxonomy_custom_fields($term_id, $aa) {

		if ( isset( $_POST['cp_term_meta'] ) ) {
			
			$term_meta = array();

			foreach ( $_POST['cp_term_meta'] as $key => $field ) {
				
				$term_meta[$key] = $_POST['cp_term_meta'][$key];
			}
			//save the option array
			if ( get_option( 'cp_term_meta_'.$term_id ) !== false ) {
				update_option( 'cp_term_meta_'.$term_id, $term_meta );
			} else {
				add_option( 'cp_term_meta_'.$term_id, $term_meta, null, 'no' );
			}
		}  
	}
}
