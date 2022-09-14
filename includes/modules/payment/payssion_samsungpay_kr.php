<?php
/**
 * payssion samsungpay_kr payment method class
 *
 */
require_once('payssion.php');

class payssion_samsungpay_kr extends payssion {
	var $description = "Samsung Pay";
	
	function payssion_samsungpay_kr() {
		$this->title = "Samsung Pay";
		parent::__construct();
	}
}