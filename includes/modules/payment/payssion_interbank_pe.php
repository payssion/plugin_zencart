<?php
/**
 * payssion InterBank payment method class
 *
 */
require_once('payssion.php');

class payssion_interbank_pe extends payssion {
	var $description = "InterBank";
	
	function payssion_interbank_pe() {
		$this->title = "InterBank";
		parent::__construct();
	}
}