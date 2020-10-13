<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/webequip_supplier.php');

if (Tools::getIsset('secure_key'))
{
	$secure_key = Configuration::get(webequip_supplier::SECURE_KEY);
	if (!empty($secure_key) && $secure_key === Tools::getValue('secure_key')) {
		$module = new webequip_supplier();
		$module->cronTask();
	}
	else die("Clé de sécurité incorrecte");
}
else die("Clé de sécurité incorrecte");