<?php

include_once('../config/config.inc.php');
include_once('../modules/webequip_transfer/webequip_transfer.php');

$module = new webequip_transfer();
$module->transfer_ps_employee();

die('THE END');