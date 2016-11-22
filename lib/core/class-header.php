<?php

class CP_Header {
	
	/**
	 * 
	 */
	public function __construct() {
	}

	/**
	 * 
	 */
	public function show_header() {
		global $CP_Smarty, $CP_Language;

		do_action( 'cp_header' );

		$current_language = $CP_Language->get_current_language();

		$page['image'] = null;
		$page['title'] = $this->get_page_title();
		$page['content'] = $this->get_page_description();
		$page['description'] = $this->get_page_description();
		$page['slug'] = $this->get_page_slug();
		$page['language'] = str_replace('_', '-', $current_language['iso']);
		$page['locale'] = $current_language['iso'];
		
		global $post;
		if ($post) {
			if (has_post_thumbnail( $post->ID ) ) {
				$page['image'] = get_post_thumbnail_id( $post->ID );
			}
		}

		ob_start();
		wp_head();
		
		$header = ob_get_clean();
		$header = str_replace("\n", "\n\t", $header);
		
		if (isset(CP::$config['header']) && is_array(CP::$config['header'])) {
			foreach (CP::$config['header'] as $config_header) {
				$header.= $config_header;
			}
		}

		$header = preg_replace('/ \/>/', '>', $header);
		
		$CP_Smarty->smarty->assign('header', $header);
		$CP_Smarty->smarty->assign('page', $page);
		$header = $CP_Smarty->smarty->fetch('_header.html');
		
		echo $header."\n";
	}

	/**
	 * 
	 */
	private static function get_page_title() {
		global $page, $paged;

		$title = '';
		if (LANGUAGE_SUFFIX != '') {
			$seo_title = get_post_meta(get_the_id(), 'meta_title' . LANGUAGE_SUFFIX, true);
		} else {
			$seo_title = get_post_meta( get_the_id(), 'meta_title', true );
		}

		if ($seo_title) {
			$title = $seo_title;
		} else {
			if (LANGUAGE_SUFFIX != '') {
				$post_title = get_post_meta(get_the_id(), 'post_title' . LANGUAGE_SUFFIX, true);
				if ( ! $post_title ) {
					$post_title = get_post_meta(get_the_id(), 'post_title', true);
				}

				$title.= $post_title. ' | ';
			} else {
				$title.= wp_title( '|', false, 'right' );
			}
			// Add the blog name.
			$title.= get_bloginfo( 'name' );

			// Add the blog description for the home/front page.
			$site_description = get_bloginfo( 'description', 'display' );
			if ( $site_description && ( is_home() || is_front_page() ) ) {
				$title.= " | $site_description";
			}
		}

		// Add a page number if necessary:
		if ( $paged >= 2 || $page >= 2 ) {
			$title.= ' | ' . sprintf( __( 'Page %s', 'twentyeleven' ), max( $paged, $page ) );
		}
		
		return $title;
	}

	/**
	 * 
	 */
	private static function get_page_description() {

		$description = '';

		if (LANGUAGE_SUFFIX != '') {
			$seo_description = get_post_meta( get_the_id(), 'meta_description' . LANGUAGE_SUFFIX, true );
		} else {
			$seo_description = get_post_meta( get_the_id(), 'meta_description', true );
		}
		
		if ($seo_description) {
			$description = $seo_description;
		} else {
			$content = '';

			if (LANGUAGE_SUFFIX != '') {
				$content = get_post_meta( get_the_id(), 'content' . LANGUAGE_SUFFIX, true );
			}

			if ( ! $content ) {
				global $post;
				if ($post) {
					$content = $post->post_content;
				}
			}
			
			$content = str_replace("<!--more-->", ' ', $content);
			$content = str_replace("\n", ' ', $content);
			$content = str_replace("  ", ' ', $content);
			$content = strip_tags($content);
			$description = self::truncate($content);
		}

		return $description;
	}

	/**
	 * 
	 */
	private static function truncate($text, $chars = 155) {
		if (strlen($text) <= $chars) {
			return $text;
		}
		$text = $text." ";
		$text = substr($text,0,$chars);
		$text = substr($text,0,strrpos($text,' '));
		$text = $text."…";
		return $text;
	}

	/**
	 * 
	 */
	private function get_page_slug() {

		if (is_front_page()) {
			return 'front-page';
		}
		
		else if (is_home()) {
			return 'home';
		}

		else if (is_404()) {
			return '404';
		}

		$page_id = get_the_ID();

		if ($page_id) {
			$page = get_post( $page_id, ARRAY_A );

			return $page['post_name'];
		}

		return '';
	}

// class end
}