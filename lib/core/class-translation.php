<?php

/**
 * Copernicus translation class file
 *
 * @package Copernicus
 * @subpackage Copernicus Theme
 * @author Piotr Soluch
 */

/**
 * translation class
 *
 * @package Copernicus
 * @subpackage Copernicus Theme
 * @author Piotr Soluch
 */
class CP_Translation {

	// all meta boxes
	private $translation = array();
	private $static_texts = array();

	/**
	 * Class constructor
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function __construct() {
		
		add_action( 'admin_menu', array(&$this, 'admin_menu') );
		add_action( 'admin_init', array($this, 'register_settings') );

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

		$translations_folder = get_stylesheet_directory() . '/languages/';
		// get all files from config folder
		if (file_exists($translations_folder) && $handle = opendir($translations_folder)) {

			// for each file with .config.php extension
			while (false !== ($filename = readdir($handle))) {
				
				if (preg_match('/.csv$/', $filename)) {
				
					$this->get_adapter_csv($translations_folder.$filename);
				}
			}
			closedir($handle);
		}
	}
	
	/**
	 * 
	 * @param type $translation
	 */
	private function get_adapter_csv($csv_filename) {
		if (file_exists($csv_filename)) {

			// default options
			$adapter = array(
				'lenght' => 0,
				'delimiter' => ',',
				'enclosure' => '"',
				'escape' => '\\'
			);

			$translation_key = '';
			$this->languages = array();

			$csv_file = @fopen($csv_filename, 'rb');
			if ($csv_file) {

				// go through every row in csv file
				while (($data = fgetcsv($csv_file, $adapter['lenght'], $adapter['delimiter'], $adapter['enclosure'])) !== false) {
					
					// if this is the first row, get the languages names
					if (!count($this->languages)) {
						foreach ($data AS $key => $language) {
							if ($key > 0) {
								$this->languages[$key] = $language;
							}
						}
					}
					else {
						
						// if the first cell in the row is not empty
						if ($data[0]) {
							// for every column
							foreach ($data AS $key => $language) {
								// first column
								if ($key == 0) {
									$translation_key = $language;
								}
								else {
									$this->translation[$translation_key][$this->languages[$key]] = $language;
								}
							}
						}
					}
				}
			}
		}
	}
	
	public function translate($text, $group = '') {

		if ($group) {
			$translation = $this->translate_group($text, $group);
			if ($translation) {
				return $translation;
			}
		}
		else {
			$groups = $this->get_groups();
			foreach ($groups as $group) {
				$translation = $this->translate_group($text, $group);
				if ($translation) {
					return $translation;
				}
			}
		}

		return $text;
	}

	function translate_group($text, $group) {
		$translations = get_option( 'cp_translation_'.$group );
		if (defined('LANGUAGE_SUFFIX') && LANGUAGE_SUFFIX) {
			if (isset($translations[$text.LANGUAGE_SUFFIX]) && !empty($translations[$text.LANGUAGE_SUFFIX])) {
				return $translations[$text.LANGUAGE_SUFFIX];
			}
		}
		if (isset($translations[$text]) && !empty($translations[$text])) {
			return $translations[$text];
		}

		return null;
	}
	/**
	 * 
	 * @param type $text
	 * @param string $language
	 * @return type
	 */
	public function translate2($text, $language = '') {
		$phrase = $this->get_phrase($text);

		if (!$phrase) {
			return $text;
		}

		if (!$language) {
			$language = LANGUAGE;
		}
		
		$translation = $this->get_translation($language, $phrase);

		if ($translation) {
			return $translation;
		}

		return $text;
	}
	
	/**
	 * 
	 * @param type $text
	 * @return null
	 */
	private function get_phrase($text) {
		if (isset($this->translation[$text]))
			return $this->translation[$text];
		return null;
	}
	
	/**
	 * 
	 * @param type $language
	 * @param type $phrase
	 * @return null
	 */
	private function get_translation($language, $phrase) {

		if (isset($phrase[$language]))
			return $phrase[$language];

		if (preg_match('/[a-zA-Z]{2}_[a-zA-Z]{2}/', $language)) {
			$language = preg_replace('/_[a-zA-Z]{2}/', '', $language);
			return $this->get_translation($language, $phrase);
		}

		foreach ($phrase AS $key => $text) {
			if (preg_match('/' . $language . '_[a-zA-Z]{2}/', $key))
				return $text;
		}

		if (isset($phrase[$language]))
			return $phrase[$language];

		return null;
	}

/* -------------- admin -------------- */

	public function get_groups() {
		$static_texts = $this->get_static_texts();
		return array_keys($static_texts);
	}

	public function register_settings() {
		$groups = $this->get_groups();
		foreach ($groups as $group) {
			register_setting( 'cp_translation_'.$group, 'cp_translation_'.$group );	
		}
	}

	public function admin_menu() {
		$general = __cp('general', 'general');
		add_options_page(__cp('Translations'), __cp('Translations'), 'manage_options', 'cp_translation', array(&$this, 'translation_page'));
	}

	public function translation_page() {
		global $CP_Field;
		
		$current_tab = 'general';
		if (isset($_GET['tab'])) {
			$current_tab = $_GET['tab'];
		}

		$values = get_option( 'cp_translation_'.$current_tab );
		echo '<div class="wrap">';
		echo '<h2>';
		_cpe('Translations', 'general');
		echo ' › ';

		_cpe($current_tab);

		echo '</h2>';

		$static_texts = $this->get_static_texts();

		echo '<ul class="subsubsub">';
		foreach ($static_texts as $key => $static_text) {
			echo '<li><a href="?page=cp_translation&tab='.$key.'"';
			if ($key == $current_tab) {
				echo ' class="current"';
			}
			echo '>'.__cp($key).'</a>';
			if ($key != key(array_slice($static_texts, -1, 1, true))) {
				echo ' | ';
			}
			echo '</li>';
		}
		echo '</ul>';

		echo '<form action="options.php" method="post">';
		echo '<table class="form-table"><tbody>';

		
		//new dBug($static_texts);

		if (isset($static_texts[$current_tab]) && is_array($static_texts[$current_tab])) {
			
			foreach ($static_texts[$current_tab] as $text => $val) {
				$field = array(
					'id' => 'cp_translation_'.$text,
					'type' => 'text'
				);
				echo '<tr valign="top">';
				echo '<th scope="row"><label for="cp_translation_'.$text.'">'.$text.'</labe></th>';
				echo '<td>';
				echo $CP_Field->show_multilanguage_field($field, sanitize_title($field['id']), 'cp_translation_'.$current_tab.'['.$text.']', $values, $text);
				echo '</td>';
				echo '</tr>';
			}
		}
		echo '</tbody></table>';
		echo '<input type="submit" name="submit" id="submit" class="button button-primary" value="Save changes">';
		settings_fields('cp_translation_'.$current_tab);
		echo '</form>';
		echo '</div>';
	}

	public function get_static_texts() {

		if (count($this->static_texts)) {
			return $this->static_texts;
		}

		$texts = array();
		$textsa = $this->get_static_texts_folder( get_template_directory()  );
		$textsb = $this->get_static_texts_folder( get_template_directory() . '/lib/' );
		$textsc = $this->get_static_texts_folder( get_template_directory() . '/templates/' );
		$texts_all = array_merge_recursive( $textsa, $textsb, $textsc );
		$texts_child = array();

		if (is_child_theme()) {
			$textsa = $this->get_static_texts_folder( get_stylesheet_directory() . '/' );
			$textsb = $this->get_static_texts_folder( get_stylesheet_directory() . '/templates/' );
			$textsc = $this->get_static_texts_folder( get_stylesheet_directory() . '/lib/' );
			$textsd = $this->get_static_texts_folder( get_stylesheet_directory() . '/config/' );
			$texts_child = array_merge_recursive( $textsa, $textsb, $textsc, $textsd );
		}
		
		$texts = array_merge_recursive( $texts_all, $texts_child );
		
		foreach ($texts as $key => $value) {
			uksort($value, 'strnatcasecmp');
			$texts[$key] = $value;
		}

		$this->static_texts = $texts;

		return $texts;
	}

	public function get_static_texts_folder( $folder ) {
		$texts = array();

		if ( file_exists($folder) && $handle = opendir( $folder ) ) {

			/* This is the correct way to loop over the directory. */
			while ( false !== ( $entry = readdir( $handle ) ) ) {
				$new_array = $this->get_static_texts_file( $folder . $entry );
				if ( is_array( $new_array ) ) {
					$texts = array_merge_recursive( $texts, $new_array );
				}
			}

			closedir( $handle );
		}
		return $texts;
	}

	public function get_static_texts_file( $filename ) {
		$results = array();
		if ( file_exists( $filename ) && preg_match( '/.php/', $filename ) ) {

			$text = file_get_contents( $filename );
			preg_match_all( "/__?cpe?\(['".'"'."]([^\(\))]+)['".'"'."], \'([a-zA-Z0-9_]+)\'\)/", $text, $matches );
			
			if ( $matches[1] ) {
				foreach ($matches[2] as $key => $match) {
					$results[$match][$matches[1][$key]] = '';
				}
				return $results;
			}
		}
		else if ( file_exists( $filename ) && preg_match( '/.html/', $filename ) ) {
			$text = file_get_contents( $filename );
			preg_match_all( "/{['\"]([^\{\}\|)]+)['\"]\|translate:([a-zA-Z0-9_]+)}/", $text, $matches );

			foreach ($matches[2] as $key => $match) {
				$results[$match][$matches[1][$key]] = '';
			}
			return $results;
		}

		return null;
	}
}

function __cp($text, $group = '') {
	global $CP_Translation;
	$text = $CP_Translation->translate($text, $group);
	return $text;
}

function _cpe($text, $group = '') {
	echo __cp($text, $group);
}
