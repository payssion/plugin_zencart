<?php
/**
 * payssion South Africa Credit Card payment method class
 *
 */
require_once('payssion.php');

class payssion_creditcard_za extends payssion {
	var $description = "South Africa Credit Card";
	
	
	function payssion_creditcard_za() {
		$this->title = "South Africa Credit Card";
		parent::__construct();
	}
}