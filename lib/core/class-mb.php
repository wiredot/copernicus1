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
class CP_Mb {

	// all meta boxes
	public $mb = array();

	/**
	 * Class constructor
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function __construct() {

		add_action('wp_ajax_cp_mb_add_group', array($this,'add_group'));
		
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
		global $MS_Language;

		add_action('edit_form_after_title', array($this, 'add_main_box'));
		add_action('pre_post_update', array($this, 'save_main_box'), 10, 2);
		
		if (isset (CP::$config['mb'])) {
			
			// get meta box configuration
			$this->mb = CP::$config['mb'];
				
			// add meta boxes
			add_action('admin_init', array($this, 'add_meta_boxes'));

			// save meta boxes
			add_action('pre_post_update', array($this, 'save_meta_boxes'), 10, 2);
		}
	}

	public function add_main_box() {
		global $post, $CP_Language, $CP_Cpt;

		if ($CP_Language->get_language_count() < 2) 
			return;
	
		$post_type = $post->post_type;
		$post_type_object = get_post_type_object($post_type);

		$languages = $CP_Language->get_languages();

		$return = '';

		if ($post_type == 'page' || $post_type == 'post' || $CP_Cpt->is_supporting( $post_type, 'title' )) {
			$return.= '<div id="titlediv" class="cp-titlediv">';
				
				$return.= '<div id="titlewrap">';

					$return.= '<div class="cp-langs full" id="langs_post_title">';

						if (!isset($_COOKIE['langs_post_title'])) {
							$active = $languages[0]['code'];
						}
						else {
							$active = $_COOKIE['langs_post_title'];
						}

						foreach ($languages as $language) {
							$return.= '<span id="_post_title_'.$language['code'].'" class="option';
							if ($active == $language['code'])
								$return.= ' active';
							$return.= '">'.$language['name'].'</span>';
						}

						$return.= '<div class="langs_list">';

							foreach ($languages as $language) {

								if (isset($language['postmeta_suffix'])) {
									$suffix = $language['postmeta_suffix'];
								}
								else {
									$suffix = '';
								}
							
								$return.= '<div id="div_post_title'.'_'.$language['code'].'" class="';
								if ($active == $language['code'])
									$return.= ' active';
								$return.= '">';

									if ($language['default']) {
										$title = esc_attr( htmlspecialchars( $post->post_title ) );
									}
									else {
										$title = esc_attr( htmlspecialchars( get_post_meta( $post->ID, 'post_title'.$suffix, true ) ) );
									}

									$return.= '<label class="screen-reader-text" id="title-prompt-text'.$suffix.'" for="title'.$suffix.'">'.apply_filters( 'enter_title_here', __( 'Enter title here' ), $post ).'</label>';
									$return.= '<input type="text" name="post_title'.$suffix.'" size="30" class="cp-title" value="'.$title.'" id="title'.$suffix.'" autocomplete="off">';

								$return.= '</div>';
							}
						$return.= '</div>';
					$return.= '</div>';
					
				$return.= '</div>';
				$return.= '<div class="inside">';
					$sample_permalink_html = $post_type_object->public ? get_sample_permalink_html($post->ID) : '';
					$shortlink = wp_get_shortlink($post->ID, 'post');

					if ( !empty($shortlink) )
	    				$sample_permalink_html .= '<input id="shortlink" type="hidden" value="' . esc_attr($shortlink) . '" /><a href="#" class="button button-small" onclick="prompt(&#39;URL:&#39;, jQuery(\'#shortlink\').val()); return false;">' . __('Get Shortlink') . '</a>';


	    			if ( $post_type_object->public && ! ( 'pending' == get_post_status( $post ) && !current_user_can( $post_type_object->cap->publish_posts ) ) ) {
						$has_sample_permalink = $sample_permalink_html && 'auto-draft' != $post->post_status;
					
						$return.= '<div id="edit-slug-box" class="hide-if-no-js">';
							if ( $has_sample_permalink )
								$return.= $sample_permalink_html;
						$return.= '</div>';
					}

				$return.= '</div>';
				wp_nonce_field( 'samplepermalink', 'samplepermalinknonce', false );
			$return.= '</div>';
		}
	
		
		if ($post_type == 'page' || $post_type == 'post' || $CP_Cpt->is_supporting( $post_type, 'editor' )) {
			$return.= '<div id="postdivrich" class="postarea edit-form-section">';
				$return.= '<div class="cp-langs full" id="langs_post_title">';

					if (!isset($_COOKIE['langs_post_content'])) {
						$active = $languages[0]['code'];
					}
					else {
						$active = $_COOKIE['langs_post_content'];
					}

					foreach ($languages as $language) {
						$return.= '<span id="_post_content_'.$language['code'].'" class="option';
						if ($active == $language['code'])
							$return.= ' active';
						$return.= '">'.$language['name'].'</span>';
					}

					$return.= '<div class="langs_list">';

						foreach ($languages as $language) {

							if (isset($language['postmeta_suffix'])) {
								$suffix = $language['postmeta_suffix'];
							}
							else {
								$suffix = '';
							}
						
							$return.= '<div id="div_post_content'.'_'.$language['code'].'" class="';
							if ($active == $language['code'])
								$return.= ' active';
							$return.= '">';

								if ($language['default']) {
									$content = $post->post_content;
								}
								else {
									$content = get_post_meta( $post->ID, 'content'.$suffix, true );
								}
								ob_start();
								wp_editor( $content, 'content'.$suffix, array() );
								$return.= ob_get_clean();
							$return.= '</div>';
						}

					$return.= '</div>';
				$return.= '</div>';
			$return.= '</div>';
		}

		echo $return;
	}

	/**
	 * Start adding meta boxes
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function add_meta_boxes() {

		// if there are meta boxes
		if (is_array($this->mb)) {

			// for each meta box
			foreach ($this->mb AS $mb) {

				// if meta box is active
				if ($mb['settings']['active'])
					// create meta box groups
					$this->add_meta_box_group($mb);
			}
		}
	}

	/**
	 * Create meta box groups
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function add_meta_box_group($mb) {

		// add meta group
		add_meta_box(
			$mb['settings']['id'], 
			$mb['settings']['name'], 
			array($this, 'add_meta_box'), 
			$mb['settings']['post_type'], 
			$mb['settings']['context'],
			$mb['settings']['priority'], 
			$mb
		);
	}

	/**
	 * Create meta boxes
	 *
	 * @access type public
	 * @return type null no return
	 * @author Piotr Soluch
	 */
	public function add_meta_box($post, $meta_box) {
		$styles = '';
		
		$template = '';
		if (isset($meta_box['args']['settings']['template'])) {
			$template = $meta_box['args']['settings']['template'];
			echo '<input type="hidden" class="_cp_template_ _cp_template_'.$meta_box['args']['settings']['template'].'">';
		}

		// get data from the DB for current post id
		$values = get_post_custom($post->ID);

		foreach($values as $key => $value) {
			if(sizeof($value) == 1) {
				$values[$key] = $value[0];
			}
		}

		$fields = $meta_box['args']['fields'];
		wp_nonce_field(basename(__FILE__), 'add_meta_box_nonce');

		// for each field in a box
		foreach ($fields as $field) {
			echo $this->meta_box_field($field, $values);
		}
	}

	/**
	 * 
	 *
	 * @access type public
	 * @return type 
	 * @author Piotr Soluch
	 */
	private function meta_box_field($field, $values) {
		global $CP_Language, $CP_Field;

		// hook used to modify some elements of fileds
		$field = apply_filters('cp_mb_meta_box_field_before', $field);

		// if a group of fields is to be displayed
		if ($field['type'] == 'group') {
			$return = '';

			if (!isset($values[$field['id']])) {
				$values[$field['id']] = array();
			}
			$group_key = 0;
			if (isset($values[$field['id']])) {
				$group_values = ( maybe_unserialize($values[$field['id']]));
				//new dBug($group_values);

				$return.= '<div class="cp-mb-group-wrapper">';

				$values = array();
				foreach ($group_values AS $group_key => $group_value) { 
					$return.= '<fieldset class="cp-mb-group" id="'.$field['id'].'_'.$group_key.'">';

					if (isset($field['fields'])) {
						foreach ($field['fields'] as $key => $group_field) {
							if (!isset($group_value[$group_field['id']])) {
								$group_value[$group_field['id']] = '';
							}

							$value_key = $field['id'].'['.$group_key.']['.$group_field['id'].']';
							$values[$value_key] = $group_value[$group_field['id']];

							$group_field['group_name'] = $field['id'];
							$group_field['group_item'] = $group_key;

							$return.= $this->meta_box_field($group_field, $values);
						}
					}

					$return.= '<a href="#'.$field['id'].'_'.$group_key.'" class="cp-mb-remove-group">remove</a>';
					$return.= '</fieldset>';
				}
				$group_key++;

				$return.= '</div>';
			}

			
			$return.= '<a href="#'.$group_key.'" class="cp-mb-add-group button" id="group-'.$field['id'].'">add</a>';

			return $return;
		}
		
		$return = '';
		$return.= '<div class="cp_meta_box field_' . $field['type'] . '">';
		$return.= '<label for="'.$field['id'] .'">' . $field['name'];
			
		if (isset($field['attributes']['required']) && $field['attributes']['required'])
			$return.= ' *';

		$return.= '</label>';
		
		$languages = $CP_Language->get_languages();
		
		$return.= '<div class="cp-langs" id="langs_'.$field['id'].'">';

		if (isset($field['group_name'])) {
			$field['id'] = $field['group_name'].'['.$field['group_item'].']['.$field['id'].']';
		}

		if (isset($field['translation']) && $field['translation']) {
			$return.= $CP_Field->show_multilanguage_field($field, $field['id'], $field['id'], $values, $field['id']);
		} else {
			$value = '';
			if (isset($values[$field['id']])) {
				$value = $values[$field['id']];
			}
			
			$text = $CP_Field->show_field($field, $field['id'], $field['id'], $value);
			$return.= $this->meta_box_field_content($field, $text);
		}
		$return.= '</div>';
		
		$return.= '</div>';
		
		return $return;
	}

	function add_group() {
		
		//$group = $this->_add_group();		
		$key = $_POST['key'];
		$groupId = $_POST['group'];

		$group_field = $this->get_group($groupId);

		$group = $this->_add_group($key, $group_field);

		$response = array(
			'type' => 'success',
			'group' => $group
		);

		CP::ajax_response($response);
	}

	function _add_group($group_key, $field) {
		$return = '';
		$values = array();

		$return.= '<fieldset class="cp-mb-group" id="'.$field['id'].'_'.$group_key.'">';

		if (isset($field['fields'])) {
			foreach ($field['fields'] as $key => $group_field) {

				$group_field['group_name'] = $field['id'];
				$group_field['group_item'] = $group_key;

				$return.= $this->meta_box_field($group_field, $values);
			}
		}

		$return.= '<a href="#'.$field['id'].'_'.$group_key.'" class="cp-mb-remove-group">remove</a>';
		$return.= '</fieldset>';

		return $return;
	}

	function get_group($groupId) {
		foreach (CP::$config['mb'] as $key => $mb) {
			
			foreach ($mb['fields'] as $fkey => $field) {
				if ($field['type'] == 'group' && $field['id'] == $groupId) {
					return $field;
				}
			}
		}

		return null;
	}
	
	/**
	 * 
	 *
	 * @access type public
	 * @return type 
	 * @author Piotr Soluch
	 */
	public function meta_box_field_content($field, $text) {
		$return = '';
		
		if (isset($field['prefix']) && $field['prefix'])
			$return.= '<div class="prefix">' . $field['prefix'] . '</div>';
		
		$return.= $text;
		
		if (isset($field['suffix']) && $field['suffix'])
			$return.= '<div class="suffix">' . $field['suffix'] . '</div>';
		
		if (isset($field['description']) && $field['description'])
			$return.= '<div class="description">' . $field['description'] . '</div>';
		
		return $return;
	}

	public function add_language_title() {
		$title_mb = array(
			'settings' => array(
				'active' => true,
				'id' => 'post_title',
				'name' => 'post_title',
				'post_type' => 'member',
				'context' => 'normal', // normal | advanced | side
				'priority' => 'low' // high | core | default | low
			),
			'fields' => array(
				1 => array(
					'id' => 'former_member',
					'name' => 'Former member',
					'type' => 'checkbox',
					'description' => '',
					'values' => array(
						1 => '',
					)
				)
			)
		);

		$this->add_meta_box_group($title_mb);
	}

// -------------------- SAVING --------------------	
	
	public function save_main_box() {
		global $post, $post_id, $CP_Language;
		
		if ($CP_Language->get_language_count() < 2) 
			return;

		// for new posts
		if ($post === null)
			return;
		
		// get post type from post object
		$post_type = get_post_type_object($post->post_type);

		// Verify the nonce before proceeding.
		//if (!isset($_POST['add_meta_box_nonce']) || !wp_verify_nonce($_POST['add_meta_box_nonce'], basename(__FILE__)))
		//	return;

		// Check if the current user has permission to edit the post.
		if (!current_user_can($post_type->cap->edit_post, $post_id))
			return;
		
		$languages = $CP_Language->get_languages();

		//can't save during autosave, otherwise it saves blank values (there's a problem that meta box values are not send with POST during autosave. Probably fixable
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
			
		foreach ($languages as $language) {

			$this->save_meta_box_field('post_title'.$language['postmeta_suffix'], $post_id);
			$this->save_meta_box_field('content'.$language['postmeta_suffix'], $post_id);
		}
	}

	/**
	 * 
	 *
	 * @access type public
	 * @return type 
	 * @author Piotr Soluch
	 */
	public function save_meta_boxes($post_id, $post) {
		
		// if custom post type has fields
		if (is_array($this->mb)) {

			// for each field
			foreach ($this->mb as $meta_box) {

				// for the post type beeing saved
				if ($post['post_type'] == $meta_box['settings']['post_type']) {

					// Save all fields in meta box group
					$this->save_meta_box_fields($meta_box['fields']);
				}
			}
		}
	}

	/**
	 * Save meta box fields
	 *
	 * @access type public
	 * @return type none save the fields
	 * @author Piotr Soluch
	 */
	public function save_meta_box_fields($fields) {
		global $post, $post_id, $CP_Language;
		
		// for new posts
		if ($post === null)
			return;
		
		// get post type from post object
		$post_type = get_post_type_object($post->post_type);

		// Verify the nonce before proceeding.
		if (!isset($_POST['add_meta_box_nonce']) || !wp_verify_nonce($_POST['add_meta_box_nonce'], basename(__FILE__))) {
			return;
		}

		// Check if the current user has permission to edit the post.
		if (!current_user_can($post_type->cap->edit_post, $post_id)) {
			return;
		}
		
		$languages = $CP_Language->get_languages();

		// for each field in a box
		foreach ($fields as $field) {

			// Get the meta key.
			$meta_key = $field['id'];
			
			//can't save during autosave, otherwise it saves blank values (there's a problem that meta box values are not send with POST during autosave. Probably fixable
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}
			else if (isset($field['translation']) && $field['translation']) {
				
				foreach ($languages as $language) {

					$this->save_meta_box_field($field['id'].$language['postmeta_suffix'], $post_id);
				}
			}
			else {
				$this->save_meta_box_field($meta_key, $post_id);
			}
		}
	}
	
	/**
	 * 
	 * @param type $meta_key
	 * @param type $post_id
	 */
	public function save_meta_box_field($meta_key, $post_id) {
		
		// Get the posted data
		$new_meta_value = ( isset($_POST[$meta_key]) ? $_POST[$meta_key] : '' );
			//$new_meta_value['2'] = 'asd';


		// Get the meta value of the custom field key.
		$meta_value = get_post_meta($post_id, $meta_key, true);

		// If a new meta value was added and there was no previous value, add it.
		if ($new_meta_value && $meta_value == '') {
			add_post_meta($post_id, $meta_key, $new_meta_value, true);
		}

		// If the new meta value does not match the old value, update it.
		else if ($new_meta_value && $new_meta_value !== $meta_value) {
			update_post_meta($post_id, $meta_key, $new_meta_value);
		}

		// If there is no new meta value but an old value exists, delete it.
		elseif (!$new_meta_value && $meta_value) {
			//new dBug($new_meta_value);
			delete_post_meta($post_id, $meta_key, $meta_value);
		}
	}
	
// -------------------- OTHER --------------------
	
	/**
	 * 
	 *
	 * @access type public
	 * @return type 
	 * @author Piotr Soluch
	 */
	public function get_value($field, $value) {
		
		switch($field['type']) {
			case 'select':
				return $field['values'][$value];
				break;
			case 'post_link':
				$post_link = get_post( $value, ARRAY_A );
				return '<a href="'.get_permalink($post_link['ID']).'" target="_blank">'.$post_link['post_title'].'</a>';
				break;
			default:
				return $value;
				break;
		}
		
	}
	
	public function get_meta_box_fields() {
		$fields = array();
		
		foreach ($this->mb AS $mb) {
			if ($mb['settings']['active']) {
				if (!isset($fields[$mb['settings']['post_type']])) {
					$fields[$mb['settings']['post_type']] = array();
				}
				
				foreach ($mb['fields'] AS $field) {
					
					$fieldName = $field['id'];
					if (isset($field['translation']) && $field['translation']) {
						$fieldName = $field['id'].LANGUAGE_SUFFIX;
					}
					$fields[$mb['settings']['post_type']][] = $fieldName;
				}
			}
		}
		
		return $fields;
	}
}
