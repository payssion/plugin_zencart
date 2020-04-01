<?php
require_once('payssion.php');

class payssion_ebanking_za extends payssion {
	var $description = "South Africa Internet Banking";
	
	function payssion_ebanking_za() {
		$this->title = "South Africa Internet Banking";
		parent::__construct();
	}
}