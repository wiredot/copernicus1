<?php

/**
 * Copernicus Theme Framework cleanup class file
 *
 * @package Copernicus
 * @subpackage Copernicus Theme Framework
 * @author Piotr Soluch
 */

/**
 * Cleanup class
 *
 * @package Copernicus
 * @subpackage Copernicus Theme Framework
 * @author Piotr Soluch
 */
class CP_Image {

	public $phpThumb;

	var $cleanup = array();

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
		// load phpThumb
		CP::load_library(CP_PATH.'/lib/phpThumb/phpthumb.class.php');
		
		$this->phpThumb = new phpThumb();
	}
	
	public function image($params) {
		if (!isset ($params['id']) || $params['id'] < 1) {
			return null;
		}
		
		$img_attributes = '';
		$img = array();
		$attachment = wp_get_attachment_metadata( $params['id'] );
		foreach ($params as $key => $value){
			if ($key=="alt" || $key=="title" || $key=="class" || $key=="style") {
				$img_attributes.=' '.$key.'="'.$value.'"';
			}
			else if ($key=="fltr" || $key=="fltr2"){
				//$value = convert_to_ent($value);
				$return.="fltr[]=".$value;
				//echo $value;
				if($value != end($params)) $return.="&";
			}
			else if ($key!="link") {
				//$value = convert_to_ent($value);
				if ($key=="w"){
					$img_attributes.=' width="'.$value.'"';
				}
				else if ($key=="h"){
					$img_attributes.=' height="'.$value.'"';
				}
				
				$img['attributes'][$key] = $value;
			}
		}
		$meta_data = $this->get_attachment_thumbnails($params['id']);
		
		$upload_dir = wp_upload_dir();
		$this_img = $this->thumbnail_exist($img, $meta_data);

		if ($this_img){

			$file_url = $upload_dir['baseurl'].'/'.$this_img['file'];
		}
		else {
			$file_url = '';
			$newfilename = wp_unique_filename( $upload_dir['basedir'].'/'.dirname($attachment['file']), basename($attachment['file']) );

			$this->phpThumb->resetObject();
			// set data source -- do this first, any settings must be made AFTER this call
			$this->phpThumb->setSourceFilename($upload_dir['basedir'].'/'.$attachment['file']);
			
			$output_filename = dirname($attachment['file']) . '/' . $newfilename;
			
			foreach ($img['attributes'] as $key => $attr) {
				$this->phpThumb->setParameter($key, $attr);
			}
			
			if ($this->phpThumb->GenerateThumbnail()) {

				if ($this->phpThumb->RenderToFile($upload_dir['basedir'].'/'.$output_filename)) {
					// do something on success
					$img['file'] = $output_filename;
					$meta_data[] = $img;
					$this->set_attachment_thumbnails($params['id'], $meta_data);
					$file_url = $upload_dir['baseurl'].'/'.$output_filename;
					
				} else {
					// do something with debug/error messages
					//echo 'Failed:<pre>'.implode("\n\n", $this->phpThumb->debugmessages).'</pre>';
				}
				$this->phpThumb->purgeTempFiles();
			} else {
				// do something with debug/error messages
				//echo 'Failed:<pre>'.$this->phpThumb->fatalerror."\n\n".implode("\n\n", $this->phpThumb->debugmessages).'</pre>';
			}
		}
		
		if (isset($params['link']) && $params['link']) 
			return $file_url;
		else 
			return '<img src="'.$file_url.'"'.$img_attributes.' />';
	}
	
	private function get_attachment_thumbnails($attachment_id) {
		if (!$attachment_id)
			return null;
		
		$meta_data = get_post_meta($attachment_id, '_cp_thumbnails', true);
		
		if ($meta_data)
			return $meta_data;
	}
	
	private function set_attachment_thumbnails($attachment_id, $value) {
		if (!$attachment_id)
			return null;
		
		$meta_data = update_post_meta($attachment_id, '_cp_thumbnails', $value);
		
		if ($meta_data)
			return true;
	}
	
	private function thumbnail_exist($img, $meta_data) {
		if ($meta_data && count($meta_data)) {
			
			foreach ($meta_data as $meta) {
				
				if ($img['attributes'] == $meta['attributes']) {
					return $meta;
				}
			}
		}
		return false;
	}

}