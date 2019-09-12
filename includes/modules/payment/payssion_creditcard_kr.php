<?php
require_once('payssion.php');

class payssion_creditcard_kr extends payssion {
	var $description = "South Korea Credit Card";
	
	function payssion_creditcard_kr() {
		$this->title = "South Korea Credit Card";
		parent::__construct();
	}
}