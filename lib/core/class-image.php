<?php

class CP_Image {

	/**
	 * 
	 */
	public function __construct() {
		// load phpThumb
		CP::load_library(CP_PATH.'/lib/phpThumb/phpthumb.class.php');
		
		$this->phpThumb = new phpThumb();
	}

	public function get_image($id, $params) {
		$parameters = $this->get_parameters($params);

		$size = $this->get_size($parameters);
		$options = $this->get_options($parameters);
		$attributes = $this->get_attributes($parameters);

		if ( isset($parameters['link']) && $parameters['link'] ) {
			return $this->get_image_link($id, $size, $options);
		} else {
			return $this->get_image_tag($id, $size, $options, $attributes);
		}
	}

	public function get_size($parameters) {
		$size = array();
		
		$possible_attr = array('w', 'h', 'q', 'zc');

		foreach ($possible_attr as $attr) {
			if (isset($parameters[$attr])) {
				$size[$attr] = $parameters[$attr];
			}
		}


		return $size;
	}

	public function get_parameters($params) {
		global $cp_config;
		
		if ( isset($params['size']) ) {
			if (isset($cp_config['image'][$params['size']])) {
				$params = array_merge($cp_config['image'][$params['size']], $params);
			}
		}

		return $params;
	}

	public function get_options($parameters) {
		$options = array();
		
		$possible_attr = array('cache', 'size');

		foreach ($possible_attr as $attr) {
			if (isset($parameters[$attr])) {
				$options[$attr] = $parameters[$attr];
			}
		}


		return $options;
	}

	
	public function get_attributes($parameters) {
		$attributes = array();

		$possible_attr = array('class', 'alt', 'title');

		foreach ($possible_attr as $attr) {
			if (isset($parameters[$attr])) {
				$attributes[$attr] = $parameters[$attr];
			}
		}

		return $attributes;
	}

	public function get_image_link($id, $size, $options = array() ) {
		$img_metadata = wp_get_attachment_metadata( $id );

		if ( ! $img_metadata ) {
			return null;
		}

		$wp_upload_dir = wp_upload_dir();
		$upload_url = $wp_upload_dir['baseurl'].'/'.dirname($img_metadata['file']).'/';
		$upload_dir = $wp_upload_dir['basedir'].'/'.dirname($img_metadata['file']).'/';

		$new_filename = $this->get_filename($img_metadata, $size, $options);

		if (file_exists($upload_dir.$new_filename)) {
			return $upload_url.$new_filename;
		} else {
			if ($this->create_image($upload_dir.basename($img_metadata['file']), $upload_dir.$new_filename, $size)) {
				return $upload_url.$new_filename;
			}
		}

		return null;
	}

	public function get_image_tag($id, $size, $options = array(), $attributes = array()) {
		$image = '<img src="';

		$image.= $this->get_image_link($id, $size, $options);

		$image.= '"';

		foreach ($attributes as $tag => $attr) {
			$image.= ' '.$tag.'="';
			$image.= $attr;
			$image.= '"';
		}

		$image.= '>';

		return $image;
	}

	public function get_filename($img_metadata, $size, $options) {
		$img = basename($img_metadata['file']);
		$img_ext = pathinfo($img, PATHINFO_EXTENSION);
		$img_file = basename($img, '.' . $img_ext);

		if (isset($options['size'])) {
			return $img_file.'_'.$options['size'].'.'.$img_ext;
		} else {
			$filename = $img_file;

			if (isset($size['w'])) {
				$filename.= '_'.$size['w'];
			}

			if (isset($size['h'])) {
				$filename.= '_'.$size['h'];
			}

			return $filename.'.'.$img_ext;
		}
	}

	function create_image($src, $new, $size) {
		$phpThumb = new phpThumb();

		if (file_exists($src)) {
			$phpThumb->setSourceFilename($src);
			
			foreach ($size as $key => $s) {
				$phpThumb->setParameter($key, $s);
			}

			if ($phpThumb->GenerateThumbnail()) {
				if ($phpThumb->RenderToFile($new)) {

					$phpThumb->purgeTempFiles();

					return 1;
				}
			}
		}

		return null;
	}

	
// class end
}