<?php
/**
 * payssion payco_kr payment method class
 *
 */
require_once('payssion.php');

class payssion_payco_kr extends payssion {
	var $description = "PAYCO";
	
	function payssion_payco_kr() {
		$this->title = "PAYCO";
		parent::__construct();
	}
}