<?php
/**
 * payssion thaiqr_th payment method class
 *
 */
require_once('payssion.php');

class payssion_thaiqr_th extends payssion {
	var $description = "Thai QR";
	
	function payssion_thaiqr_th() {
		$this->title = "Thai QR";
		parent::__construct();
	}
}