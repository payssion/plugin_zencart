<?php
require_once('payssion.php');

class payssion_bankcard_tr extends payssion {
	var $description = "Turkish Credit/Bank Card";
	
	function payssion_bankcard_tr() {
		$this->title = "Turkish Credit/Bank Card";
		parent::__construct();
	}
}