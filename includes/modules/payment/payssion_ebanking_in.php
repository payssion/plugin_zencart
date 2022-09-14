<?php
/**
 * payssion ebanking_in payment method class
 *
 */
require_once('payssion.php');

class payssion_ebanking_in extends payssion {
	var $description = "India Netbanking";
	
	function payssion_ebanking_in() {
		$this->title = "India Netbanking";
		parent::__construct();
	}
}