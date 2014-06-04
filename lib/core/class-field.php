<?php

class CP_Field {
	
	public function __construct() {
	}

	function show_field( $field, $field_id, $field_name, $value ) {

		$fields = array('attributes', 'options', 'values', 'labels', 'filetype', 'multiple', 'exclude', 'arguments');
		foreach ($fields as $f) {
			if (!isset($field[$f])) {
				$field[$f] = array();
			}
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
				return $this->get_input($field['type'], $value, $field_id, $field_name, $field['attributes']);
				break;

			// date field
			case 'date':
				return $this->get_date($value, $field_id, $field_name, $field['options'], $field['attributes']);
				break;
			
			case 'textarea':
				return $this->get_textarea($value, $field_id, $field_name, $field['attributes']);
				break;

			// wysiwyg editor field
			case 'editor':
				return $this->get_editor($value, $field_id);
				break;

			// checkboxes
			case 'checkbox':
				return $this->get_checkbox($value, $field_id, $field_name, $field['values'], $field['attributes']);
				break;

			// radio 
			case 'radio':
				return $this->get_radio($value, $field_id, $field_name, $field['values'], $field['attributes']);
				break;

			// selectbox
			case 'select':
				return $this->get_select($value, $field_id, $field_name, $field['values'], $field['attributes']);
				break;

			// multi select
			case 'multiselect':
				return $this->get_multiselect($value, $field_id, $field_name, $field['values'], $field['attributes']);
				break;

			// post link
			case 'post_link':
				return $this->get_post_link($value, $field_id, $field_name, $field['arguments'], $field['attributes']);
				break;

			// post links
			case 'post_links':
				return $this->get_post_links($value, $field_id, $field_name, $field['arguments'], $field['attributes']);
				break;

			// user roles
			case 'user_role':
			case 'user_roles':
				return $this->get_user_roles($value, $field_id, $field_name, $field['exclude'], $field['attributes']);
				break;
				
			// users
			case 'users':
				return $this->get_users($value, $field_id, $field_name, $field['exclude'], $field['attributes']);
				break;
			// taxonomy
			case 'taxonomy':
				return $this->get_taxonomy($value, $field_id, $field_name, $field['options'], $field['arguments'], $field['attributes']);
				break;

			// file upload
			case 'upload':
				return $this->get_upload($value, $field_id, $field_name, $field['multiple'], $field['filetype'], $field['labels'], $field['attributes']);
				break;
		}

		// hook used to add additional field type
		$field_hook = apply_filters('cp_show_field_after', $field, $field_id, $field_name, $value);
		
		if ($field_hook) {
			return $field_hook;
		}

		return null;
	}

	function show_multilanguage_field( $field, $field_id, $field_name, $values, $value_key ) {
		global $CP_Language;

		$languages = $CP_Language->get_languages();

		if (count($languages) < 2) {
			$value = $values[$value_key];
			return $this->show_field( $field, $field_id, $field_name, $value );
		}

		$return = '';
		$return.= '<div class="cp-langs" id="langs_'.$field_id.'">';

		if (!isset($_COOKIE['langs_'.$field_id])) {
			$active = $languages[0]['code'];
		}
		else {
			$active = $_COOKIE['langs_'.$field_id];
		}

		foreach ($languages as $language) {
			$return.= '<span id="_'.$field_id.'_'.$language['code'].'" class="option';
			if ($active == $language['code'])
				$return.= ' active';
			$return.= '">'.$language['name'].'</span>';
		}

		$return.= '<div class="langs_list">';
		foreach ($languages as $language) {
				
			$return.= '<div id="div_'.$field_id.'_'.$language['code'].'"';
			if ($active == $language['code'])
				$return.= ' class="active"';
			$return.= '>';
			
			$suffix = '';
			if (isset($language['postmeta_suffix'])) {
				$suffix = $language['postmeta_suffix'];
			}
			$value = '';
			if (isset($values[$value_key.$suffix])) {
				$value = $values[$value_key.$suffix];
			}
			if (substr($field_name, -1) == "]") {
				$new_field_name = rtrim($field_name, "]").$suffix."]";
			} else {
				$new_field_name = $field_name.$suffix;
			}
			$return.= $this->show_field($field, $field_id.$suffix, $new_field_name, $value);
			$return.= '</div>';
		}
		$return.= '</div>';
		$return.= '</div>';
		return $return;
	}

	public function get_input($type, $value, $field_id, $field_name, $attributes = array()) {

		$default_attributes = array(
			'size' => 30
		);

		$text_field = '<input type="'.$type.'" name="'.$field_name.'" id="'.$field_id.'" value="'.$value.'"';
		$text_field.= $this->get_field_attributes($attributes, $default_attributes);
		$text_field.= '>';
		return $text_field;
	}

	public function get_date($value, $field_id, $field_name, $options = array(), $attributes = array()) {
		$date =  '<input type="date" name="' . $field_name . '" id="' . $field_id . '" value="' . $value . '"';
		if (isset($attributes))
			$date.= $this->get_field_attributes($attributes);

		$date.= '/>';

		return $date;
	}

	public function get_textarea($value, $field_id, $field_name, $attributes = array()) {

		$default_attributes = array(
			'rows' => 6,
			'cols' => 60
		);

		$textarea = '<textarea name="'.$field_name.'" id="'.$field_id.'"';
		$textarea.= $this->get_field_attributes($attributes, $default_attributes);
		$textarea.= '>';
		$textarea.= $value;
		$textarea.= '</textarea>';
		return $textarea;
	}

	public function get_editor($value, $field_id, $attributes = array()) {
		ob_start();
		wp_editor($value, $field_id, $attributes);
		return ob_get_clean();
	}

	public function get_select($value, $field_id, $field_name, $options, $attributes = array()) {
		$select = '<select id="' . $field_id . '" name="' . $field_name . '"';
		$select.= $this->get_field_attributes($attributes);
		$select.= '>';

		if (is_array($options)) {
			foreach ($options AS $field_key => $field_option) {
				$select.= '<option value="' . $field_key . '" ';

				if ($value == $field_key)
					$select.= 'selected="selected" ';
				$select.= '> ';
				$select.= $field_option;
				$select.= '</option>';
			}
		}
		$select.= '</select>';

		return $select;
	}

	public function get_multiselect($values, $field_id, $field_name, $options, $attributes = array()) {
		$default_attributes = array(
			'size' => 1,
			'style' => 'height: auto !important;'
		);

		if ($values)
			$values = maybe_unserialize($values);
		else
			$values = array();

		$select = '<select id="' . $field_id . '" name="' . $field_name . '[]"';
		$select.= $this->get_field_attributes($attributes, $default_attributes);
		$select.= ' multiple="multiple">';

		if (is_array($options)) {
			foreach ($options AS $field_key => $field_option) {
				$select.= '<option value="' . $field_key . '" ';

				if (in_array($field_key, $values))
					$select.= 'selected="selected" ';
				$select.= '> ';
				$select.= $field_option;
				$select.= '</option>';
			}
		}
		$select.= '</select>';

		return $select;
	}

	public function get_checkbox($values, $field_id, $field_name, $options, $attributes = array()) {
		if ($values) {
			$values = maybe_unserialize($values);
		} else {
			$values = array();
		}

		$checkbox = '<ul>';
		if (is_array($options) && count($options)) {
			foreach ($options AS $field_key => $field_value) {
				$checkbox.= '<li>';
				$checkbox.= '<input type="checkbox" name="' . $field_name . '[]" id="' . $field_id . '_' . $field_key . '" value="' . $field_key . '" ';
				if (in_array($field_key, $values)) {
					$checkbox.= 'checked="checked" ';
				}
				
				$checkbox.= $this->get_field_attributes($attributes);
				$checkbox.= ' /> ';
				$checkbox.= '<label for="' . $field_id . '_' . $field_key . '">' . $field_value . '</label>';
				$checkbox.= '</li>';
			}
		}
		else {
			$checkbox.= '<li>';
			$checkbox.= '<input type="checkbox" name="' . $field_name . '" id="' . $field_id . '" value="1" ';
			if ($values)
				$checkbox.= 'checked="checked" ';

			if (!isset($attributes))
				$attributes = array();
			
			$checkbox.= $this->get_field_attributes($attributes);
			$checkbox.= ' /> ';
			$checkbox.= '</li>';
		}
		$checkbox.= '</ul>';

		return $checkbox;
	}

	public function get_radio($value, $field_id, $field_name, $options, $attributes = array()) {

		$checkbox = '<ul>';
		if (is_array($options)) {
			foreach ($options AS $field_key => $field_value) {
				$checkbox.= '<li>';
				$checkbox.= '<input type="radio" name="' . $field_name . '" id="' . $field_id . '_' . $field_key . '" value="' . $field_key . '" ';
				if ($field_key == $value)
					$checkbox.= 'checked="checked" ';

				if (!isset($attributes))
					$attributes = array();
				
				$checkbox.= $this->get_field_attributes($attributes);
				$checkbox.= ' /> ';
				$checkbox.= '<label for="' . $field_id . '_' . $field_key . '">' . $field_value . '</label>';
				$checkbox.= '</li>';
			}
		}
		$checkbox.= '</ul>';

		return $checkbox;
	}

	public function get_post_link($value, $field_id, $field_name, $arguments, $attributes = array()) {
		$default_arguments = array(
			'posts_per_page' => -1,
			'post_type' => 'page'
		);

		$arguments = array_merge($default_arguments, $arguments);


		$post_link = '<select id="' . $field_id . '" name="' . $field_name .'"';
		$post_link.= $this->get_field_attributes($attributes);
		$post_link.= '>';
		$post_link.= '<option value="0"> -- select -- </option>';

		$loop_links = new WP_Query( $arguments );

		$all_links = array();

		$posts = $loop_links->posts;

		if ($posts) {
			foreach ($posts as $post) {
				if ($value == $post->ID)
					$post->selected = 1;
				$all_links[$post->post_parent][$post->ID] = $post;
			}

			$post_link.= $this->get_links($all_links);
		}

		$post_link.= '</select>';

		return $post_link;
	}

	public function get_post_links($values, $field_id, $field_name, $arguments, $attributes = array()) {
		if ($values) {
			$values = maybe_unserialize($values);
		} else {
			$values = array();
		}

		$default_arguments = array(
			'posts_per_page' => -1,
			'post_type' => 'page'
		);

		$arguments = array_merge($default_arguments, $arguments);

		

		$post_link = '<select id="' . $field_id . '" name="' . $field_name .'"';
		$post_link.= $this->get_field_attributes($attributes);
		$post_link.= '>';
		$post_link.= '<option value="0"> -- select -- </option>';

		$loop_links = new WP_Query( $arguments );

		$all_links = array();

		$posts = $loop_links->posts;

		if ($posts) {
			foreach ($posts as $post) {
				//if ($value == $post->ID)
				//	$post->selected = 1;
				//$all_links[$post->post_parent][$post->ID] = $post;
			}

			//$post_link.= $this->get_links($all_links);
		}

		$post_link.= '</select>';

		$checkbox = '';

		if ($posts) {
			$checkbox.= '<ul>';
			foreach ($posts AS $field_key => $field_value) {
				$checkbox.= '<li>';
				$checkbox.= '<input type="checkbox" name="' . $field_name . '[]" id="' . $field_id . '_' . $field_key . '" value="' . $field_value->ID . '" ';
				if (in_array($field_value->ID, $values)) {
					$checkbox.= 'checked="checked" ';
				}
				
				$checkbox.= $this->get_field_attributes($attributes);
				$checkbox.= ' /> ';
				$checkbox.= '<label for="' . $field_id . '_' . $field_key . '">' . $field_value->post_title . '</label>';
				$checkbox.= '</li>';
			}
			
			$checkbox.= '</ul>';
		}

		return $checkbox;
	}

	public function get_user_roles($values, $field_id, $field_name, $exclude = array(), $attributes = array()) {
		if ($values)
			$values = maybe_unserialize($values);
		else
			$values = array();

		$roles = new WP_Roles();

		$role_names = $roles->role_names;
		
		if (is_array($exclude)) {
			$excludes = $exclude;
		} else {
			$excludes = explode(',', str_replace(' ', '', $exclude));	
		}

		$user_roles = '<ul>';
		if (is_array($role_names)) {
			foreach ($role_names AS $field_key => $field_value) {
				if (!in_array($field_key, $excludes)) {
					$user_roles.= '<li>';
					$user_roles.= '<input type="checkbox" name="' . $field_name . '[]" id="' . $field_id . '_' . $field_key . '" value="' . $field_key . '" ';
					if (in_array($field_key, $values))
						$user_roles.= 'checked="checked" ';

					if (!isset($attributes))
						$attributes = array();
					
					$user_roles.= $this->get_field_attributes($attributes);
					$user_roles.= ' /> ';
					$user_roles.= '<label for="' . $field_id . '_' . $field_key . '">' . $field_value . '</label>';
					$user_roles.= '</li>';
				}
			}
		}
		$user_roles.= '</ul>';

		return $user_roles;
	}

	public function get_users($values, $field_id, $field_name, $exclude = array(), $attributes = array()) {
		if ($values)
			$values = maybe_unserialize($values);
		else
			$values = array();

		$users = get_users();

		$users_output = '<ul>';
		if (is_array($users)) {
			foreach ($users AS $field_key => $field_value) {
					$users_output.= '<li>';
					$users_output.= '<input type="checkbox" name="' . $field_name . '[]" id="' . $field_id . '_' . $field_value->data->ID . '" value="' . $field_value->data->ID . '" ';
					if (in_array($field_value->data->ID, $values))
						$users_output.= 'checked="checked" ';

					if (!isset($attributes))
						$attributes = array();
					
					$users_output.= $this->get_field_attributes($attributes);
					$users_output.= ' /> ';
					$users_output.= '<label for="' . $field_id . '_' . $field_value->data->ID . '">' . $field_value->data->display_name . '</label>';
					$users_output.= '</li>';
			}
		}
		$users_output.= '</ul>';

		return $users_output;
	}

	public function get_taxonomy($value, $field_id, $field_name, $options = array(), $arguments = array(), $attributes = array()) {
		$terms = get_terms($options['taxonomy'], $arguments);

		$select = '<select id="' . $field_id . '" name="' . $field_name . '"';
		$select.= $this->get_field_attributes($attributes);
		$select.= '>';
		$select.= '<option value="0"> -- select -- </option>';

		if (is_array($terms)) {
			foreach ($terms AS $field_key => $field_option) {
				$select.= '<option value="' . $field_option->term_id . '" ';

				if ($value == $field_option->term_id)
					$select.= 'selected="selected" ';
				$select.= '> ';
				$select.= $field_option->name;
				$select.= '</option>';
			}
		}
		$select.= '</select>';

		return $select;
	}

	public function get_upload($values, $field_id, $field_name, $multiple = false, $filetype = 'image', $labels = array(), $attributes = array()) {
		global $CP_Image;

		if ($multiple) {
			$go_function = 'media_upload_multiple';
		}
		else {
			$go_function = 'media_upload_single';
		}
		
		if ($values) {
			$values = maybe_unserialize($values);
		}
		else {
			$values = array();
		}
		
		$disabled = '';
		
		if (!$multiple && count($values) == 1) {
			$disabled = ' disabled="disabled"';
		}
		
		$upload = '<div id="container_'.$field_id.'" class="cp-files">';
		$upload.= '<a href="javascript:'.$go_function.'(\''.$filetype.'\',\''.$field_id.'\',\''.$field_name.'\',\''.$labels['title_window'].'\', \''.$labels['button_window'].'\');" '.$disabled.' class="cp-open-media button button-primary" id="button_'.$field_id.'" title="' . esc_attr__( 'Add Images', 'tgm-nmp' ) . '">' . __( $labels['button'], 'tgm-nmp' ) . '</a>';
		
		foreach ($values as $attachment) {
			
			$attachment_data = get_post($attachment);
		
			$params = array(
				'id' => $attachment,
				'w' => 200,
				'echo' => 0
			);

			$image = $CP_Image->image($params);
		
			$im = wp_get_attachment_metadata($attachment, 1);
			
			$upload.= '<div id="file-'.$attachment.'">';
			
			if ($filetype == 'image') {
				$upload.= $image;
			}
			else {
				$image = wp_get_attachment_image_src( $attachment, 100, true);
				if ($image[1] > 100) {
					$image[1] = 100;
				}
				$upload.= '<img src="'.$image[0].'" width="'.$image[1].'" >';
				$upload.= '<span>'.basename($attachment_data->guid).'</span>';
			}
			
			$upload.= '<input type="hidden" name="'.$field_name.'[]" value="'.$attachment.'">';
			$upload.= '<a href="javascript:remove_image(\''.$attachment.'\');" class="cp-remove button">Remove</a>';
			$upload.= '</div>';
		}
		
		$upload.= '</div>';

		return $upload;
	}

	private function get_links($links, $parent_id = 0, $indent = 0) {
		$return = '';

		if (isset($links[$parent_id])) {
			foreach ($links[$parent_id] as $link) {
				$return.= '<option value="' . $link->ID . '"';
				if (isset($link->selected))
					$return.= ' selected="selected" ';
				$return.= '>';
				for ($i=0; $i<$indent; $i++) {
					$return.= '---';
				}
				if ($indent)
					$return.= ' ';
				$return.= $link->post_title;
				$return.= '</option>';
				$return.= $this->get_links($links, $link->ID, $indent+1);
			}
		}
		return $return;
	}

	private function get_field_attributes($attributes = array(), $default_attributes = array()) {

		$attributes = array_merge($default_attributes, $attributes);

		$styles = '';
		$return = '';
		
		if (is_array($attributes)) {
			foreach ($attributes AS $key => $attribute) {
				switch($key) {
					case 'width':
					case 'height':
						$styles.= $key . ': ' . $attribute . '; ';
						break;
					case 'style':
						$styles.= $attribute;
						break;
					case 'disabled':
					case 'readonly':
					case 'required':
					case 'autofocus':
						if ($attribute)
							$return.= ' ' . $key . '="' . $key . '"';
						break;
					case 'class':
					case 'size':
					case 'maxlength':
					case 'rows':
					case 'cols':
					case 'pattern':
					case 'placeholder':
					case 'min':
					case 'max':
					case 'step':
					case 'autocomplete':
						if ($attribute !== false && $attribute !='')
						$return.= ' ' . $key . '="' . $attribute . '"';
						break;
				}
			}
		}
		
		if ($styles)
			$return.= ' style="'.$styles.'"';
		
		return $return;
	}
}