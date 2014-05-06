<?php

class CP_Footer {
	
	function __construct() {
		$this->_init();
	}

	private function _init() {

	}

	public function show_footer() {
		global $CP_Smarty;
		
		ob_start();
		wp_footer();
		
		$footer = ob_get_clean();
		$footer = str_replace("\n", "\n\t", $footer);
		
		$CP_Smarty->smarty->assign('footer', $footer);
		$footer = $CP_Smarty->smarty->display('_footer.html');
		
		echo $footer."\n";
	}
}