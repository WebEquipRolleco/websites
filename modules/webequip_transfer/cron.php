<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/webequip_transfer.php');

	$module = new webequip_transfer();

	$actions = explode('|', Tools::getValue('action'));
	$min_id = Tools::getValue('min_id', null);

	foreach($actions as $action)
		switch($action) {

			case 'CUSTOMER':
				$nb = $module->transfer_ps_customer($min_id);
				echo "<div><b>$nb</b> clients importés</div>";
			break;
			
			case 'ADDRESS':
				$nb = $module->transfer_ps_address($min_id);
				echo "<div><b>$nb</b> adresses importées</div>";
			break;

			case 'ORDER':
				$nb = $module->transfer_ps_orders($min_id);
				echo "<div><b>$nb</b> commandes importées</div>";
			break;

			case 'ORDER_DETAIL':
				$nb = $module->transfer_ps_order_detail($min_id);
				echo "<div><b>$nb</b> détails commandes importés</div>";
			break;

			case 'ORDER_HISTORY':
				$nb = $module->transfer_ps_order_history($min_id);
				echo "<div><b>$nb</b> historiques de commande importés</div>";
			break;

			case 'QUOTATION':
				$nb = $module->transfer_ps_activis_devis($min_id);
				echo "<div><b>$nb</b> devis importés</div>";
			break;

			case 'QUOTATION_LINE':
				$nb = $module->transfer_ps_activis_devis_line($min_id);
				echo "<div><b>$nb</b> lignes devis importés</div>";
			break;

			case 'OLD_CUSTOMERS':
				$nb = $module->updateOldCustomers();
				echo "<div><b>$nb</b> clients tranférés</div>";
			break;

			case 'OLD_ORDERS':
				$nb = $module->updateOldOrders();
				echo "<div><b>$nb</b> commandes transférées</div>";
			break;

			case 'OLD_CARTS':
				$nb = $module->updateOldCarts();
				echo "<div><b>$nb</b> paniers transférés</div>";
			break;

		}

die("END");