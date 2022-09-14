<?php
/**
 * payssion touchngo_my payment method class
 *
 */
require_once('payssion.php');

class payssion_touchngo_my extends payssion {
	var $description = "Touch 'N Go";
	
	function payssion_touchngo_my() {
		$this->title = "Touch 'N Go";
		parent::__construct();
	}
}