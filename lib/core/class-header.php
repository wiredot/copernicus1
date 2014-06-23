<?php

class CP_Header {
	
	function __construct() {
		$this->_init();
	}

	private function _init() {

	}

	public function show_header() {
		global $CP_Smarty, $CP_Language;

		$current_language = $CP_Language->get_current_language();

		ob_start();
		wp_head();
		
		$header = ob_get_clean();
		$header = str_replace("\n", "\n\t", $header);
		
		global $post;

		$page['image'] = null;
		$page['language'] = str_replace('-', '_', get_bloginfo('language'));
		$page['title'] = $this->get_page_title();
		$page['slug'] = $this->get_page_slug();
		$page['language'] = $current_language['iso'];
		
		if ($post) {
			$page['content'] = str_replace(array("\n","&nbsp;"), '', $post->post_content);
			
			if (has_post_thumbnail( $post->ID ) ) {
				$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
				$page['image'] = $image[0];
			}
		}
		else {
			$page['content'] = '';
		}
		
		$CP_Smarty->smarty->assign('header', $header);
		$CP_Smarty->smarty->assign('page', $page);
		$header = $CP_Smarty->smarty->fetch('_header.html');
		
		echo $header."\n";
	}

	private static function get_page_title() {
		global $page, $paged;

		$title = '';
		
		$title.= wp_title( '|', false, 'right' );

		// Add the blog name.
		$title.= get_bloginfo( 'name' );

		// Add the blog description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) )
			$title.= " | $site_description";

		// Add a page number if necessary:
		if ( $paged >= 2 || $page >= 2 )
			$title.= ' | ' . sprintf( __( 'Page %s', 'twentyeleven' ), max( $paged, $page ) );
		
		return $title;
	}

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
}
