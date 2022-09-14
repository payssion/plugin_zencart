<?php
/**
 * payssion kakaopay_kr payment method class
 *
 */
require_once('payssion.php');

class payssion_kakaopay_kr extends payssion {
	var $description = "KakaoPay";
	
	function payssion_kakaopay_kr() {
		$this->title = "KakaoPay";
		parent::__construct();
	}
}