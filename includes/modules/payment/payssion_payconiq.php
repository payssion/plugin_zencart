<?php
require_once('payssion.php');

class payssion_payconiq extends payssion {
	var $description = "Payconiq";
	
	function payssion_payconiq() {
		$this->title = "Payconiq";
		parent::__construct();
	}
}