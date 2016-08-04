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

/**
 * payssion payment method class
 *
 */
class payssion extends base {
  /**
   * string repesenting the payment method
   *
   * @var string
   */
  var $code;

  /**
   * $description is a soft name for this payment method
   *
   * @var string
    */
  var $description = 'NO DESCRIPTION SET';
  /**
   * $enabled determines whether this module shows or not... in catalog.
   *
   * @var boolean
    */
  var $enabled;
  
  /**
   * $currency_available is the currencies this payment method supports
   *
   * @var array
   */
  var $currency_available = array();
  
  /**
   * $currency_check determines if the currency will be checked.
   *
   * @var boolean
   */
  var $currency_check = false;
  
  private $module_prefix;
  private $pm_id = '';
  private $pm_name = '';
  
  /**
   * $title is the displayed name for this payment method
   *
   * @var string
   */
  var $var;

  /**
    * constructor
    */
  public function __construct() {
    global $order, $messageStack;
    
    $class_name = get_class($this);
    $this->module_prefix = "MODULE_PAYMENT_" . strtoupper($class_name) . "_";
    
    $index = strpos($class_name, '_');
    if ($index) {
    	$this->pm_id = strtolower(substr($class_name, $index + 1));
    	if (!$this->pm_name) {
    		$pos_pm_name = strpos($this->pm_id, '_');
    		if ($pos_pm_name) {
    			$this->pm_name = strtolower(substr($this->pm_id, $pos_pm_name + 1));
    		} else {
    			$this->pm_name = $this->pm_id;
    		}
    	}
    }
    	
    $this->code = $class_name;
    try {
    	$this->sort_order = $this->getConst("SORT_ORDER");
    	if ((int)MODULE_PAYMENT_PAYSSION_PAID_STATUS_ID > 0) {
    		$this->order_status = MODULE_PAYMENT_PAYSSION_PAID_STATUS_ID;
    	}
    	if (is_object($order)) $this->update_status();
    	$this->enabled = (($this->getConst("STATUS") == 'True') ? true : false);
    	
    	if (!$this->pm_id) {
    		$this->enabled = false;
    		$this->title = "Payssion Main Module";
    		$this->description = "You must set up this main module before collecting payments via payssion.";
    	}
    	
    	if (!MODULE_PAYMENT_PAYSSION_API_KEY || !MODULE_PAYMENT_PAYSSION_SECRET_KEY) {
    		$this->enabled = false;
    	}
    }
    catch (Exception $e) {
    	$this->enabled = false;
    }

    if ((MODULE_PAYMENT_PAYSSION_TESTMODE == 'True')) {
    	$this->form_action_url = 'http://sandbox.payssion.com/payment/create.html';
    } else {
    	$this->form_action_url = 'https://www.payssion.com/payment/create.html';
    }
    
  }
  
  private function getConst($name) {
  	return constant($this->module_prefix . $name);
  }
  
  private function getConstKey($name) {
  	return $this->module_prefix . $name;
  }
  
  
  function  __get($name) {
  	if ($name == 'title') {
  		$dbg = debug_backtrace();
  
  		if (sizeof($dbg) == 1) {
  			if ($this->pm_id) {
  				return '&nbsp;&nbsp;&nbsp;&nbsp; -> ' . $this->var['title'] . ' (powered by Payssion)';
  			} else {
  				$title = $this->var['title'];
  				if (!MODULE_PAYMENT_PAYSSION_API_KEY || !MODULE_PAYMENT_PAYSSION_SECRET_KEY) {
  					$title .= '<b style="color: red"> (Main module is not configured)</b>';
  				}
  				return "<b>$title</b>";
  			}
  		} else {
  			$pm_name = $this->var['title'];
  			$pm_id = $this->pm_id;
  			$test_mode = '';
  			if ((MODULE_PAYMENT_PAYSSION_TESTMODE == 'True')) {
  				$test_mode = 'TESTMODE ';
  			}
  			return "$test_mode$pm_name<div style=\"text-align: center; margin: auto;\"><img src=\"payssion/images/pm/$pm_id.png\" alt=\"Checkout with $pm_name\" title=\"Checkout with $pm_name\" /></div>";
  		}
  	} else {
  		return isset($this->var[$name]) ? $this->var[$name] : null;
  	}
  }
  
  function __set($name, $value) {
  	$this->var[$name] = $value;
  }
  
  function __isset($name) {
  	return isset($this->var[$name]);
  }
  
  /**
   * calculate zone matches and flag settings to determine whether this module should display to customers or not
    *
    */
  function update_status() {
    global $order, $db;

    if ( ($this->enabled == true) && ((int)$this->getConst("ZONE") > 0) ) {
      $check_flag = false;
      $check_query = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . $this->getConst("ZONE") . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
      while (!$check_query->EOF) {
        if ($check_query->fields['zone_id'] < 1) {
          $check_flag = true;
          break;
        } elseif ($check_query->fields['zone_id'] == $order->billing['zone_id']) {
          $check_flag = true;
          break;
        }
        $check_query->MoveNext();
      }

      if ($check_flag == false) {
        $this->enabled = false;
      }
    }
  }
  /**
   * JS validation which does error-checking of data-entry if this module is selected for use
   * (Number, Owner, and CVV Lengths)
   *
   * @return string
    */
  function javascript_validation() {
    return false;
  }
  /**
   * Displays Credit Card Information Submission Fields on the Checkout Payment Page
   * In the case of payssion, this only displays the payssion title
   *
   * @return array
    */
  function selection() {
    return array('id' => $this->code,
                 'module' => $this->title);
  }
  /**
   * Normally evaluates the Credit Card Type for acceptance and the validity of the Credit Card Number & Expiration Date
   * Since payssion module is not collecting info, it simply skips this step.
   *
   * @return boolean
   */
  function pre_confirmation_check() {
  		global $order, $messageStack;
		$CUR = $order->info['currency'];
		if ($this->currency_check && !in_array($CUR, $this->currency_available)) {
			$currencies_support = implode($this->currency_available, ', ');
			$messageStack->add_session('checkout_payment', $this->var['title'] . " doesn't support $CUR currency. Please select $currencies_support currency.", 'error');
			zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
			return true;
		}
		return false;
  }
  /**
   * Display Credit Card Information on the Checkout Confirmation Page
   * Since none is collected for payssion before forwarding to payssion site, this is skipped
   *
   * @return boolean
    */
  function confirmation() {
    return false;
  }
  
  private function create_order(){
  	global $order, $db, $order_totals;
  	$order->info['payment_method'] = $this->pm_name;
  	$order->info['payment_module_code'] = $this->code;
  	$order->info['order_status'] = MODULE_PAYMENT_PAYSSION_UNPAID_STATUS_ID;
  
  	$order->info['currency'] = $_SESSION['currency'];
  
  	$orders_id = $order->create($order_totals, 2);
  	$_SESSION['orders_id'] = $orders_id;
  	$order->create_add_products($orders_id);
  }
  
  /**
   * Build the data and actions to process when the "Submit" button is pressed on the order-confirmation screen.
   * This sends the data to the payment gateway for processing.
   * (These are hidden fields on the checkout confirmation page)
   *
   * @return string
    */
  function process_button() {
  	global $db, $order, $currencies;
  	 
  	$this->create_order();
  	
  	//payssion accepted currency
  	$CUR = $order->info['currency'];
  	if ($this->currency_check && !in_array($CUR, $this->currency_available)) {
  		$CUR = 'USD';
  	}
  	 
  	$api_key = MODULE_PAYMENT_PAYSSION_API_KEY;
  	
  	$payer_name = $order->customer['firstname'] . $order->customer['lastname'];
  	$email = $order->customer['email_address'];
  	$total = $order->info['total'] * $currencies->get_value($CUR);
  	$amount = number_format($total, 2, '.', '');
  	$track_id = $_SESSION['orders_id'];
  	$sub_track_id = '';
  	$api_str = implode('|', array(
  			$api_key,
  			$this->pm_id,
  			$amount,
  			$CUR,
  			$track_id,
  			$sub_track_id,
  			MODULE_PAYMENT_PAYSSION_SECRET_KEY));
  	$api_sig = md5($api_str);
  	$description = STORE_NAME . " - Order #$track_id";
  	 
  	$process_button_string = zen_draw_hidden_field('api_key', $api_key) .
  	zen_draw_hidden_field('source', 'zencart') .
  	zen_draw_hidden_field('pm_id', $this->pm_id) .
  	zen_draw_hidden_field('amount', $amount) .
  	zen_draw_hidden_field('currency', $CUR) .
  	zen_draw_hidden_field('description', $description) .
  	zen_draw_hidden_field('payer_email', $email) .
  	zen_draw_hidden_field('payer_name', $payer_name) .
  	zen_draw_hidden_field('track_id', $track_id) .
  	zen_draw_hidden_field('sub_track_id', $sub_track_id) .
  	zen_draw_hidden_field('api_sig', $api_sig) .
  	zen_draw_hidden_field('language', 'en') .
  	zen_draw_hidden_field('notify_url', zen_href_link('payssion_notify.php', '', 'SSL', false, false)).
  	zen_draw_hidden_field('success_url', zen_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL')) .
  	zen_draw_hidden_field('fail_url', zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL')) .
  	zen_draw_hidden_field('country', $order->customer['country']['iso_code_2']);
  	
  	return $process_button_string;
  }
  /**
   * Store transaction info to the order and process any results that come back from the payment gateway
    *
    */
  function before_process() {
  	return false;
  }

  /**
   * Post-processing activities
   *
   * @return boolean
    */
  function after_process() {
    $_SESSION['order_created'] = '';
    return false;
  }
  /**
   * Used to display error message details
   *
   * @return boolean
    */
  function output_error() {
    return false;
  }
  /**
   * Check to see whether module is installed
   *
   * @return boolean
    */
  function check() {
    global $db;
    if (!isset($this->_check)) {
      $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = '" . $this->getConstKey("STATUS") . "'");
      $this->_check = $check_query->RecordCount();
    }
    return $this->_check;
  }
  /**
   * Install the payment module and its configuration settings
    *
    */
  function install() {
  	global $db;
  	$sort_order = 0;
  	if ($this->pm_id) {
  		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Payssion Module', '" . $this->getConstKey("STATUS") . "', 'True', 'Do you want to accept ". $this->pm_name . " payments?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
  		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', '" . $this->getConstKey("SORT_ORDER") . "', '0', 'Sort order of display. Lowest is displayed first.', '6', '1', now())");
  		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', '" . $this->getConstKey("ZONE") . "', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
  		//$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Title', '" . $this->getConstKey("TITLE") . "', '" . $this->pm_name . "', 'title', '6', '3', now())");
  	} else {
  		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Payssion Module', '" . $this->getConstKey("STATUS") . "', 'True', 'Do you want to accept ". $this->pm_name . " payments?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
  		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', '" . $this->getConstKey("SORT_ORDER") . "', '0', 'Sort order of display. Lowest is displayed first.', '6', '1', now())");
  		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('API Key', '" . $this->getConstKey("API_KEY") . "', '', 'Your Payssion App Name', '6', '2', now())");
  		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Secret Key', '" . $this->getConstKey("SECRET_KEY") . "', '', 'Your Payssion App API Key', '6', '0', now())");
  		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', '" . $this->getConstKey("ZONE") . "', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '3', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
  		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Unpaid Order Status', '" . $this->getConstKey("UNPAID_STATUS_ID") . "', '" . intval(DEFAULT_ORDERS_STATUS_ID) . "', 'Automatically set the status of unpaid orders to this value.', '6', '4', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
  		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Paid Order Status', '" . $this->getConstKey("PAID_STATUS_ID") . "', '2', 'Automatically set the status of paid orders to this value.', '6', '5', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
  		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Refund Order Status', '" . $this->getConstKey("REFUND_STATUS_ID") . "', '1', 'Automatically set the status of refund orders to this value.', '6', '6', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
  		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Testmode', '" . $this->getConstKey("TESTMODE") . "', 'False', 'Do you want to activate the Testmode? No real payments will be made and we will not collect any fees as well.', '6', '7', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
  	}
      }
  /**
   * Remove the module and all its settings
    *
    */
  function remove() {
    global $db;
    $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key LIKE  '%" . $this->getConstKey("") . "%'");
    $this->notify('NOTIFY_' . $this->getConstKey("UNINSTALLED"));
  }
  /**
   * Internal list of configuration keys used for configuration of the module
   *
   * @return array
    */
  function keys() {
  	if ($this->pm_id) {
  		return array(
  				$this->module_prefix . 'STATUS',
  				$this->module_prefix . 'SORT_ORDER',
  				$this->module_prefix . 'ZONE',
  				/*$this->module_prefix . 'TITLE'*/);
  	}
  	return array(
  			$this->module_prefix . 'STATUS',
  			$this->module_prefix . 'SORT_ORDER',
  			$this->module_prefix . 'API_KEY',
  			$this->module_prefix . 'SECRET_KEY',
  			$this->module_prefix . 'ZONE',
  			$this->module_prefix . 'UNPAID_STATUS_ID',
  			$this->module_prefix . 'PAID_STATUS_ID',
  			$this->module_prefix . 'REFUND_STATUS_ID',
  			$this->module_prefix . 'TESTMODE');
  }
  
  public static function isValidNotify() {
  	$apiKey = MODULE_PAYMENT_PAYSSION_API_KEY;
  	$secretKey = MODULE_PAYMENT_PAYSSION_SECRET_KEY;
  
  	// Assign payment notification values to local variables
  	$pm_id = $_POST['pm_id'];
  	$amount = $_POST['amount'];
  	$currency = $_POST['currency'];
  	$track_id = $_POST['track_id'];
  	$sub_track_id = $_POST['sub_track_id'];
  	$state = $_POST['state'];
  
  	$check_array = array(
  			$apiKey,
  			$pm_id,
  			$amount,
  			$currency,
  			$track_id,
  			$sub_track_id,
  			$state,
  			$secretKey
  	);
  	$check_msg = implode('|', $check_array);
  	$check_sig = md5($check_msg);
  	$notify_sig = $_POST['notify_sig'];
  	return ($notify_sig == $check_sig);
  }
}
