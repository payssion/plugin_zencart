<?php
require_once('payssion.php');

class payssion_ebanking_kr extends payssion {
	var $description = "South Korea Internet Banking";
	
	function payssion_ebanking_kr() {
		$this->title = "South Korea Internet Banking";
		parent::__construct();
	}
}