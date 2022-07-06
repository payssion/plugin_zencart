<?php
/**
 * payssion PromptPay payment method class
 *
 */
require_once('payssion.php');

class payssion_promptpay_th extends payssion {
	var $description = "PromptPay";
	
	function payssion_promptpay_th() {
		$this->title = "PromptPay";
		parent::__construct();
	}
}