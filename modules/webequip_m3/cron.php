<?php

require_once('../../config/config.inc.php');
require_once('webequip_m3.php');

	$module = new Webequip_m3();

	switch(Tools::getValue('action')) {
		
		// Envoyer les nouvelles commandes vers M3
		case 'SEND_ORDERS':
			$module->sendOrders();
		break;
		
		// Vérifier les commandes validées par M3
		case 'GET_ORDER_NUMBER':
			$module->getOrderNumbers();
		break;

		// Vérifier les commandes à mettre à jour (statut M3)
		case 'GET_ORDER_CHANGES':
			$module->getNewStates();
		break;

		// Unknown use
		case 'LIST_ADDRESSES':
			$module->listAddresses(Tools::getValue('id_customer'));
		break;

		// TEST
		case 'TEST_URL':
			$module->testUrl(Tools::getValue('url'));
		break;
	}