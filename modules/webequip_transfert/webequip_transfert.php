<?php

class webequip_transfert extends Module {

	private $old_db;

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
	* Connexion à l'ancienne BDD
	**/
	public function connectToDB() {

		$dbServerName 	= Configuration::get(self::$configurations['host']['name']);
		$dbUsername 	= Configuration::get(self::$configurations['login']['name']);
		$dbPassword 	= Configuration::get(self::$configurations['pwd']['name']);
		$dbName 		= Configuration::get(self::$configurations['db']['name']);

		$this->old_db = new mysqli($dbServerName, $dbUsername, $dbPassword, $dbName);
		if($old_db->connect_error)
    		die("<div class='alert alert-danger'>".$old_db->connect_error."</div>");
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
		
		$this->context->smarty->assign('link', new Link());
		$this->context->smarty->assign('configs', $data);
		$this->context->smarty->assign('is_configured', $is_configured);
	}

	/**
	* Retourne la liste des données pouvant être transférées
	* @return array
	**/
	private function getTransfertList() {
		
		$data['ps_address'] = array('name'=>"Adresses", 'lang'=>false, 'shop'=>false);
		$data['ps_supplier'] = array('name'=>"Fournisseurs", 'lang'=>true, 'shop'=>true);
		$data['ps_manufacturer'] = array('name'=>"Marques", 'lang'=>true, 'shop'=>true);

		return $data;
	}

	/**
	* Assigne les données de la preview
	**/
	private function getTransfertPreview() {

		$this->connectToDB();
		$table = Tools::getValue('transfert_name');
		$infos = $this->getTransfertList()[$table];

		$query = "SELECT COUNT(*) AS nb FROM ".$table;
		$result = $this->old_db->query($query);

		$data[0][] = $infos['name'];
		$data[0][] = $result->fetch_object()->nb;
		$data[0][] = Db::getInstance()->getValue($query);

		if($infos['lang']) {
			$query = "SELECT COUNT(*) AS nb FROM ".$table."_lang";
			$result = $this->old_db->query($query);

			$data[1][] = "Gestion des langues";
			$data[1][] = $result->fetch_object()->nb;
			$data[1][] = Db::getInstance()->getValue($query);
		}

		if($infos['shop']) {
			$query = "SELECT COUNT(*) AS nb FROM ".$table."_shop";
			$result = $this->old_db->query($query);

			$data[2][] = "Gestion des boutiques";
			$data[2][] = $result->fetch_object()->nb;
			$data[2][] = Db::getInstance()->getValue($query);
		}

		return $data;
	}

	/**
	* Gestion du choix de transfert de données
	**/
	public function handleAjax() {
		if(Tools::getValue('ajax')) {

			// Test de la connexion 
			$this->connectToDB();

    		// Gestion des transfert
			switch (Tools::getValue('action')) {
					
				case 'load_transfert':
					$this->context->smarty->assign('data_list', $this->getTransfertList());
					die($this->display(__FILE__, 'transfer_form.tpl'));
				break;

				case 'load_preview':
					$this->context->smarty->assign('data_list', $this->getTransfertPreview());
					$this->context->smarty->assign('transfert_name', Tools::getValue('transfert_name'));
					die($this->display(__FILE__, 'transfer_preview.tpl'));
				break;

				case 'load_data':
					$method = "transfer_".Tools::getValue('transfert_name');
					$this->{$method}();
					die("<div class='alert alert-success'>Transfert terminé</div>");
				break;
			}	
		}
	}

	/**
	* Transfert des fournisseurs
	**/
	private function transfer_ps_supplier() {

		$this->connectToDB();

		Db::getInstance()->execute("DELETE FROM ps_supplier");
		Db::getInstance()->execute("DELETE FROM ps_supplier_lang");
		Db::getInstance()->execute("DELETE FROM ps_supplier_shop");

		$result = $this->old_db->query("SELECT * FROM ps_supplier");
		while($row = $result->fetch_assoc()) {

			$split = explode('-', $row['name']);
			if(count($split) == 2)
				Db::getInstance()->execute("INSERT INTO ps_supplier VALUES(".$row['id_supplier'].", '".trim($split[0])."', '".trim($split[1])."', '".$row['emails']."', NULL, '".$row['date_add']."', '".$row['date_upd']."', ".$row['active'].", ".$row['BC'].", ".$row['BL'].")");
			else
				Db::getInstance()->execute("INSERT INTO ps_supplier VALUES(".$row['id_supplier'].", NULL, '".$row['name']."', '".$row['emails']."', NULL, '".$row['date_add']."', '".$row['date_upd']."', ".$row['active'].", ".$row['BC'].", ".$row['BL'].")");
		}

		$result = $this->old_db->query("SELECT * FROM ps_supplier_lang");
		while($row = $result->fetch_assoc())
			Db::getInstance()->execute("INSERT INTO ps_supplier_lang VALUES(".$row['id_supplier'].", ".$row['id_lang'].", '".pSql($row['description'])."', '".$row['meta_title']."', '".$row['meta_keywords']."', '".$row['meta_description']."')");

		$result = $this->old_db->query("SELECT * FROM ps_supplier_shop");
		while($row = $result->fetch_assoc()) {
			Db::getInstance()->execute("INSERT INTO ps_supplier_shop VALUES(".$row['id_supplier'].", ".$row['id_shop'].")");
		}
	}

	/**
	* Transfert des marques
	**/
	private function transfer_ps_manufacturer() {

		$this->connectToDB();

		Db::getInstance()->execute("DELETE FROM ps_manufacturer");
		Db::getInstance()->execute("DELETE FROM ps_manufacturer_lang");
		Db::getInstance()->execute("DELETE FROM ps_manufacturer_shop");

		$result = $this->old_db->query("SELECT * FROM ps_manufacturer");
		while($row = $result->fetch_assoc())
			Db::getInstance()->execute("INSERT INTO ps_manufacturer VALUES(".$row['id_manufacturer'].", '".$row['name']."', '".$row['date_add']."', '".$row['date_upd']."', ".$row['active'].")");

		$result = $this->old_db->query("SELECT * FROM ps_manufacturer_lang");
		while($row = $result->fetch_assoc())
			Db::getInstance()->execute("INSERT INTO ps_manufacturer_lang VALUES(".$row['id_manufacturer'].", ".$row['id_lang'].", '".pSql($row['description'])."', '".pSql($row['short_description'])."', '".$row['meta_title']."', '".$row['meta_keywords']."', '".$row['meta_description']."')");

		$result = $this->old_db->query("SELECT * FROM ps_manufacturer_shop");
		while($row = $result->fetch_assoc())
			Db::getInstance()->execute("INSERT INTO ps_manufacturer_shop VALUES(".$row['id_manufacturer'].", ".$row['id_shop'].")");
	}

	/**
	* Transfert des adresses
	**/
	private function transfer_ps_address() {

		$this->connectToDB();

		Db::getInstance()->execute("DELETE FROM ps_address");
		$result = $this->old_db->query("SELECT * FROM ps_address");
		while($row = $result->fetch_assoc())
			Db::getInstance()->execute("INSERT INTO ps_address VALUES(".$row['id_address'].", ".$row['id_country'].", ".$row['id_state'].", ".$row['id_customer'].", ".$row['id_manufacturer'].", ".$row['id_supplier'].", ".$row['id_warehouse'].", '".pSql($row['alias'])."', '".pSql($row['company'])."', '".pSql($row['lastname'])."', '".pSql($row['firstname'])."', '".pSql($row['address1'])."', '".pSql($row['address2'])."', '".$row['postcode']."', '".pSql($row['city'])."', '".pSql($row['other'])."', '".$row['phone']."', '".$row['phone_mobile']."', '".$row['vat_number']."', '".$row['dni']."', '".$row['date_add']."', '".$row['date_upd']."', ".$row['active'].", ".$row['deleted'].")");
	}
}