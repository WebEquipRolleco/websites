<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/webequip_transfer.php');

	$module = new webequip_transfer();

	$actions = explode('|', Tools::getValue('action'));
	$min_id = Tools::getValue('min_id', null);
	
	foreach($actions as $action)
		switch () {

			case 'CUSTOMER':
				$module->transfer_ps_customer($min_id);
			break;
			
			case 'ADDRESS':
				$module->transfer_ps_address($min_id);
			break;

			case 'ORDER':
				$module->transfer_ps_orders($min_id);
			break;

			case 'ORDER_DETAIL':
				$module->transfer_ps_order_detail($min_id);
			break;

			case 'ORDER_HISTORY':
				$module->transfer_ps_order_history($min_id);
			break;

			case 'QUOTATION':
				$module->transfer_ps_activis_devis($min_id);
			break;

			case 'QUOTATION_LINE':
				$module->transfer_ps_activis_devis_line($min_id);
			break;
		}