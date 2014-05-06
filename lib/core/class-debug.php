<?php

class CP_Debug {
	
	function __construct() {
		$this->_init();
	}

	private function _init() {
		// load dBug
		CP::load_library(CP_PATH.'/lib/dBug/dBug.php');
	}
}