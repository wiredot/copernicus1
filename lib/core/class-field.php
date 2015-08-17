<?php

class CP_Field {

	/**
	 * 
	 */
	public function __construct() {
	}

	/**
	 * 
	 */
	public function show_field( $field, $field_id, $field_name, $value ) {

		$fields = array('attributes', 'options', 'values', 'labels', 'filetype', 'multiple', 'exclude', 'arguments');

		foreach ($fields as $f) {
			if (!isset($field[$f])) {
				$field[$f] = array();
			}
		}

		$field['value'] = $value;
		if (isset($field['group_name']) && isset($field['group_item']) ) {
			$field['field_id'] = $field['group_name'].'_'.$field['group_item'].'_'.$field_id.'';
			$field['field_name'] = $field['group_name'].'['.$field['group_item'].']['.$field_name.']';
		} else if (isset($field['group_name'])) {
			$field['field_id'] = $field['group_name'].'_'.$field_id.'';
			$field['field_name'] = $field['group_name'].'['.$field_name.']';
		} else {
			$field['field_id'] = $field_id;
			$field['field_name'] = $field_name;
		}

		switch ($field['type']) {
			
			// text field
			case 'text':
			case 'password':
			case 'email':
			case 'number':
			case 'range':
			case 'color':
			case 'url':
				return $this->_get_input($field);
				break;

			case 'date':
				return $this->_get_date($field);
				break;

			case 'textarea':
				return $this->_get_textarea($field);
				break;

			// wysiwyg editor field
			case 'editor':
				return $this->_get_editor($field);
				break;

			// checkboxes
			case 'checkbox':
				return $this->_get_checkbox($field);
				break;

			// radio 
			case 'radio':
				return $this->_get_radio($field);
				break;

			// selectbox
			case 'select':
				return $this->_get_select($field);
				break;

			// post link
			case 'post_link':
				return $this->_get_post_link($field);
				break;

			// user roles
			case 'user_role':
				return $this->_get_user_role($field);
				break;
				
			// users
			case 'user':
				return $this->_get_user($field);
				break;
			// taxonomy
			case 'taxonomy':
				return $this->_get_taxonomy($field);
				break;

			// file upload
			case 'upload':
				return $this->_get_upload($field);
				break;
		}

		// hook used to add additional field type
		$field_hook = apply_filters('cp_show_field_after', $field, $field_id, $field_name, $value);
		
		if ($field_hook) {
			return $field_hook;
		}

		return null;
	}

	/**
	 * 
	 */
	public function show_multilanguage_field( $field, $field_id, $field_name, $values, $value_key ) {
		global $CP_Language, $CP_Smarty;

		$languages = $CP_Language->get_languages();

		$fields = array();

		foreach ($languages as $lang) {
			$value = '';
			if (isset($values[$field_name.$lang['postmeta_suffix']])) {
				$value = $values[$field_name.$lang['postmeta_suffix']];
			}
			$fields[$lang['short_name']]['field'] = $this->show_field($field, $field_id.$lang['postmeta_suffix'], $field_name.$lang['postmeta_suffix'], $value);
		}

		if (isset($field['group_name']) && isset($field['group_item']) ) {
			$field['field_id'] = $field['group_name'].'_'.$field['group_item'].'_'.$field_id.'';
		} else if ( isset($field['group_name']) ) {
			$field['field_id'] = $field['group_name'].'_'.$field_id.'';
		} else {
			$field['field_id'] = $field_id;
		}

		$CP_Smarty->smarty->assign('languages', $languages);
		$CP_Smarty->smarty->assign('fields', $fields);
		$CP_Smarty->smarty->assign('field', $field);

		return $CP_Smarty->smarty->fetch('fields/multilanguage.html');
	}

	// -------------------- FIELDS --------------------	

	/**
	 * 
	 */
	private function _get_input($field) {
		global $CP_Smarty;
		$CP_Smarty->smarty->assign('field', $field);

		return $CP_Smarty->smarty->fetch('fields/input.html');
	}

	/**
	 * 
	 */
	private function _get_date($field) {
		global $CP_Smarty;

		if (isset($field['attributes']['class'])) {
			$field['attributes']['class'] = $field['attributes']['class'] . ' cp_datepicker';
		} else {
			$field['attributes']['class'] = 'cp_datepicker';
		}
		
		$CP_Smarty->smarty->assign('field', $field);

		return $CP_Smarty->smarty->fetch('fields/date.html');
	}

	/**
	 * 
	 */
	private function _get_textarea($field) {
		global $CP_Smarty;
		$CP_Smarty->smarty->assign('field', $field);

		return $CP_Smarty->smarty->fetch('fields/textarea.html');
	}

	/**
	 * 
	 */
	private function _get_editor($field) {
		ob_start();
		$field['attributes']['textarea_name'] = $field['field_name'];
		wp_editor($field['value'], $field['field_id'], $field['attributes']);
		return ob_get_clean();
	}

	/**
	 * 
	 */
	private function _get_select($field) {
		global $CP_Smarty;

		if ($field['value']) {
			$field['value'] = maybe_unserialize($field['value']);
		}

		if (isset($field['multiple']) && $field['multiple']) {
			if ( ! is_array($field['value'])) {
				$field['value'] = array();
			}
		}

		$CP_Smarty->smarty->assign('field', $field);

		return $CP_Smarty->smarty->fetch('fields/select.html');
	}

	/**
	 * 
	 */
	private function _get_checkbox($field) {
		global $CP_Smarty;

		if ($field['value']) {
			$field['value'] = maybe_unserialize($field['value']);
		}

		if (count($field['options'])) {
			if ( ! is_array($field['value'])) {
				$field['value'] = array();
			}
		}

		$CP_Smarty->smarty->assign('field', $field);

		return $CP_Smarty->smarty->fetch('fields/checkbox.html');
	}

	/**
	 * 
	 */
	private function _get_radio($field) {
		global $CP_Smarty;

		$CP_Smarty->smarty->assign('field', $field);

		return $CP_Smarty->smarty->fetch('fields/radio.html');
	}

	/**
	 * 
	 */
	private function _get_post_link($field) {
		$default_arguments = array(
			'posts_per_page' => -1,
			'post_type' => 'page'
		);

		$arguments = array_merge($default_arguments, $field['arguments']);

		$loop_links = new WP_Query( $arguments );

		$options = array();

		$posts = $loop_links->posts;
		if ($posts) {
			foreach ($posts as $post) {
				$options[$post->ID] = $post->post_title;
			}
		}

		$field['options'] = $options;

		if (isset($field['multiple']) && $field['multiple']) {
			return $this->_get_checkbox($field);
		} else {
			return $this->_get_select($field);
		}
	}

	/**
	 * 
	 */
	private function _get_user_role($field) {
		$roles = new WP_Roles();

		$role_names = $roles->role_names;

		$options = array();
		if (is_array($role_names)) {
			foreach ($role_names AS $field_key => $field_value) {
				$options[$field_key] = $field_value;
			}
		}

		$field['options'] = $options;

		if (isset($field['multiple']) && $field['multiple']) {
			return $this->_get_checkbox($field);
		} else {
			return $this->_get_radio($field);
		}
	}

	/**
	 * 
	 */
	private function _get_user($field) {
		$users = get_users($field['arguments']);

		$options = array();
		if (is_array($users)) {
			foreach ($users AS $user) {
				$options[$user->ID] = get_user_meta( $user->ID, 'first_name', true ).' '.get_user_meta( $user->ID, 'last_name', true ).' ('.$user->data->user_login.')';
			}
		}

		$field['options'] = $options;

		if (isset($field['multiple']) && $field['multiple']) {
			return $this->_get_checkbox($field);
		} else {
			return $this->_get_select($field);
		}
	}

	/**
	 * 
	 */
	private function _get_taxonomy($field) {
		$terms = get_terms($field['taxonomy'], $field['arguments']);

		$options = array();
		if (is_array($terms)) {
			foreach ($terms AS $term) {
				$options[$term->term_id] = $term->name;
			}
		}

		$field['options'] = $options;

		if (isset($field['multiple']) && $field['multiple']) {
			return $this->_get_checkbox($field);
		} else {
			return $this->_get_select($field);
		}
	}

	/**
	 * 
	 */
	private function _get_upload($field) {
		global $CP_Imageold, $CP_Smarty;;

		if ($field['multiple']) {
			$field['go_function'] = 'media_upload_multiple';
		}
		else {
			$field['go_function'] = 'media_upload_single';
		}
		
		if ($field['value']) {
			$field['value'] = maybe_unserialize($field['value']);
		}
		else {
			$field['value'] = array();
		}

		$field['disabled'] = '';
		
		if (!$field['multiple'] && count($field['value']) == 1) {
			$field['disabled'] = ' disabled="disabled"';
		}

		$field['files'] = array();

		foreach ($field['value'] as $attachment) {
			
			$attachment_data = get_post($attachment);
		
			$params = array(
				'id' => $attachment,
				'w' => 200,
				'echo' => 0
			);

			$image = $CP_Imageold->image($params);
		
			$im = wp_get_attachment_metadata($attachment, 1);
			
			$upload = '<div id="file-'.$attachment.'">';
			
			if ($field['filetype'] == 'image') {
				$file = $image;
			}
			else {
				$image = wp_get_attachment_image_src( $attachment, 100, true);
				if ($image[1] > 100) {
					$image[1] = 100;
				}
				$file = '<img src="'.$image[0].'" width="'.$image[1].'" >';
				$file.= '<span>'.basename($attachment_data->guid).'</span>';
			}

			$field['files'][] = array(
				'attachment' => $attachment,
				'file' => $file,
			);
		}

		$CP_Smarty->smarty->assign('field', $field);

		return $CP_Smarty->smarty->fetch('fields/upload.html');
	}

	// -------------------- PUBLIC FIELDS --------------------	

	/**
	 * 
	 */
	public function get_select($value, $field_id, $field_name, $options) {
		$field = array(
			'type' => 'select',
			'options' => $options
		);

		return $this->show_field( $field, $field_id, $field_name, $value );
	}

// end class
}