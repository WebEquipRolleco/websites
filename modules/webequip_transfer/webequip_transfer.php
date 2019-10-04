<?php

class webequip_transfer extends Module {

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

		$this->name = 'webequip_transfer';
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
		$data['ps_customer'] = array('name'=>"Clients", 'lang'=>false, 'shop'=>false);
		$data['ps_orders'] = array('name'=>"Commandes", 'lang'=>false, 'shop'=>false);
		$data['ps_order_detail'] = array('name'=>"Commandes : liste des produits", 'lang'=>false, 'shop'=>false);
		$data['ps_order_state'] = array('name'=>"Commandes : liste des états", 'lang'=>true, 'shop'=>false);
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
					ini_set("memory_limit", "-1");
					set_time_limit(0);

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

	/**
	* Transfert des clients
	**/
	private function transfer_ps_customer() {

		$this->connectToDB();

		Db::getInstance()->execute("DELETE FROM ps_customer");
		Db::getInstance()->execute("DELETE FROM ps_customer_group");

		$result = $this->old_db->query("SELECT c.*, (SELECT SUM(l.amount) FROM ps_activis_loyalty l WHERE c.id_customer = l.id_customer GROUP BY l.id_customer) AS rollcash FROM ps_customer c");
		while($row = $result->fetch_assoc())
			Db::getInstance()->execute("INSERT INTO ps_customer VALUES(
				".$row['id_customer'].",
				".$row['id_shop_group'].",
				".$row['id_shop'].",
				".$row['id_gender'].",
				".$row['id_default_group'].",
				".$row['id_lang'].",
				".$row['id_risk'].",
				'".pSql($row['reference_m3'])."',
				'".pSql($row['reference_chorus'])."',
				'".$row['tva']."',
				".$row['funding'].",
				'".$row['date_funding']."',
				1,
				NULL,
				NULL,
				'".pSql($row['company'])."',
				'".pSql($row['siret'])."',
				'".$row['ape']."',
				'".pSql($row['firstname'])."',
				'".pSql($row['lastname'])."',
				'".pSql($row['email'])."',
				'".$row['email_invoice']."',
				'".$row['email_tracking']."',
				".($row['rollcash'] ?? 0).",
				0,
				'".$row['passwd']."',
				'".$row['last_passwd_gen']."',
				'".$row['birthday']."',
				".$row['newsletter'].",
				'".$row['ip_registration_newsletter']."',
				'".$row['newsletter_date_add']."',
				".$row['optin'].",
				'".$row['website']."',
				".$row['outstanding_allow_amount'].",
				".($row['shop_public_prices'] ?? 0).",
				".$row['max_payment_days'].",
				'".$row['secure_key']."',
				'".pSql($row['note'])."',
				".$row['active'].",
				".$row['is_guest'].",
				".$row['deleted'].",
				'".$row['date_add']."',
				'".$row['date_upd']."',
				NULL,
				NULL
			)");

		$result = $this->old_db->query("SELECT * FROM ps_customer_group");
		while($row = $result->fetch_assoc())
			Db::getInstance()->execute("INSERT INTO ps_customer_group VALUES(".$row['id_customer'].", ".$row['id_group'].")");
	}

	/**
	* Transfert des commandes
	**/
	private function transfer_ps_orders() {

		$this->connectToDB();

		Db::getInstance()->execute("DELETE FROM ps_orders");
		Db::getInstance()->execute("DELETE FROM ps_order_history");

		$result = $this->old_db->query("SELECT * FROM ps_orders");
		while($row = $result->fetch_assoc()) 
			Db::getInstance()->execute("INSERT INTO ps_orders VALUES(
				".$row['id_order'].",
				'".$row['reference']."',
				'".pSql($row['internal_reference'])."', 
				NULL,
				NULL,
				".$row['id_shop_group'].",
				".$row['id_shop'].",
				".$row['id_carrier'].",
				".$row['id_lang'].",
				".$row['id_customer'].",
				".$row['id_cart'].",
				".$row['id_currency'].",
				".$row['id_address_delivery'].",
				".$row['id_address_invoice'].",
				".$row['current_state'].",
				'".$row['secure_key']."',
				'".$row['payment']."',
				".$row['conversion_rate'].",
				'".$row['module']."',
				".$row['recyclable'].",
				".$row['gift'].",
				'".$row['gift_message']."',
				".$row['mobile_theme'].",
				'".$row['shipping_number']."',
				".$row['total_discounts'].",
				".$row['total_discounts_tax_incl'].",
				".($row['total_discount_tax_excl'] ?? 0).",
				".$row['total_paid'].",
				".$row['total_paid_tax_incl'].",
				".$row['total_paid_tax_excl'].",
				".$row['total_paid_real'].",
				".$row['total_products'].",
				".$row['total_products_wt'].",
				".$row['total_shipping'].",
				".$row['total_shipping_tax_incl'].",
				".$row['total_shipping_tax_excl'].",
				".$row['carrier_tax_rate'].",
				".$row['total_wrapping'].",
				".$row['total_wrapping_tax_incl'].",
				".$row['total_wrapping_tax_excl'].",
				2,
				1,
				".$row['invoice_number'].",
				NULL,
				".$row['delivery_number'].",
				'".$row['invoice_date']."',
				'".$row['delivery_date']."',
				".$row['valid'].",
				0,
				0,
				'".$row['date_add']."',
				'".$row['date_upd']."'
			)");
	}

	/**
	* Transfert des détails de commande
	**/
	private function transfer_ps_order_detail() {

		$this->connectToDB();

		Db::getInstance()->execute("DELETE FROM ps_order_detail");
		$result = $this->old_db->query("SELECT * FROM ps_order_detail ORDER BY id_order_detail");
		while($row = $result->fetch_assoc())
			Db::getInstance()->execute("INSERT INTO ps_order_detail VALUES(
				".$row['id_order_detail'].",
				".$row['id_order'].",
				".($row['id_order_invoice'] ?? 0).",
				".$row['id_warehouse'].",
				".$row['id_shop'].",
				".$row['product_id'].",
				".($row['product_attribute_id'] ?? 0).",
				NULL,
				NULL,
				0,
				'".pSql($row['product_name'])."',
				".$row['product_quantity'].",
				".$row['product_quantity_in_stock'].",
				".$row['product_quantity_refunded'].",
				".$row['product_quantity_return'].",
				".$row['product_quantity_reinjected'].",
				".$row['product_price'].",
				".$row['reduction_percent'].",
				".$row['reduction_amount'].",
				".$row['reduction_amount_tax_incl'].",
				".$row['reduction_amount_tax_excl'].",
				".$row['group_reduction'].",
				".$row['product_quantity_discount'].",
				'".$row['product_ean13']."',
				'".$row['product_isbn']."',
				'".$row['product_upc']."',
				'".$row['product_reference']."',
				'".$row['product_supplier_reference']."',
				".$row['product_weight'].",
				0,
				".$row['tax_computation_method'].",
				'".$row['tax_name']."',
				".$row['tax_rate'].",
				".$row['ecotax'].",
				".$row['ecotax_tax_rate'].",
				".$row['discount_quantity_applied'].",
				'".$row['download_hash']."',
				".$row['download_nb'].",
				'".$row['download_deadline']."',
				".$row['total_price_tax_incl'].",
				".$row['total_price_tax_excl'].",
				".$row['unit_price_tax_incl'].",
				".$row['unit_price_tax_excl'].",
				".$row['total_shipping_price_tax_incl'].",
				".$row['total_shipping_price_tax_excl'].",
				".$row['purchase_supplier_price'].",
				".$row['original_product_price'].",
				0,
				NULL,
				NULL,
				'".trim(pSql($row['commentaire1']).' '.pSql($row['commentaire2']))."'
			)");
	}

	private function transfer_ps_order_state() {

		$this->connectToDB();

		Db::getInstance()->execute("DELETE FROM ps_order_state");
		Db::getInstance()->execute("DELETE FROM ps_order_state_lang");

		$result = $this->old_db->query("SELECT * FROM ps_order_state");
		while($row = $result->fetch_assoc())
			Db::getInstance()->execute("INSERT INTO ps_order_state VALUES(
				".$row['id_order_state'].",
				".$row['invoice'].",
				".$row['send_email'].",
				'".$row['module_name']."',
				'".$row['color']."',
				".$row['unremovable'].",
				".$row['hidden'].",
				".$row['logable'].",
				".$row['delivery'].",
				".$row['shipped'].",
				".$row['paid'].",
				".$row['proforma'].",
				0,
				0,
				0,
				0,
				".$row['deleted']."
			)");

		$result = $this->old_db->query("SELECT * FROM ps_order_state_lang");
		while($row = $result->fetch_assoc())
			Db::getInstance()->execute("INSERT INTO ps_order_state_lang VALUES(
				".$row['id_order_state'].",
				".$row['id_lang'].",
				'".pSql(utf8_encode($row['name']))."',
				'".$row['template']."'
			)");
	}
}