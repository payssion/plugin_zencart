<?php
/**
 * payssion PagoEfectivo payment method class
 *
 */
require_once('payssion.php');

class payssion_pagoefectivo_pe extends payssion {
	var $description = "PagoEfectivo";
	
	function payssion_pagoefectivo_pe() {
		$this->title = "PagoEfectivo";
		parent::__construct();
	}
}