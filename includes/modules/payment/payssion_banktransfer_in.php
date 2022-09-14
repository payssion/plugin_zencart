<?php
/**
 * payssion banktransfer_in payment method class
 *
 */
require_once('payssion.php');

class payssion_banktransfer_in extends payssion {
	var $description = "Indian Bank Transfer";
	
	function payssion_banktransfer_in() {
		$this->title = "Indian Bank Transfer";
		parent::__construct();
	}
}