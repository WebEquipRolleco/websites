<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/webequip_transfer.php');

	$module = new webequip_transfer();

	$actions = explode('|', Tools::getValue('action'));
	$min_id = Tools::getValue('min_id', null);

	foreach($actions as $action)
		switch($action) {

			case 'customer':
				$nb = $module->transfer_ps_customer($min_id);
				echo "<div><b>$nb</b> clients importés</div>";
			break;
			
			case 'address':
				$nb = $module->transfer_ps_address($min_id);
				echo "<div><b>$nb</b> adresses importées</div>";
			break;

			case 'order':
				$nb = $module->transfer_ps_orders($min_id);
				echo "<div><b>$nb</b> commandes importées</div>";
			break;

			case 'order_detail':
				$nb = $module->transfer_ps_order_detail($min_id);
				echo "<div><b>$nb</b> détails commandes importés</div>";
			break;

			case 'order_history':
				$nb = $module->transfer_ps_order_history($min_id);
				echo "<div><b>$nb</b> historiques de commande importés</div>";
			break;

			case 'quotation':
				$nb = $module->transfer_ps_activis_devis($min_id);
				echo "<div><b>$nb</b> devis importés</div>";
			break;

			case 'quotation_line':
				$nb = $module->transfer_ps_activis_devis_line($min_id);
				echo "<div><b>$nb</b> lignes devis importés</div>";
			break;

			case 'old_customers':
				$nb = $module->updateOldCustomers();
				echo "<div><b>$nb</b> clients tranférés</div>";
			break;

			case 'old_orders':
				$nb = $module->updateOldOrders();
				echo "<div><b>$nb</b> commandes transférées</div>";
			break;

			case 'old_carts':
				$nb = $module->updateOldCarts();
				echo "<div><b>$nb</b> paniers transférés</div>";
			break;

		}

die("END");