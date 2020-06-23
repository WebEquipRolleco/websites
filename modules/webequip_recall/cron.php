<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/webequip_recall.php');


	$module = new webequip_recall();
	$module->cronTask();

	Configuration::updateValue('RECALL_CRON_LAST_DATE', date('d/m/Y H:i'));