<?php

class CP_Template {

	var $templaes = array();

	function __construct() {
		$this->init_templates();
		$this->_init();
	}
	
	public function _init() {
		
		add_action('pre_post_update', array($this, 'save_meta_boxes'), 10, 2);

		// add meta boxes
		add_action('admin_init', array($this, 'add_meta_boxes'));
	}

	public function get_template($template_id) {
		if (isset(CP::$config['template'][$template_id])) {
			return CP::$config['template'][$template_id];
		}

		return null;
	}

	private function init_templates() {
		if (isset(CP::$config['template'])) {
			foreach (CP::$config['template'] as $key => $template) {
				$this->templates[$template['post_type']][$key] = $template;
			}
		}
	}

	function add_meta_boxes() {
		global $CP_Mb;

		if (isset($this->templates)) {
			foreach ($this->templates as $key => $template) {
				add_meta_box (
					'1231',
					'Template',
					array($this, 'add_template_field'), 
					'page',
					'side',
					'default',
					$template
				);
			}
		}
	}

	function add_template_field($post, $meta_box) {
		global $CP_Field;

		$values = array('' => '-- default --');
		foreach ($meta_box['args'] as $key => $arg) {
			if ($arg['active']) {
				$values[$key] = $arg['name'];
			}
		}

		$value = get_post_meta( $post->ID, '_cp_template', true );

		echo $CP_Field->get_select($value, '_cp_template', '_cp_template', $values);
	}

	public function save_meta_boxes($post_id, $post) {
		
		// get post type from post object
		$post_type = get_post_type_object($post['post_type']);

		// Check if the current user has permission to edit the post.
		if (!current_user_can($post_type->cap->edit_post, $post_id)) {
			return;
		}

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}


		// Get the posted data
		$_cp_template = ( isset($_POST['_cp_template']) ? $_POST['_cp_template'] : '' );

		update_post_meta($post_id, '_cp_template', $_cp_template);
	}

// end class
}