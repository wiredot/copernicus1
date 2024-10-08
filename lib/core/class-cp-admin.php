<?php

/**
 * Admin class
 *
 * @package Copernicus
 * @author Piotr Soluch
 */

class CP_Admin {

	private $templates;
	private $theme;

	/**
	 *
	 */
	public function __construct() {

		$this->custom_media_upload();
		$this->theme = array(
			'version' => '',
		);

		if ( isset( CP::$config['theme'] ) ) {

			// get meta box configuration
			$this->theme = CP::$config['theme'];
		}

		// add js files in admin panel
		add_action( 'admin_init', array( $this, 'load_js' ) );

		// add css files in admin panel
		add_action( 'admin_init', array( $this, 'load_css' ) );

		add_filter( 'media_upload_tabs', array( $this, 'remove_gallery' ), 99 );

		add_action( 'add_meta_boxes', array( $this, 'wpse44966_add_meta_box' ) );
	}

	public function wpse44966_add_meta_box( $post_type ) {
		global $post, $wpdb;

		if ( $post->menu_order == 0 && ! isset( $_GET['post'] ) ) {

			$max_menu_order = $wpdb->get_var(
				'
				SELECT max(menu_order)
				FROM ' . $wpdb->posts . "
				WHERE post_type = '" . $post_type . "'
			"
			);

			$post->menu_order = $max_menu_order + 10;
		}
	}

	/**
	 *
	 */
	public function custom_media_upload() {
		if ( isset( $_GET['cmu'] ) ) {
			add_action( 'admin_print_footer_scripts', array( $this, 'header_f' ) );
		}
	}

	/**
	 *
	 */
	public function header_f() {
		$cmu = $_GET['cmu'];
		echo '<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery("tr.align").hide();
				jQuery("tr.url").hide();
				jQuery("tr.image-size").hide();
				jQuery("p.ml-submit").hide();
				jQuery("#url").parents("tr").hide();
				jQuery("a.del-link").hide();
				jQuery(".savesend input.button").val("add");
				jQuery("#go_button").val("add");
				
				window.old = window.updateMediaForm;
					
				window.updateMediaForm = function(html) {
					window.old(html);
					alert("asdasdas");
					jQuery("tr.align").hide();
					jQuery("tr.url").hide();
					jQuery("tr.image-size").hide();
					jQuery("p.ml-submit").hide();
					jQuery("#url").parents("tr").hide();
					jQuery("a.del-link").hide();
					jQuery(".savesend input.button").val("add");
					jQuery("#go_button").val("add");
				}

				jQuery(".savesend input.button").click(function($this){
					vvv = jQuery(this).attr(\'id\');
					vvv = vvv.replace("send[", "");
					vvv = vvv.replace("]", "");
					
					zzz = jQuery(this).parents("table").find("img.thumbnail.").attr(\'src\');
					alert(vvv + zzz);
					jQuery("#media_file", top.document).append("<img src=\""+zzz+"\" /><input type=\"hidden\" name=\"' . $cmu . '[]\" value=\""+vvv+"\" /> "+vvv+"");
					top.tb_remove();
					return false;
					exit;
				}
				);
			});
		</script>';
	}

	/**
	 *
	 */
	public function load_js() {
		// load main admin js file
		wp_register_script( 'cp_datepicker', CP_URL . 'static/datepicker/js/zebra_datepicker.js', array( 'jquery' ), $this->theme['version'], 1 );
		wp_enqueue_script( 'cp_datepicker' );

		wp_register_script( 'cp_admin', CP_URL . 'static/js/cp-admin.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ), $this->theme['version'], 1 );
		wp_enqueue_script( 'cp_admin' );
	}

	/**
	 *
	 */
	public function load_css() {
		wp_register_style( 'cp_admin', CP_URL . 'static/css/cp-admin.css', '', $this->theme['version'], 'all' );
		wp_enqueue_style( 'cp_admin' );

		wp_register_style( 'cp_datepicker', CP_URL . 'static/datepicker/css/metallic.css', '', $this->theme['version'], 'all' );
		wp_enqueue_style( 'cp_datepicker' );
	}

	/**
	 *
	 */
	public function remove_gallery( $array ) {
		unset( $array['gallery'] );
		return $array;
	}

	// class end
}
