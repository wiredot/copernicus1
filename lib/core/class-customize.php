<?php

/**
 * Admin class
 *
 * @package Copernicus
 * @author Piotr Soluch
 */

class CP_Customize {
  private $sections = array();
  private $settings = array();

	function __construct() {

		if (isset(CP::$config['customize_section'])) {
		  $this->sections = CP::$config['customize_section'];
		}
		if (isset(CP::$config['customize_settings'])) {
		  $this->settings = CP::$config['customize_settings'];
		}

		$this->_init();
	}
	
	public function _init() {
		// Setup the Theme Customizer settings and controls...
		
		add_action( 'customize_register' , array( $this , 'register_sections' ) );
		//	add_action( 'customize_register' , array( $this , 'register_settings' ) );

		// Output custom CSS to live site
		add_action( 'wp_head' , array( $this , 'header_output' ) );

		// Enqueue live preview javascript in Theme Customizer admin screen
		add_action( 'customize_preview_init' , array( $this , 'live_preview' ) );
	}

  	public function register_sections( $wp_customize ) {
		foreach ($this->sections as $key => $section) {
			$wp_customize->add_section( $key, 
				array(
					'title' => $section['title'], //Visible title of section
					'priority' => $section['priority'], //Determines what order this appears in
					'capability' => $section['capability'], //Capability needed to tweak
					'description' => $section['description'] //Descriptive tooltip
				)
			);
		}
  
		foreach ($this->settings as $key => $settings) {
			$wp_customize->add_setting( $key, //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
				array(
					'default' => $settings['default'], //Default setting/value to save
					'type' => $settings['type'], //Is this an 'option' or a 'theme_mod'?
					'capability' => $settings['capability'], //Optional. Special permissions for accessing this setting.
					'transport' => $settings['transport'], //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
				) 
			);

			$wp_customize->add_control( 
				new $settings['control']( //Instantiate the color control class
					$wp_customize, //Pass the $wp_customize object (required)
					$settings['section'].'_'.$key, //Set a unique ID for the control
					array(
						'label' => $settings['label'], //Admin-visible name of the control
						'section' => $settings['section'], //ID of the section this control should render in (can be one of yours, or a WordPress default section)
						'settings' => $key, //Which setting to load and manipulate (serialized is okay)
						'priority' => $settings['priority'], //Determines the order this control appears in for the specified section
					) 
				) 
			);
		}
	}

   	public function header_output() {
		echo '<style type="text/css">';
		foreach ($this->settings as $key => $settings) {
			if (isset($settings['css'])) {
				foreach ($settings['css'] as $css) {
					$value = get_theme_mod($key);
					if ($value) {
						if (preg_match('/rgba/', $css)) {
							echo sprintf($css, $this->hex2rgb($value))."\n";
						} else {
							echo sprintf($css, $value)."\n";
						}
					}
				}
			}
		}
		echo '</style>';
   	}

	public static function live_preview() {
		global $CP_Customize;
		$script = $CP_Customize->get_js();

		$script_md5 = md5($script);
		$script_file = WP_CONTENT_DIR.'/cache/js/'.$script_md5.'.js';
		$script_url = WP_CONTENT_URL.'/cache/js/'.$script_md5.'.js';

		if (!file_exists($script_file)) {
			$script_stream = @fopen($script_file, 'w');		
			fwrite($script_stream, $script);
			fclose($script_stream);
		}

		wp_enqueue_script( 
		   'mytheme-themecustomizer', // Give the script a unique ID
			$script_url, // Define the path to the JS file
		   array(  'jquery', 'customize-preview' ), // Define dependencies
		   '1.3', // Define a version (optional) 
		   true // Specify whether to put in footer (leave this true)
		);
   }

	function get_js() {
		$return = "( function( $ ) {";
		foreach ($this->settings as $key => $settings) {
			if (isset($settings['css'])) {
				$return.= "
				wp.customize( '".$key."', function( value ) {
					value.bind( function( newval ) {
						
				";
				foreach ($settings['css'] as $css) {
					$parts = explode('{', $css);

					$styles = explode(':', $parts[1]);
					$return.= "$('".$parts[0]."').css('".$styles[0]."', newval );";
				}
				$return .= "} );
				} );";
			}
		}

		$return.= "} )( jQuery );";

		return $return;
		return "
			( function( $ ) {

			// Update the site title in real time...
			wp.customize( 'blogname', function( value ) {
			value.bind( function( newval ) {
			$( '#site-title a' ).html( newval );
			} );
			} );

			//Update the site description in real time...
			wp.customize( 'blogdescription', function( value ) {
			value.bind( function( newval ) {
			$( '.site-description' ).html( newval );
			} );
			} );

			//Update site title color in real time...
			wp.customize( 'header_textcolor', function( value ) {
			value.bind( function( newval ) {
			$('#site-title a').css('color', newval );
			} );
			} );

			//Update site background color...
			wp.customize( 'background', function( value ) {
			value.bind( function( newval ) {
			$('body').css('background-color', newval );
			} );
			} );

			//Update site link color in real time...
			wp.customize( 'main_color', function( value ) {
			value.bind( function( newval ) {
			$('p a').css('color', newval );
			$('header').css('background-color', newval );
			} );
			} );

			//Update site link color in real time...
			wp.customize( 'secondary_color', function( value ) {
			value.bind( function( newval ) {
			$('header nav ul li.current_page_item a').css('background-color', newval );
			$('header nav ul li a:hover').css('background-color', newval );
			} );
			} );

			} )( jQuery );
		";
	}

   function hex2rgb($hex) {
		$color = str_replace('#', '', $hex);

		//Check if color has 6 or 3 characters and get values
		if (strlen($color) == 6) {
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) == 3 ) {
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
			return null;
		}

		//Convert hexadec to rgb
		$rgb = array_map('hexdec', $hex);

		return implode(",",$rgb);
   }

// class end
}