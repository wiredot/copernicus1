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

		// dynamic group adding
		add_action('wp_ajax_cp_mb_add_group', array($this,'add_group'));

		// adding language translation for titles
		add_action('edit_form_after_title', array($this, 'add_main_box'));

		// saving the main meta box
		add_action('pre_post_update', array($this, 'save_main_box'), 10, 2);
		
		// if any meta boxes are configured
		if (isset (CP::$config['mb']) && is_array(CP::$config['mb'])) {
			
			// get meta box configuration
			$this->mb = CP::$config['mb'];
				
			// add meta boxes
			add_action('admin_init', array($this, 'add_meta_boxes'));

			// save meta boxes
			add_action('pre_post_update', array($this, 'save_meta_boxes'), 10, 2);
		}
	}

	// -------------------- MAIN BOX --------------------	

	/**
	 * 
	 */
	public function add_main_box() {
		global $post, $CP_Language, $CP_Cpt, $CP_Spt;

		if ($CP_Language->get_language_count() < 2) {
			return;
		}
	
		$post_type = $post->post_type;
		$post_type_object = get_post_type_object($post_type);

		$languages = $CP_Language->get_languages();

		$return = '';

		if ( ( ($post_type == 'page' && $CP_Spt->is_supported('page', 'title') && $CP_Spt->is_translated('page', 'title')) || ($post_type == 'post' && $CP_Spt->is_supported('post', 'title') && $CP_Spt->is_translated('post', 'title')) || $CP_Cpt->is_supporting( $post_type, 'title' ) ) && ($this->is_to_translate($post_type, 'title')) ) {
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
	
		
		if ( (($post_type == 'page' && $CP_Spt->is_supported('page', 'editor')) || ($post_type == 'post' && $CP_Spt->is_supported('post', 'editor')) || $CP_Cpt->is_supporting( $post_type, 'editor' ))  && ($this->is_to_translate($post_type, 'editor')) ) {
			
			if ( ! ( $post_type == 'page' || $post_type == 'post' || $CP_Cpt->is_supporting( $post_type, 'title' ) ) || ! ($this->is_to_translate($post_type, 'title')) ) {
				$return.= '<br>';
			}

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

	// -------------------- META BOXES --------------------	

	/**
	 * Start adding meta boxes
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function add_meta_boxes() {
		// for each meta box
		foreach ($this->mb AS $key => $mb) {

			// if meta box is active
			if ($mb['active']) {
				// create meta box groups

				$mb['id'] = $key;
				$this->add_meta_box($mb);
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
	public function add_meta_box($mb) {
		if (is_array($mb['post_type'])) {
			foreach ($mb['post_type'] as $post_type) {
				// add meta group
				add_meta_box(
					$mb['id'], 
					$mb['name'], 
					array($this, 'add_meta_box_content'), 
					$post_type, 
					$mb['context'],
					$mb['priority'], 
					$mb
				);
			}
		} else {
			// add meta group
			add_meta_box(
				$mb['id'], 
				$mb['name'], 
				array($this, 'add_meta_box_content'), 
				$mb['post_type'], 
				$mb['context'],
				$mb['priority'], 
				$mb
			);
		}
	}

	/**
	 * Create meta boxes
	 *
	 * @access type public
	 * @return type null no return
	 * @author Piotr Soluch
	 */
	public function add_meta_box_content($post, $meta_box) {
		$styles = '';
		$template = '';

		if (isset($meta_box['args']['template'])) {
			$template = $meta_box['args']['template'];

			echo '<input type="hidden" class="_cp_template_';
			
			if (is_array($meta_box['args']['template'])) {
				foreach ($meta_box['args']['template'] as $temp) {
					echo ' _cp_template_'.$temp;
				}
			} else {
				echo ' _cp_template_'.$meta_box['args']['template'];
			}
			echo '">';
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
		foreach ($fields as $key => $field) {
			$field['id'] = $key;
			if ($field['type'] == 'group') {
				echo $this->meta_box_group($field, $values);
			} else {
				echo $this->meta_box_field($field, $values);
			}
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
		global $CP_Language, $CP_Field, $CP_Smarty;
		
		// hook used to modify some elements of fileds
		$field = apply_filters('cp_mb_meta_box_field_before', $field);

		if (isset($field['translation']) && $field['translation']) {
			$field['field'] = $CP_Field->show_multilanguage_field($field, $field['id'], $field['id'], $values, $field['id']);
		} else {
			$value = '';
			if (isset($values[$field['id']])) {
				$value = $values[$field['id']];
			}
			
			$field['field'] = $CP_Field->show_field($field, $field['id'], $field['id'], $value);
		}

		if (isset($field['group_name'])) {
			$field['field_id'] = $field['group_name'].'_'.$field['group_item'].'_'.$field['id'].'';

		} else {
			$field['field_id'] = $field['id'];
		}

		$CP_Smarty->smarty->assign('field', $field);
		return $CP_Smarty->smarty->fetch('mb/row.html');

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

	// -------------------- GROUPS --------------------	

	/**
	 * 
	 */
	private function meta_box_group($field, $values) {
		global $CP_Language, $CP_Field, $CP_Smarty;

		$return = '';
		$group_key = 0;

		if (!isset($values[$field['id']])) {
			$values[$field['id']] = array();
		}
		
		$group_values = ( maybe_unserialize($values[$field['id']]));

		$groups = '';
		if (isset($group_values)) {
			foreach ($group_values AS $group_key => $group_value) {

				if (isset($field['fields'])) {
					
					$fields = '';
					foreach ($field['fields'] as $key => $group_field) {

						$group_field['group_name'] = $field['id'];
						$group_field['group_item'] = $group_key;
						$group_field['group_field'] = $key;
						$group_field['id'] = $key;
						
						$fields.= $this->meta_box_field($group_field, $group_values[$group_key]);
					}
				}
				$CP_Smarty->smarty->assign('group_key', $group_key);
				$CP_Smarty->smarty->assign('fields', $fields);
				$groups.= $CP_Smarty->smarty->fetch('mb/group.html');
			}
		}

		$CP_Smarty->smarty->assign('groups', $groups);
		$return = $CP_Smarty->smarty->fetch('mb/groups.html');
		$return.= '<a href="#add" class="cp-mb-add-group button" id="group-'.$field['id'].'">add</a>';
		
		return $return;
	}

	/**
	 * 
	 */
	public function add_group() {
		global $CP_Smarty;
		
		$key = $_POST['key'];
		$group_id = $_POST['group'];

		$group_field = $this->get_group($group_id);

		$return = '';

		if (isset($group_field['fields'])) {
			foreach ($group_field['fields'] as $k => $group_field) {

				$group_field['group_name'] = $group_id;
				$group_field['group_item'] = $key;
				$group_field['id'] = $k;

				$return.= $this->meta_box_field( $group_field, array() );
			}
		}

		$CP_Smarty->smarty->assign('fields', $return);
		$CP_Smarty->smarty->assign('group_key', $key);
		$group = $CP_Smarty->smarty->fetch('mb/group.html');

		$response = array(
			'type' => 'success',
			'group' => $group
		);

		CP::ajax_response($response);
	}

	/**
	 * 
	 */
	public function get_group($group_id) {
		foreach (CP::$config['mb'] as $key => $mb) {
			
			foreach ($mb['fields'] as $fkey => $field) {
				$field['id'] = $fkey;
				if ($field['type'] == 'group' && $field['id'] == $group_id) {
					return $field;
				}
			}
		}

		return null;
	}

	// -------------------- SAVING --------------------	
	
	/**
	 * 
	 */
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

				if (is_array($meta_box['post_type'])) {
					foreach ($meta_box['post_type'] as $post_type) {
						// for the post type beeing saved
						if ($post['post_type'] == $post_type) {

							// Save all fields in meta box group
							$this->save_meta_box_fields($meta_box['fields']);
						}
					}
				} else {
					// for the post type beeing saved
					if ($post['post_type'] == $meta_box['post_type']) {

						// Save all fields in meta box group
						$this->save_meta_box_fields($meta_box['fields']);
					}
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
		if ($post === null) {
			return;
		}
		
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
		foreach ($fields as $k => $field) {

			if ($field['type'] == 'group') {
				foreach ($field['fields'] as $f => $fvalue) {
					if ($fvalue['type'] == 'upload' && isset($_POST[$k])) {
						foreach ($_POST[$k] AS $kkey => $postvalues) {
							foreach ($postvalues as $postkey => $postvalue) {
								if ($postkey == $f) {

									$postvaluearray = array();

									foreach ($postvalue['id'] as $postvaluekey => $postvalueid) {
										$title = '';
										$caption = '';
										$alt = '';

										$postvaluearray[] = $postvalueid;
										
										if (isset($postvalue['title'][$postvaluekey])) {
											$title = $postvalue['title'][$postvaluekey];
										}
										
										if (isset($postvalue['caption'][$postvaluekey])) {
											$caption = $postvalue['caption'][$postvaluekey];
										}

										if (isset($postvalue['alt'][$postvaluekey])) {
											$alt = $postvalue['alt'][$postvaluekey];
										}

										$this->update_image_data($postvalueid, $title, $caption, $alt);
									}

									$_POST[$k][$kkey][$f] = $postvaluearray;
								}
							}
						}
					}
				}
			}

			$field['id'] = $k;

			// Get the meta key.
			$meta_key = $field['id'];
			
			//can't save during autosave, otherwise it saves blank values (there's a problem that meta box values are not send with POST during autosave. Probably fixable
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			} else if (isset($field['translation']) && $field['translation']) {
				
				foreach ($languages as $language) {

					if ($field['type'] == 'upload') {
						$this->save_meta_box_field_upload($field['id'].$language['postmeta_suffix'], $post_id);
					} else {
						$this->save_meta_box_field($field['id'].$language['postmeta_suffix'], $post_id);
					}
				}
			}
			else {
				if ($field['type'] == 'upload') {
					$this->save_meta_box_field_upload($meta_key, $post_id);
				} else {
					$this->save_meta_box_field($meta_key, $post_id);
				}
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

		// If the new meta value does not match the old value, update it.
		if ($new_meta_value) {
			update_post_meta($post_id, $meta_key, $new_meta_value);
		} else {
			delete_post_meta($post_id, $meta_key);
		}
	}

	public function save_meta_box_field_upload($meta_key, $post_id) {
		
		// Get the posted data
		$new_meta_value = ( isset($_POST[$meta_key]) ? $_POST[$meta_key] : '' );
		if ($new_meta_value) {
			update_post_meta($post_id, $meta_key, $new_meta_value['id']);
		} else {
			delete_post_meta($post_id, $meta_key);
		}

		if (isset($new_meta_value['id'])) {
			foreach ($new_meta_value['id'] as $key => $id) {
				$title = '';
				$caption = '';
				$alt = '';
				
				if (isset($new_meta_value['title'][$key])) {
					$title = $new_meta_value['title'][$key];
				}
				
				if (isset($new_meta_value['caption'][$key])) {
					$caption = $new_meta_value['caption'][$key];
				}

				if (isset($new_meta_value['alt'][$key])) {
					$alt = $new_meta_value['alt'][$key];
				}

				$this->update_image_data($id, $title, $caption, $alt);
			}
		}
	}

	public function update_image_data($id, $title, $caption, $alt = '') {
		global $wpdb;

		$wpdb->update(
			$wpdb->posts,
			array(
				'post_title' => $title,
				'post_excerpt' => $caption
			),
			array(
				'ID' => $id
			)
		);

		if ($alt) {
			update_post_meta( $id, 'alt', $alt );
		} else {
			delete_post_meta( $id, 'alt' );
		}
	}
	
	// -------------------- OTHER --------------------

	/**
	 * 
	 */
	public function get_value($field, $value) {
		
		switch($field['type']) {
			case 'select':
				//return $field['options'][$value];
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
	
	/**
	 * 
	 */
	public function get_meta_box_fields() {
		$fields = array();
		
		foreach ($this->mb AS $mb) {
			if ($mb['active']) {
				if (!isset($fields[$mb['post_type']])) {
					$fields[$mb['post_type']] = array();
				}
				
				foreach ($mb['fields'] AS $field) {
					
					$fieldName = $field['id'];
					if (isset($field['translation']) && $field['translation']) {
						$fieldName = $field['id'].LANGUAGE_SUFFIX;
					}
					$fields[$mb['post_type']][] = $fieldName;
				}
			}
		}
		
		return $fields;
	}

	public function get_meta_key_fields($mb_search) {
		$fields = array();
		
		foreach ($this->mb AS $mb) {
			
			if($mb['post_type'] == $mb_search) {
				foreach ($mb['fields'] AS $key => $field ) {
					$fields[$mb['post_type']][] = $key;
				}		
			}
		}
		
		return $fields;
	}

	public function is_to_translate($post_type, $field) {
		if (isset(CP::$config['cpt'][$post_type]['translate'][$field])) {
			if (CP::$config['cpt'][$post_type]['translate'][$field]) {
				return 1;
			} else {
				return 0;
			}
		}

		return 1;
	}
}
