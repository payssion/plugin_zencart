<?php
/**
 * payssion klarna payment method class
 *
 */
require_once('payssion.php');

class payssion_klarna extends payssion {
	var $description = "Klarna";
	
	function payssion_klarna() {
		$this->title = "Klarna";
		parent::__construct();
	}
}