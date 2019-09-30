<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/supplierrecall.php');

if (Tools::getIsset('secure_key'))
{
	$secure_key = Configuration::get(SupplierRecall::SECURE_KEY);
	if (!empty($secure_key) && $secure_key === Tools::getValue('secure_key')) {
		$module = new SupplierRecall();
		$module->cronTask();
	}
	else die("Clé de sécurité incorrecte");
}
else die("Clé de sécurité incorrecte");