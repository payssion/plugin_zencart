<?php
/**
 * payssion ssgpay_kr payment method class
 *
 */
require_once('payssion.php');

class payssion_ssgpay_kr extends payssion {
	var $description = "SSG Pay";
	
	function payssion_ssgpay_kr() {
		$this->title = "SSG Pay";
		parent::__construct();
	}
}