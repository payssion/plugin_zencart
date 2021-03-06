<?php

/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2011-2014 Payssion
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

use Respect\Validation\Rules\Uppercase;

require 'includes/application_top.php';
require 'includes/modules/payment/payssion.php';

function getStatusId($state) {
	$status = null;
	switch ($state) {
		case "completed":
			$status = MODULE_PAYMENT_PAYSSION_PAID_STATUS_ID;
			break;
		case "refunded":
		case "refund_pending":
		case "chargeback":
			$status = MODULE_PAYMENT_PAYSSION_REFUND_STATUS_ID;
			break;
		default:
			$status = MODULE_PAYMENT_PAYSSION_UNPAID_STATUS_ID;
			break;
	}

	return $status;
}

$state = $_POST['state'];
if (payssion::isValidNotify()) {
	$orders_id = $_POST['track_id'];
	$id = $db->Execute("select orders_id from " . TABLE_ORDERS . " where orders_id = " .  intval($orders_id));
	if(!$id->EOF) {
		// Send an empty HTTP 200 OK response to acknowledge receipt of the notification
		header('HTTP/1.1 200 OK');
		//handle payment notification
		$status_id = getStatusId($state);
		echo "success: $status_id";
		
		if (function_exists('zen_update_orders_history')) {
		    $message = 'Payssion Transaction ID: ' . $_POST['transaction_id'];
		    zen_update_orders_history(intval($orders_id), $message, null, $status_id, 0);
		} else {
		    $db->Execute("update ". TABLE_ORDERS. " set orders_status = " . $status_id . " where orders_id = ". intval($orders_id)); 
		}
	} else {
		header('HTTP/1.0 406 Not Acceptable');
		echo 'Error: order not found';
	}
} else {
	header('HTTP/1.0 406 Not Acceptable');
	echo "failed to check notify_sig";
}

