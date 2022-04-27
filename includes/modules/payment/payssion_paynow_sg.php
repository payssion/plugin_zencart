<?php
/**
 * payssion paynow payment method class
 *
 */
require_once('payssion.php');

class payssion_paynow_sg extends payssion {
	var $description = "PayNow";
	
	
	function payssion_paynow_sg() {
		$this->title = "PayNow";
		parent::__construct();
	}
}