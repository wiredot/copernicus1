<?php

/**
 * My Story user meta box
 *
 * @package My Story
 * @subpackage My Story Theme
 * @author Piotr Soluch
 */

/**
 * user meta box class
 *
 * @package My Story
 * @subpackage My Story Theme
 * @author Piotr Soluch
 */
class CP_Umb {

	var $umb = array();

	/**
	 * Class constructor
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function __construct() {

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {

			if ( isset( CP::$config['umb'] ) ) {
				$this->umb = CP::$config['umb'];

				// create taxonomies
				add_action( 'edit_user_profile', array( $this, 'add_user_meta_boxes' ) );
				add_action( 'show_user_profile', array( $this, 'add_user_meta_boxes' ) );

				add_action( 'profile_update', array( $this, 'update_user_meta' ) );
				//add_action('show_user_profile', array($this, 'add_user_meta_boxes'));
			}
		}
	}

	/**
	 * Start adding user meta boxes
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @author Piotr Soluch
	 */
	public function add_user_meta_boxes( $user ) {

		// if there are meta boxes
		if ( is_array( $this->umb ) ) {

			// for each meta box
			foreach ( $this->umb as $key => $umb ) {
				$umb['key'] = $key;
				// if meta box is active
				if ( $umb['active'] && isset( $umb['fields'] ) && is_array( $umb['fields'] ) ) {
					// create meta box groups
					$this->add_user_meta_box_group( $umb, $user->ID );
				}
			}
		}
	}

	/**
	 * Add user meta box group
	 *
	 * @access type public
	 * @return type mixed returns possible errors
	 * @param $umb array all details of user meta box
	 * @author Piotr Soluch
	 */
	public function add_user_meta_box_group( $umb, $user_id ) {
		global $CP_Field;
		$return = '<div id="umb_' . $umb['key'] . '">';
		$return .= '<hr><h3>' . $umb['name'] . '</h3><table class="form-table"><tbody>';

		foreach ( $umb['fields'] as $key => $field ) {
			$return .= '<tr><th>';
			$return .= '<label for="cp_user_meta_' . $key . '">' . $field['name'] . '</label></th><td>';
			$return .= $CP_Field->show_field( $field, 'cp_user_meta_' . $key, 'cp_user_meta_' . $key, get_user_meta( $user_id, $key, true ) );

			$return .= '</td></tr>';
		}

		$return .= '</tbody></table>';
		$return .= '</div>';

		echo $return;
	}

	/**
	 *
	 */
	public function update_user_meta( $user_id ) {
		// if there are meta boxes
		if ( is_array( $this->umb ) ) {

			// for each meta box
			foreach ( $this->umb as $umb ) {

				// if meta box is active
				if ( $umb['active'] && isset( $umb['fields'] ) && is_array( $umb['fields'] ) ) {

					//new dBug($umb['fields']);
					foreach ( $umb['fields'] as $key => $field ) {

						if ( $field['type'] == 'upload' ) {
							$this->save_user_field_upload( $key, $user_id );
						} else {
							if ( isset( $_POST[ 'cp_user_meta_' . $key ] ) ) {
								update_user_meta( $user_id, $key, $_POST[ 'cp_user_meta_' . $key ] );
							} else {
								delete_user_meta( $user_id, $key );
							}
						}
					}
				}
			}
		}
	}

	public function save_user_field_upload( $key, $user_id ) {
		// Get the posted data
		$meta_value = ( isset( $_POST[ 'cp_user_meta_' . $key ] ) ? $_POST[ 'cp_user_meta_' . $key ] : '' );

		if ( $meta_value ) {
			update_user_meta( $user_id, $key, $meta_value['id'] );
		} else {
			delete_user_meta( $user_id, $key );
		}

		if ( isset( $meta_value['id'] ) ) {
			foreach ( $meta_value['id'] as $key => $id ) {
				$title = '';
				$caption = '';
				if ( isset( $meta_value['title'][ $key ] ) ) {
					$title = $meta_value['title'][ $key ];
				}
				if ( isset( $meta_value['caption'][ $key ] ) ) {
					$caption = $meta_value['caption'][ $key ];
				}

				wp_update_post(
					array(
						'ID' => $id,
						'post_title' => $title,
						'post_excerpt' => $caption,
					)
				);

				if ( isset( $meta_value['alt'][ $key ] ) ) {
					update_post_meta( $id, 'alt', $meta_value['alt'][ $key ] );
				} else {
					delete_post_meta( $id, 'alt' );
				}
			}
		}
	}

	// class end
}
