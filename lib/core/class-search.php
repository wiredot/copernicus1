<?php

class CP_Search {

	/**
	 * 
	 */
	public function __construct() {
		$this->_init();

		// initialize all plugins
		//$this->init_plugins();
	}

	/**
	 * 
	 */
	private function _init() {
		if (isset($_GET['s']) && !is_admin()) {

			//add_filter('post_limits', array($this,'custom_search_limits'));
			add_filter('posts_where', array($this,'custom_search_where'));
		}
	}

	/**
	 * 
	 * @global type $wpdb
	 * @global type $CP_Cpt
	 * @global type $CP_Mb
	 * @param type $where
	 * @return type
	 */
	function custom_search_where($where) {
		global $wpdb, $CP_Cpt, $CP_Mb;

		$term = $_GET['s'];
		if (!strpos($where, $term)) {
			return $where;
		}
		
		$where = "AND post_status = 'publish'";
		// post types
		$post_types = $CP_Cpt->get_post_types();

		$where.= " AND post_type IN('post', 'page'";
		foreach ($post_types as $type) {
			$where.= ", '".$type."'";
		}
		$where.= ")";
		$where.= "\n\n";
		// meta fields
		$fields = $CP_Mb->get_meta_box_fields();
		$where.= " AND (";
		$fieldsItems = count($fields);
		$j = 0;
		foreach ($fields as $key => $type) {
			$typeItems = count($type);
			$i = 0;
			foreach ($type as $tkey => $type_field) {
				
				$where.= " (SELECT count(*) FROM ".$wpdb->postmeta." WHERE post_id = ID AND meta_key = '".$type_field."' AND meta_value LIKE '%".$term."%') > 0 ";
				
				if(++$i != $typeItems) {
					$where.= ' OR ';
					$where.= "\n";
				}
			}
			
			if(++$j != $fieldsItems) {
				$where.= ' OR ';
				$where.= "\n\n";
			}
		}

		$where.= " OR post_title LIKE '%".$term."%'";

		$where.= ")";

	//	echo $where;

		return($where);
	}

	public function custom_search_limits($limit) {
		return 'LIMIT 99999';
	}

}