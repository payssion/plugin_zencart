<?php
/**
 * payssion webpay payment method class
 *
 */
require_once('payssion.php');

class payssion_webpay_cl extends payssion {
	var $description = "WebPay";
	
	function payssion_webpay_cl() {
		$this->title = "WebPay";
		parent::__construct();
	}
}