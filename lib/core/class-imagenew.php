<?php

class CP_Imagenew {

	private $attachment_id;
	private $attributes;
	private $attachment_metadata;
	private $image_config;
	private $upload_dir;

	public function __construct($attachment_id = null, $attributes = array()) {
		add_filter('the_content', array($this, 'inline_images'));

		if ( ! $attachment_id ) {
			return null;
		}

		$this->_set_attachment_id($attachment_id);
		$this->_set_attributes($attributes);
		$this->_set_attachment_metadata($attachment_id);
		$this->_set_image_config();
		$this->_set_upload_dir();
	}

	public function inline_images($content) {
		$content = preg_replace_callback('/<img(.*)>/', array($this, 'replace_inline_images') , $content);
		return $content;
	}

	private function replace_inline_images($image) {
		$theimage = $image[0];
		$sizes = $this->_get_image_sizes();

		preg_match('/wp-image-([0-9]+)/', $theimage, $attachment);
		$attachment_id = $attachment[1];

		preg_match('/class="([0-9a-z A-Z\-]+)"/', $theimage, $classes);
		$classes_array = explode(' ', $classes[1]);

		$size = $this->_find_matching_size($classes_array, $sizes);

		if ( ! $size ) {
			return $theimage;
		}

		$new_image = new CP_Imagenew($attachment_id, array('class'=>$classes_array));
		return $new_image->get_image_tag('inline');
	}

	public function get_image_tag($config_id) {
		
		return $this->_get_image_tag($this->attachment_id, $config_id);
	}

	private function _get_image_tag($attachment_id, $image_config_id) {
		$image_config = $this->_get_image_config_by_id($image_config_id);
		$img = '<img src="';
		$img.= $this->_get_image_url_by_size($image_config_id);
		$img.= '"';
		if (isset($image_config['srcset']) && $image_config['srcset']) {
			$srcsets = array();
			foreach ($image_config['srcset'] as $key => $srcset) {
				$image_url = $this->_get_image_url_by_size($image_config_id, $key);
				if ($image_url) {
					$srcsets[$key] = $image_url;
				}
			}
			$img.= $this->_get_image_srcset_attribute($srcsets);
		}
		$img.= $this->_get_image_attributes();
		$img.= '>';

		return $img;
	}

	private function _get_image_url_by_size($size, $srcset = '') {
		$image_url = '';

		$cp_size = 'cp_'.$size;
		if ($srcset) {
			$cp_size.= '_'.$srcset;
		}

		if (isset($this->attachment_metadata['sizes'][$cp_size])) {
			$image_url =  $this->attachment_metadata['sizes'][$cp_size]['file'];
		} else {
			$image_url =  $this->_create_image_by_size($size, $srcset);
		}

		if ($image_url) {
			return $this->upload_dir['url'].$image_url;
		}
		
		return null;
	}

	private function _create_image_by_size($size, $srcset) {
		$cp_size = 'cp_'.$size;
		if ($srcset) {
			$cp_size.= '_'.$srcset;
		}

		$phpThumb = new phpThumb();
		$attachment_file = basename($this->attachment_metadata['file']);
		$ext = pathinfo($attachment_file, PATHINFO_EXTENSION);

		$file = basename($attachment_file, '.' . $ext);
		$new_file = $file . '_' . $cp_size . '.' . $ext;

		$phpThumb->resetObject();
		$phpThumb->setSourceFilename($this->upload_dir['dir'] . '/' . $attachment_file);

		$image_parameters = $this->_get_image_parameters($size, $srcset);

		foreach ($image_parameters as $key => $attr) {
			$phpThumb->setParameter($key, $attr);
		}

		if ($phpThumb->GenerateThumbnail()) {
			if ($phpThumb->RenderToFile($this->upload_dir['dir'] . '/' . $new_file)) {
				$image_info = getimagesize($this->upload_dir['dir'] . '/' . $new_file);

				$attachment_metadata = array(
					'file' => $new_file,
					'width' => $image_info[0],
					'height' => $image_info[1],
					'mime-type' => $image_info['mime']
				);

				$this->_save_image_size($this->attachment_id, $cp_size, $attachment_metadata);

				$phpThumb->purgeTempFiles();
			}
		}

		return $new_file;
	}

	private function _get_image_srcset_attribute($srcset) {
		$srcset_tag = '';

		if ($srcset && is_array($srcset) && count($srcset)) {
			$srcset_tag.= ' srcset="';
			end($srcset);
			$last_key = key($srcset);
			foreach ($srcset as $scrkey => $scrvalue) {
				$srcset_tag.= $scrvalue . ' ' . $scrkey;

				if ($scrkey != $last_key) {
					$srcset_tag.= ', ';
				}
			}
			$srcset_tag.= '"';
		}
		return $srcset_tag;
	}

	private function _get_image_attributes() {
		$attributes = '';

		if ($this->attributes) {
			foreach ($this->attributes as $tag => $attributes_array) {
				$attributes.= ' '.$tag.'="';
				end($attributes_array);
				$last_key = key($attributes_array);
				foreach ($attributes_array as $key => $attribute) {
					$attributes.= $attribute;
					if ($key != $last_key) {
						$attributes.= ' ';
					}
				}
				$attributes.= '"';
			}
		}

		return $attributes;
	}

	private function _get_image_config_by_id($config_id) {
		if (isset($this->image_config[$config_id])) {
			return $this->image_config[$config_id];
		}

		return null;
	}

	private function _get_image_parameters($size, $srcset) {
		$image_parameters = array();

		if ($srcset) {
			if (isset($this->image_config[$size]['srcset'][$srcset])) {
				$image_parameters = $this->image_config[$size]['srcset'][$srcset];
			}
		} else {
			$image_parameters = $this->image_config[$size];
			unset($image_parameters['srcset']);
		}

		return $image_parameters;
	}

	private function _get_image_sizes() {
		$sizes = array();
		if (isset (CP::$config['image'])) {
			foreach (CP::$config['image'] as $key => $image) {
				$sizes[] = $key;
			}
		}
		return $sizes;
	}

	private function _find_matching_size($classes, $sizes) {
		foreach ($sizes as $size) {
			foreach ($classes as $class) {
				if ($class == $size) {
					return $size;
				}
			}
		}

		return null;
	}

	private function _set_attachment_id($attachment_id) {
		$this->attachment_id = $attachment_id;
	}

	private function _set_attributes($attributes) {
		$this->attributes = $attributes;
	}

	private function _set_attachment_metadata($attachment_id) {
		$attachment_metadata = wp_get_attachment_metadata( $attachment_id );
		$this->attachment_metadata = $attachment_metadata;
	}

	private function _set_image_config() {
		$image_config = null;

		if (isset (CP::$config['image'])) {
			$image_config = CP::$config['image'];
		}
		$this->image_config = $image_config;
	}

	private function _set_upload_dir() {

		$wp_upload_dir = wp_upload_dir();

		$upload_dir['url'] = $wp_upload_dir['baseurl'].'/'.dirname($this->attachment_metadata['file']).'/';
		$upload_dir['dir'] = $wp_upload_dir['basedir'].'/'.dirname($this->attachment_metadata['file']).'/';
		
		$this->upload_dir = $upload_dir;
	}

	private function _save_image_size($attachment_id, $size, $attachment_metadata) {
		$new_attachment_metadata = $this->attachment_metadata;
		$new_attachment_metadata['sizes'][$size] = $attachment_metadata;
		$this->attachment_metadata = $new_attachment_metadata;
		wp_update_attachment_metadata( $attachment_id, $new_attachment_metadata );
	}
	
// class end
}