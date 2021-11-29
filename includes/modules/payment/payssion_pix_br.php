<?php
/**
 * payssion PIX payment method class
 *
 */
require_once('payssion.php');

class payssion_pix_br extends payssion {
	var $description = "PIX";
	
	function payssion_pix_br() {
		$this->title = "PIX";
		parent::__construct();
	}
}