<?php

global $CP_Smarty;
// register the prefilter

function smarty_block_loop( $params, $content, $template, &$repeat ) {
	if ( ! $repeat ) {
		global $wp_query, $CP_Loop, $CP_Smarty, $post, $pages, $ccc;

		$main_post = $post;
		$main_pages = $pages;

		$return = '';
		$key = 0;
		$page_id = 0;
		if ( isset( $post->ID ) ) {
			$page_id = $post->ID;
		}

		if ( isset( $wp_query->query_vars['page'] ) && $wp_query->query_vars['page'] > 0 ) {
			$current_page = $wp_query->query_vars['page'];
		} else if ( isset( $wp_query->query_vars['paged'] ) && $wp_query->query_vars['paged'] > 0 ) {
			$current_page = $wp_query->query_vars['paged'];
		} else {
			$current_page = 1;
		}

		if ( isset( $params['name'] ) && $params['name'] ) {

			$loop = $CP_Loop->get_loop( $params['name'] );

			if ( isset( $params['template'] ) ) {
				$loop['template'] = $params['template'];
			}

			// if there are valid arguments parameters
			if ( isset( $params['args'] ) && is_array( $params['args'] ) ) {

				global $CP_Loop;
				$loop['args'] = $CP_Loop->merge_attributes( $params['args'], $loop['args'] );
			}

			if ( $loop ) {

				if ( isset( $loop['pages'] ) && $loop['pages'] ) {

					if ( $current_page ) {
						$loop['args']['paged'] = $current_page;
					}
				}

				$WP_loop = new WP_Query( $loop['args'] );
				$ccc = $WP_loop->post_count;
				//new dBug($WP_loop);

				while ( $WP_loop->have_posts() ) :
					$WP_loop->the_post();
					$CP_Smarty->smarty->assign( 'loop', $WP_loop );
					$CP_Smarty->smarty->assign( 'key', $key );
					$CP_Smarty->smarty->assign( 'page_id', $page_id );
					$CP_Smarty->smarty->assign( 'count', $WP_loop->post_count );
					$return .= $CP_Smarty->fetch( 'string:' . $content );
					$key++;
				endwhile;

				$return = apply_filters( 'cp_loop', $return );

				if ( isset( $loop['pages'] ) && $loop['pages'] && $WP_loop->max_num_pages > 1 ) {
					$return .= show_pagination( $WP_loop->max_num_pages, $current_page );
				}
			}
		} else {
			rewind_posts();
			while ( have_posts() ) :
				the_post();
				$CP_Smarty->smarty->assign( 'key', $key );
				$CP_Smarty->smarty->assign( 'post', $post );
				$CP_Smarty->smarty->assign( 'page_id', $page_id );
				if ( isset( $WP_loop ) ) {
					$CP_Smarty->smarty->assign( 'count', $WP_loop->post_count );
				} else {
					$CP_Smarty->smarty->assign( 'count', 0 );
				}
				$return .= $CP_Smarty->fetch( 'string:' . $content );
				$key++;
			endwhile;

			global $wp_query;

			if ( ! isset( $params['pages'] ) ) {
				$params['pages'] = false;
			}

			if ( $params['pages'] && $wp_query->max_num_pages > 1 ) {
				$return .= show_pagination( $wp_query->max_num_pages, $current_page );
			}
		}

		$post = $main_post;
		$pages = $main_pages;
		return $return;
	}
}

function show_pagination( $pages = 0, $current_page = 1 ) {
	global $wp_query;

	$pagination = '';

	$page_url = $_SERVER['REQUEST_URI'];
	$page_url = preg_replace( '/\/page\/[0-9]+\//', '/', $page_url );

	if ( $pages ) {

		$pagination .= '<ul class="pagination">';

		if ( $current_page > 1 ) {
			if ( $current_page > 2 ) {
				$pagination .= '<li><a href="' . $page_url . 'page/' . ($current_page - 1) . '/">«</a></li>';
			} else {
				$pagination .= '<li><a href="' . $page_url . '">«</a></li>';
			}
		}

		$separator_bottom = false;
		$separator_top = false;

		for ( $i = 1; $i <= $pages; $i++ ) {
			if ( $i == 1 ) {
				$pagination .= '<li><a href="' . $page_url . '"';
				if ( $current_page == 1 ) {
					$pagination .= ' class="active"';
				}
				$pagination .= '>1</a></li>';
			} else {
				if ( $i < 4 || ($i < ($current_page + 3) && $i > ($current_page - 3)) || $i > ($pages - 3) ) {
					$pagination .= '<li><a href="' . $page_url . 'page/' . ($i) . '/"';
					if ( $current_page == ($i) ) {
						$pagination .= ' class="active"';
					}
					$pagination .= '>' . ($i) . '</a></li>';
				} else {
					if ( $i < $current_page && ! $separator_bottom ) {
						$separator_bottom = true;
						$pagination .= '<li>...</li>';
					} else if ( $i > $current_page && ! $separator_top ) {
						$separator_top = true;
						$pagination .= '<li>...</li>';
					}
				}
			}
		}

		if ( $current_page < $pages ) {
			$pagination .= '<li><a href="' . $page_url . 'page/' . ($current_page + 1) . '/">»</a></li>';
		}

		$pagination .= '</ul>';
	}

	return $pagination;
}
