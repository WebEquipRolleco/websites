<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/webequip_beezup.php');

	switch (Tools::getValue('action')) {
		
		case 'EXPORT_BEEZUP':
			$module = new webequip_beezup();
			$module->cronTask();
		break;

	}