<?php

class webequip_transfert extends Module {

	public static $configurations = array(
        'host' 	=> array('name'=>"WEBEQUIP_OLD_DB_HOST", "label"=>"Hôte", "type"=>"text"),
        'db' 	=> array('name'=>"WEBEQUIP_OLD_DB_NAME", "label"=>"BDD", "type"=>"text"),
        'login' => array('name'=>"WEBEQUIP_OLD_DB_LOGIN", "label"=>"Login", "type"=>"text"),
        'pwd' 	=> array('name'=>"WEBEQUIP_OLD_DB_PASSWORD", "label"=>"Mot de passe", "type"=>"password")
    );

	/**
	* Infos module
	**/
	public function __construct() {

		$this->name = 'webequip_transfert';
		$this->tab = 'others';
		$this->version = '1.0';
		$this->author = 'Web-equip';
		$this->bootstrap = true;

		parent::__construct();
		
		$this->displayName = $this->l('Webequip Transfert');
		$this->description = $this->l('Transfert des données depuis la version précédente de Prestashop');
	}

	/**
	* Gestion Back-Office
	**/
	public function getContent() {
		
		$this->handleAjax();

		$this->loadConfiguration();
		return $this->display(__FILE__, 'config.tpl');
	}

	/**
	* Gestion de la configuration
	**/
	public function loadConfiguration() {

		$is_configured = 1;
		foreach(self::$configurations as $x => $configuration) {

			// Modification
			if(Tools::getIsset($configuration['name']))
				Configuration::updateValue($configuration['name'], Tools::getValue($configuration['name']));

			// Chargement
			$data[$x] = $configuration;
			$data[$x]['value'] = Configuration::get($configuration['name']);

			if(!$data[$x]['value'])
				$is_configured = 0;
		}

		//$db = new PDO('mysql:host=5.196.77.240;dbname=webequip', 'webequip', 'WsirGoNIgFrURKuG');
		
		$this->context->smarty->assign('link', new Link());
		$this->context->smarty->assign('configs', $data);
		$this->context->smarty->assign('is_configured', $is_configured);
	}

	public function handleAjax() {
		if(Tools::getValue('ajax')) {

			$dbServerName 	= Configuration::get(self::$configurations['host']['name']);
			$dbUsername 	= Configuration::get(self::$configurations['login']['name']);
			$dbPassword 	= Configuration::get(self::$configurations['pwd']['name']);
			$dbName 		= Configuration::get(self::$configurations['db']['name']);

			// Test de la connexion 
			$conn = new mysqli($dbServerName, $dbUsername, $dbPassword, $dbName);
			if ($conn->connect_error)
    			die("<div class='alert alert-danger'>".$conn->connect_error."</div>");

    		// Gestion des transfert
			switch (Tools::getValue('action')) {
					
				case 'load_transfert':
					die('ok');
				break;

			}	
		}
	}

}