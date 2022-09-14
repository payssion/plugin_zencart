<?php
/**
 * payssion boost_my payment method class
 *
 */
require_once('payssion.php');

class payssion_boost_my extends payssion {
	var $description = "Boost";
	
	function payssion_boost_my() {
		$this->title = "Boost";
		parent::__construct();
	}
}