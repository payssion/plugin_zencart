<?php
/**
 * payssion afterpay payment method class
 *
 */
require_once('payssion.php');

class payssion_afterpay_us extends payssion {
	var $currency_available = array ("USD");
	var $description = "Afterpay";
	
	
	function payssion_afterpay_us() {
		$this->title = "Afterpay";
		parent::__construct();
	}
}