<?php

/**
 * Main framework class
 *
 * @package Copernicus
 * @author Piotr Soluch
 */

/**
 * 
 */
class CP {

	public static $config = array();
	
	public static $smarty;
	
/* -------------- methods -------------- */	
	
	public static function init() {
		session_start();

		self::load_translations();
		
		// load config file
		self::load_config();
		
		// autoload copernicus classes
		self::autoload_classes(CP_PATH.'/lib/core');

		// autoload child theme classes
		self::autoload_classes(get_template_directory().'/lib');

		// init plugins
		self::init_plugins();
	}

/* -------------- views -------------- */	

	public static function header() {
		global $CP_Header;
		$CP_Header->show_header();
	}
	
	public static function footer() {
		global $CP_Footer;
		$CP_Footer->show_footer();
	}

	public static function view($template) {
		global $CP_Smarty;

		if (!$CP_Smarty->smarty->templateExists($template)) {
			return false;
		}

		$view = '';
		
		if (have_posts()) {
			the_post();
		}
		
		$view.= $CP_Smarty->smarty->fetch($template);
		
		echo $view."\n";

		return true;
	}

	public static function template() {
		global $CP_Template;
		$post_id = '';

		if (is_404()) {
			if (CP::view('404.html')) {
				return;
			}
		}
		
		if (@get_the_ID()) {
			$post_id = get_the_ID();
		}
		
		if ($post_id) {
			$template_id = get_post_meta( $post_id, '_cp_template', true );
			if ($template_id) {
				$template = $CP_Template->get_template($template_id);
				if ($template && $template['active']) {
					if (CP::view($template['file'])) {
						return;
					}
				}
			}
		}

		if (is_home()) {
			if (CP::view('home.html')) {
				return;
			}
		}

		if (is_front_page()) {
			if (CP::view('front-page.html')) {
				return;
			}
		}

		if (is_search()) {
			if (CP::view('search.html')) {
				return;
			}
		}

		if (is_date()) {
			if (CP::view('date.html')) {
				return;
			} else if (CP::view('archive.html')) {
				return;
			}
		}

		if (is_author()) {
			$user_nicename = get_the_author_meta( 'nickname' );
			$user_id = get_the_author_meta( 'ID' );

			if ($user_nicename && CP::view('author-'.$user_nicename.'.html')) {
				return;
			} else if ($user_id && CP::view('author-'.$user_id.'.html')) {
				return;
			} else if (CP::view('author.html')) {
				return;
			} else if (CP::view('archive.html')) {
				return;
			}
		}

		if (is_category()) {
			$category_id = get_query_var('cat');
			$current_category = get_category ($category_id);
			$category_slug = $current_category->slug;
			if ($category_slug && CP::view('category-'.$category_slug.'.html')) {
				return;
			} else if ($category_id && CP::view('category-'.$category_id.'.html')) {
				return;
			} else if (CP::view('category.html')) {
				return;
			} else if (CP::view('archive.html')) {
				return;
			}
		}

		if (is_tag()) {
			$current_tag_id = get_queried_object()->term_id;
			$current_tag_slug = get_queried_object()->slug;
			if ($current_tag_slug && CP::view('tag-'.$current_tag_slug.'.html')) {
				return;
			} else if ($current_tag_id && CP::view('tag-'.$current_tag_id.'.html')) {
				return;
			} else if (CP::view('tag.html')) {
				return;
			} else if (CP::view('archive.html')) {
				return;
			}
		}

		if (is_tax()) {
			$current_tag_id = get_queried_object()->term_id;
			$current_tag_slug = get_queried_object()->slug;
			$current_tag_taxonomy = get_queried_object()->taxonomy;
			if ($current_tag_taxonomy && $current_tag_slug && CP::view('taxonomy-'.$current_tag_taxonomy.'-'.$current_tag_slug.'.html')) {
				return;
			} else if ($current_tag_taxonomy && $current_tag_id && CP::view('taxonomy-'.$current_tag_taxonomy.'-'.$current_tag_id.'.html')) {
				return;
			} else if ($current_tag_taxonomy && CP::view('taxonomy-'.$current_tag_taxonomy.'.html')) {
				return;
			} else if (CP::view('taxonomy.html')) {
				return;
			} else if (CP::view('archive.html')) {
				return;
			}
		}

		if (is_archive()) {
			if (CP::view('archive-'.get_post_type().'.html')) {
				return;
			} else if (CP::view('archive.html')) {
				return;
			}
		}
		
		if (is_single()) {
			if (CP::view('single-'.get_post_type().'.html')) {
				return;
			} else if (CP::view('single.html')) {
				return;
			}
		}

		if (is_attachment()) {
			$mime_type = get_post_mime_type();
			$mime_type_parts = explode("/", $mime_type);
			if ($mime_type_parts[0] && $mime_type_parts[1] && CP::view($mime_type_parts[0] . '_' . $mime_type_parts[1] .'.html')) {
				return;
			} else if ($mime_type_parts[0] && CP::view($mime_type_parts[0] .'.html')) {
				return;
			} else if ($mime_type_parts[1] && CP::view($mime_type_parts[1] .'.html')) {
				return;
			} else if (CP::view('attachment.html')) {
				return;
			} else if (CP::view('single.html')) {
				return;
			}
		}
		
		if (is_page()) {
			global $post;
			if (CP::view('page-'.$post->post_name.'.html')) {
				return;
			} else if (CP::view('page-'.$post_id.'.html')) {
				return;
			} else if (CP::view('page.html')) {
				return;
			}
		}
	}

/* -------------- loaders -------------- */

	private static function init_plugins() {
		if (isset(CP::$config['plugin'])) {
			foreach (CP::$config['plugin'] as $plugin) {
				if (isset($plugin['id'])) {
					$class = strtoupper($plugin['id']);
					global $$class;
					
					$$class->init();
				}
			}
		}
	}

	private static function load_config() {
		global $cp_config;

		if (isset($cp_config['plugin'])) {
			foreach ($cp_config['plugin'] as $plugin) {
				self::load_config_directory($plugin['directory'] . '/config/');
			}
		}
		
		self::load_config_directory(get_stylesheet_directory() . '/config/');

		self::$config = $cp_config;
	}

	private static function load_config_directory($directory) {
		global $cp_config;
		// get all files from config folder
		if (file_exists($directory) && $handle = opendir($directory)) {

			// for each file with .config.php extension
			while (false !== ($filename = readdir($handle))) {
				
				if (preg_match('/.config.php$/', $filename)) {
					//reset config array
					
					if (file_exists($directory.$filename)) {
						// get config array from file
						require_once $directory.$filename;
					}
				}
			}
			closedir($handle);
		}
	}
	
	/**
	 * autoload and init all classes in a specific folder
	 * @param  string $folder_name folder name
	 * @return none
	 */
	private static function autoload_classes($folder_name) {
		if (file_exists($folder_name)) {
			$handle = opendir($folder_name);
			
			while (false !== ($entry = readdir($handle))) {
				if (preg_match('/^class-((?!copernicus).*).php/', $entry, $matches)) {
					$file = $folder_name.'/'.$matches[0];
					$class_name = 'CP_'.ucfirst($matches[1]);

					if (!class_exists($class_name)) {
						CP::load_class($file, $class_name);
					}
				}
			}
		}
	}

	private static function load_class($file, $class_name) {
		if (file_exists($file)) {
			include_once $file;
			
			global $$class_name;
			$$class_name = new $class_name;
		}
	}

	public static function load_library($library_file) {
		// check if class file exists and return true if it does
		if (file_exists($library_file)) {
			include_once $library_file;
			return true;
		}
		
		// if class doesn't exists
		echo $library_file . " doesn't exist";
		return false;
	}

	public static function load_translations() {
		CP::load_class(CP_PATH.'/lib/core/class-translation.php', 'CP_Translation');
	}

	public function ajax_response($response) {
		// encode and return response
		$response_json = json_encode( $response );
		header( "Content-Type: application/json" );
		echo $response_json;
		exit;
	}

// classs end
}
