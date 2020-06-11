<?php

require_once("../../override/controllers/admin/exports/Export.php");

class webequip_beezup extends Module {

	/**
	* Infos module
	**/
	public function __construct() {

		$this->name = 'webequip_beezup';
		$this->tab = 'others';
		$this->version = '1.0';
		$this->author = 'Web-equip';
		$this->bootstrap = true;
		
		parent::__construct();
		
		$this->displayName = $this->l('Webequip Export Beezup');
		$this->description = $this->l('Export automatique du flux Beezup');
	}

	public function cronTask() {

		$directory = configuration::get(AdminBeezupController::CONFIG_DIRECTORY);

		// Supprimer la sauvegarde précédente
		@unlink(_PS_ROOT_DIR_.$directory.AdminBeezupController::CONFIG_ARCHIVE_PREFIX.AdminBeezupController::CONFIG_FILE);

		// Sauvegarder le flux actuel
		@rename(_PS_ROOT_DIR_.$directory.AdminBeezupController::CONFIG_FILE, _PS_ROOT_DIR_.$directory.AdminBeezupController::CONFIG_ARCHIVE_PREFIX.AdminBeezupController::CONFIG_FILE);

		// Générer le nouveau flux
		$export = new ExportBeezup();

		$fp = fopen(_PS_ROOT_DIR_.$directory.AdminBeezupController::CONFIG_FILE, "w+");
		foreach($export->getLines() as $line)
			fputcsv($fp, $line, ";");

		fclose($fp);
	}
}