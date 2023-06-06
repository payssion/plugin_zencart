<?php
/**
 * payssion skrill payment method class
 *
 */
require_once('payssion.php');

class payssion_skrill extends payssion {
	var $description = "Skrill";
	
	function payssion_skrill() {
		$this->title = "Skrill";
		parent::__construct();
	}
}