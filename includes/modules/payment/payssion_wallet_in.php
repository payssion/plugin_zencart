<?php
/**
 * payssion wallet_in payment method class
 *
 */
require_once('payssion.php');

class payssion_wallet_in extends payssion {
	var $description = "Indian Wallets";
	
	function payssion_wallet_in() {
		$this->title = "Indian Wallets";
		parent::__construct();
	}
}