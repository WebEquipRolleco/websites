<?php

require_once("exports/Export.php");

class AdminBeezupControllerCore extends AdminController {

	const CONFIG_DIRECTORY = "BEEZUP_DIRECTORY";
	const CONFIG_IMG_FORMAT = "BEEZUP_IMG_FORMAT";
	const CONFIG_FILE = "beezup.csv";
	const CONFIG_ARCHIVE_PREFIX = "_";

	private $directory;

	/**
	* Activer Bootstrap
	**/
	public function __construct() {
        
        $this->bootstrap = true;
        $this->directory = configuration::get(self::CONFIG_DIRECTORY);

        parent::__construct();
    }

    /**
	* Récupère la configuration
	**/
	public function initContent() {

		parent::initContent();

		$files = array();
		if($this->directory) {

			// Vérification de l'arborescence
			$this->checkExportDirectory();

			foreach(glob(_PS_ROOT_DIR_.$this->directory."/*.csv") as $file_path) {
				
				$data = explode("/", $file_path);
				$name = end($data);

				$files[] = array('name'=>$name, 'path'=>$this->directory.$name, 'time'=>date('d/m/Y H:i', filemtime($file_path)));
			}
		}

		$formats[] = "cart";
		$formats[] = "home";
		$formats[] = "small";
		$formats[] = "medium";
		$formats[] = "large";

		$this->context->smarty->assign('files', $files);
		$this->context->smarty->assign('formats', $formats);
		$this->context->smarty->assign(self::CONFIG_DIRECTORY, configuration::get(self::CONFIG_DIRECTORY));
		$this->context->smarty->assign(self::CONFIG_IMG_FORMAT, Configuration::get(self::CONFIG_IMG_FORMAT));
	}

	/**
	* Enregistrer la configuration
	**/
	public function postProcess() {

		if(Tools::getIsset(self::CONFIG_IMG_FORMAT))
			Configuration::updateValue(self::CONFIG_IMG_FORMAT, Tools::getValue(self::CONFIG_IMG_FORMAT));

		if(Tools::getIsset(self::CONFIG_DIRECTORY)) {

			$value = Tools::getValue(self::CONFIG_DIRECTORY);
			if(substr($value, strlen($value) -1) != "/")
				$value = $value."/";

			Configuration::updateValue(self::CONFIG_DIRECTORY, $value);
		}

		if(Tools::isSubmit('manual_export')) {
			$this->context->smarty->assign('confirmation', "Les produits ont été exportés");
			$this->exportFile();
		}
	}

	/**
	* Vérifie l'existe du dossier d'export
	**/
	private function checkExportDirectory() {
		if(!is_dir(_PS_ROOT_DIR_.$this->directory))
			mkdir(_PS_ROOT_DIR_.$this->directory, 0777, true);
	}

	/**
	* Crée un fichier export
	**/
	public function exportFile() {
		if($this->directory) {

			// Vérification de l'arborescence
			$this->checkExportDirectory();
			
			// Supprimer la sauvegarde précédente
			@unlink(_PS_ROOT_DIR_.$this->directory.self::CONFIG_ARCHIVE_PREFIX.self::CONFIG_FILE);

			// Sauvegarder le flux actuel
			@rename(_PS_ROOT_DIR_.$this->directory.self::CONFIG_FILE, _PS_ROOT_DIR_.$this->directory.self::CONFIG_ARCHIVE_PREFIX.self::CONFIG_FILE);

			// Générer le nouveau flux
			$export = new ExportBeezup();

			file_put_contents(_PS_ROOT_DIR_.$this->directory.self::CONFIG_FILE, $export->getContent());
		}
	}

}