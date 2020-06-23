<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/webequip_transfer.php');

	$module = new webequip_transfer();

	$actions = explode('|', Tools::getValue('action'));
	foreach($actions as $action)
		switch () {

			case 'CUSTOMER':
				$module->transfer_ps_customer();
			break;
			
			case 'ADDRESS':
				$module->transfer_ps_address();
			break;

			case 'ORDER':
				$module->transfer_ps_orders();
			break;

			case 'ORDER_DETAIL':
				$module->transfer_ps_order_detail();
			break;

			case 'ORDER_HISTORY':
				$module->transfer_ps_order_history();
			break;

			case 'QUOTATION':
				$module->transfer_ps_activis_devis();
			break;

			case 'QUOTATION_LINE':
				$module->transfer_ps_activis_devis_line();
			break;
		}