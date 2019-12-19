<?php

class webequip_transfer extends Module {

	private $old_db;
	private $nb_rows;

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
	private function getTransferList() {
		
		$data['ps_address'] = array('name'=>"Adresses", 'lang'=>false, 'shop'=>false);
		$data['ps_customer'] = array('name'=>"Comptes : clients", 'lang'=>false, 'shop'=>false, 'updatable'=>true);
		$data['ps_employee'] = array('name'=>"Comptes : administration", 'lang'=>false, 'shop'=>false);
		$data['ps_orders'] = array('name'=>"Commandes", 'lang'=>false, 'shop'=>false);
		$data['ps_order_detail'] = array('name'=>"Commandes : liste des produits", 'lang'=>false, 'shop'=>false);
		$data['ps_order_state'] = array('name'=>"Commandes : liste des états", 'lang'=>true, 'shop'=>false);
		$data['ps_activis_devis'] = array('name'=>"Devis", 'lang'=>false, 'shop'=>false, 'new_table'=>_DB_PREFIX_.Quotation::TABLE_NAME, 'updatable'=>true);
		$data['ps_activis_devis_line'] = array('name'=>"Devis : liste des produits", 'lang'=>false, 'shop'=>false, 'new_table'=>_DB_PREFIX_.QuotationLine::TABLE_NAME, 'updatable'=>true);
		$data['ps_supplier'] = array('name'=>"Fournisseurs", 'lang'=>true, 'shop'=>true, 'updatable'=>true);
		$data['ps_manufacturer'] = array('name'=>"Marques", 'lang'=>true, 'shop'=>true, 'updatable'=>true);
		$data['ps_product'] = array('name'=>"Produits", 'lang'=>true, 'shop'=>true, 'updatable'=>true);
		$data['ps_feature'] = array('name'=>"Produits : liste des groupes d'attributs", 'lang'=>true, 'shop'=>true, 'new_table'=>'ps_attribute_group');
		$data['ps_feature_value'] = array('name'=>"Produits : liste des valeurs d'attributs", 'lang'=>true, 'shop'=>false, 'new_table'=>'ps_attribute');

		return $data;
	}

	/**
	* Assigne les données de la preview
	**/
	private function getTransferPreview() {

		$this->connectToDB();
		$table = Tools::getValue('transfer_name');
		$infos = $this->getTransferList()[$table];
		$new_table = isset($infos['new_table']) ? $infos['new_table'] : $table;

		$query = "SELECT COUNT(*) AS nb FROM ";
		$result = $this->old_db->query($query.$table);

		$data['updatable'] = $infos['updatable'] ?? false;

		$data['data'][0][] = $infos['name'];
		$data['data'][0][] = $result->fetch_object()->nb;
		$data['data'][0][] = Db::getInstance()->getValue($query.$new_table);

		if($infos['lang']) {
			$result = $this->old_db->query($query.$table."_lang");

			$data['data'][1][] = "Gestion des langues";
			$data['data'][1][] = $result->fetch_object()->nb;
			$data['data'][1][] = Db::getInstance()->getValue($query.$new_table."_lang");
		}

		if($infos['shop']) {
			$result = $this->old_db->query($query.$table."_shop");

			$data['data'][2][] = "Gestion des boutiques";
			$data['data'][2][] = $result->fetch_object()->nb;
			$data['data'][2][] = Db::getInstance()->getValue($query.$new_table."_shop");
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
					
				case 'load_transfer':
					$this->context->smarty->assign('data_list', $this->getTransferList());
					die($this->display(__FILE__, 'transfer_form.tpl'));
				break;

				case 'load_preview':
					$this->context->smarty->assign('data_list', $this->getTransferPreview());
					$this->context->smarty->assign('transfer_name', Tools::getValue('transfer_name'));
					die($this->display(__FILE__, 'transfer_preview.tpl'));
				break;

				case 'load_data':
					ini_set("memory_limit", "-1");
					set_time_limit(0);

					$this->nb_rows = 0;
					$method = "transfer_".Tools::getValue('transfer_name');
					$this->{$method}();

					if($this->nb_rows)
						die("<div class='alert alert-success'>Transfert terminé : ".$this->nb_rows." lignes importées</div>");
					else
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

		if(Tools::getValue('eraze')) {
			Db::getInstance()->execute("DELETE FROM ps_supplier");
			Db::getInstance()->execute("DELETE FROM ps_supplier_lang");
			Db::getInstance()->execute("DELETE FROM ps_supplier_shop");
		}
		else {
			$ids = Db::getInstance()->executeS("SELECT DISTINCT(id_supplier) FROM ps_supplier");
			$ids = array_map(function($e) { return $e['id_supplier']; }, $ids);
			$ids = trim(implode(",", $ids));
		}

		$sql = "SELECT * FROM ps_supplier";
		if(isset($ids) and $ids) $sql .= " WHERE id_supplier NOT IN ($ids)";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			$split = explode('-', $row['name']);
			if(count($split) == 2)
				Db::getInstance()->execute("INSERT INTO ps_supplier VALUES(".$row['id_supplier'].", '".trim($split[0])."', '".trim(pSql(utf8_encode($split[1])))."', '".$row['emails']."', NULL, '".$row['date_add']."', '".$row['date_upd']."', ".$row['active'].", ".$row['BC'].", ".$row['BL'].")");
			else
				Db::getInstance()->execute("INSERT INTO ps_supplier VALUES(".$row['id_supplier'].", NULL, '".pSql(utf8_encode($row['name']))."', '".$row['emails']."', NULL, '".$row['date_add']."', '".$row['date_upd']."', ".$row['active'].", ".$row['BC'].", ".$row['BL'].")");
		}

		$sql = "SELECT * FROM ps_supplier_lang";
		if(isset($ids) and $ids) $sql .= " WHERE id_supplier NOT IN ($ids)";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc())
			Db::getInstance()->execute("INSERT INTO ps_supplier_lang VALUES(".$row['id_supplier'].", ".$row['id_lang'].", '".pSql(utf8_encode($row['description']))."', '".$row['meta_title']."', '".$row['meta_keywords']."', '".$row['meta_description']."')");

		$sql = "SELECT * FROM ps_supplier_shop";
		if(isset($ids) and $ids) $sql .= " WHERE id_supplier NOT IN ($ids)";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {
			Db::getInstance()->execute("INSERT INTO ps_supplier_shop VALUES(".$row['id_supplier'].", ".$row['id_shop'].")");
		}
	}

	/**
	* Transfert des marques
	**/
	private function transfer_ps_manufacturer() {

		$this->connectToDB();

		if(Tools::getValue('eraze')) {
			Db::getInstance()->execute("DELETE FROM ps_manufacturer");
			Db::getInstance()->execute("DELETE FROM ps_manufacturer_lang");
			Db::getInstance()->execute("DELETE FROM ps_manufacturer_shop");
		}
		else {
			$ids = Db::getInstance()->executeS("SELECT DISTINCT(id_manufacturer) FROM id_manufacturer");
			$ids = array_map(function($e) { return $e['id_manufacturer']; }, $ids);
			$ids = trim(implode(",", $ids));	
		}

		$sql = "SELECT * FROM ps_manufacturer";
		if(isset($ids) and $ids) $sql .= " WHERE id_manufacturer NOT IN ($ids)";
		
		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc())
			Db::getInstance()->execute("INSERT INTO ps_manufacturer VALUES(".$row['id_manufacturer'].", '".pSql(utf8_encode($row['name']))."', '".$row['date_add']."', '".$row['date_upd']."', ".$row['active'].")");

		$sql = "SELECT * FROM ps_manufacturer_lang";
		if(isset($ids) and $ids) $sql .= " WHERE id_manufacturer NOT IN ($ids)";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc())
			Db::getInstance()->execute("INSERT INTO ps_manufacturer_lang VALUES(".$row['id_manufacturer'].", ".$row['id_lang'].", '".pSql(utf8_encode($row['description']))."', '".pSql($row['short_description'])."', '".$row['meta_title']."', '".$row['meta_keywords']."', '".$row['meta_description']."')");

		$sql = "SELECT * FROM ps_manufacturer_shop";
		if(isset($ids) and $ids) $sql .= " WHERE id_manufacturer NOT IN ($ids)";

		$result = $this->old_db->query($sql);
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
			Db::getInstance()->execute("INSERT INTO ps_address VALUES(".$row['id_address'].", ".$row['id_country'].", ".$row['id_state'].", ".$row['id_customer'].", ".$row['id_manufacturer'].", ".$row['id_supplier'].", ".$row['id_warehouse'].", '".pSql(utf8_encode($row['alias']))."', '".pSql(utf8_encode($row['company']))."', '".pSql(utf8_encode($row['lastname']))."', '".pSql(utf8_encode($row['firstname']))."', '".pSql(utf8_encode($row['address1']))."', '".pSql(utf8_encode($row['address2']))."', '".$row['postcode']."', '".pSql(utf8_encode($row['city']))."', '".pSql(utf8_encode($row['other']))."', '".$row['phone']."', '".$row['phone_mobile']."', '".$row['vat_number']."', '".$row['dni']."', '".$row['date_add']."', '".$row['date_upd']."', ".$row['active'].", ".$row['deleted'].")");
	}

	/**
	* Transfert des clients
	**/
	private function transfer_ps_customer() {

		$this->connectToDB();

		if(Tools::getValue('eraze')) {
			Db::getInstance()->execute("DELETE FROM ps_customer");
		}
		else {
			$ids = Db::getInstance()->executeS("SELECT id_customer FROM ps_customer");
			$ids = array_map(function($e) { return $e['id_customer']; }, $ids);
			$ids = trim(implode(',', $ids));
		}

		$sql = "SELECT c.*, (SELECT SUM(l.amount) FROM ps_activis_loyalty l WHERE c.id_customer = l.id_customer GROUP BY l.id_customer) AS rollcash FROM ps_customer c";
		if(isset($ids) and $ids) $sql .= " WHERE c.id_customer NOT IN ($ids)";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc())
			Db::getInstance()->execute("INSERT INTO ps_customer VALUES(
				".$row['id_customer'].",
				".$row['id_shop_group'].",
				".$row['id_shop'].",
				".$row['id_gender'].",
				".$row['id_default_group'].",
				".$row['id_lang'].",
				".$row['id_risk'].",
				'".pSql(utf8_encode($row['reference_m3']))."',
				'".pSql(utf8_encode($row['reference_chorus']))."',
				'".$row['tva']."',
				".$row['funding'].",
				'".$row['date_funding']."',
				1,
				NULL,
				NULL,
				'".pSql(utf8_encode($row['company']))."',
				'".pSql(utf8_encode($row['siret']))."',
				'".$row['ape']."',
				'".pSql(utf8_encode($row['firstname']))."',
				'".pSql(utf8_encode($row['lastname']))."',
				'".pSql(utf8_encode($row['email']))."',
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
				'".pSql(utf8_encode($row['note']))."',
				".$row['active'].",
				".$row['is_guest'].",
				".$row['deleted'].",
				'".$row['date_add']."',
				'".$row['date_upd']."',
				NULL,
				NULL
			)");

		Db::getInstance()->execute("DELETE FROM ps_customer_group");
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
				'".pSql(utf8_encode($row['internal_reference']))."', 
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
				'".pSql(utf8_encode($row['product_name']))."',
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
				'".trim(pSql(utf8_encode($row['commentaire1'])).' '.pSql(utf8_encode($row['commentaire2'])))."'
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

	/**
	* Transfert des groupes d'attributs
	**/
	private function transfer_ps_feature() {

		$this->connectToDB();

		Db::getInstance()->execute("DELETE FROM ps_attribute_group");
		Db::getInstance()->execute("DELETE FROM ps_attribute_group_lang");
		Db::getInstance()->execute("DELETE FROM ps_attribute_group_shop");

		$result = $this->old_db->query("SELECT * FROM ps_feature");
		while($row = $result->fetch_assoc())
			Db::getInstance()->execute("INSERT INTO ps_attribute_group VALUES(".$row['id_feature'].", 0, 0, 'select', ".$row['position'].")");

		$result = $this->old_db->query("SELECT * FROM ps_feature_lang");
		while($row = $result->fetch_assoc())
			Db::getInstance()->execute("INSERT INTO ps_attribute_group_lang VALUES(".$row['id_feature'].", ".$row['id_lang'].", '".pSql(utf8_encode($row['name']))."', '".pSql(utf8_encode($row['name']))."')");

		$result = $this->old_db->query("SELECT * FROM ps_feature_shop");
		while($row = $result->fetch_assoc())
			Db::getInstance()->execute("INSERT INTO ps_attribute_group_shop VALUES(".$row['id_feature'].", ".$row['id_shop'].")");
	}

	/**
	* Transfert des valeurs d'attributs
	**/
	private function transfer_ps_feature_value() {

		$this->connectToDB();

		Db::getInstance()->execute("DELETE FROM ps_attribute");
		Db::getInstance()->execute("DELETE FROM ps_attribute_lang");
		Db::getInstance()->execute("DELETE FROM ps_attribute_shop");

		$result = $this->old_db->query("SELECT * FROM ps_feature_value");
		while($row = $result->fetch_assoc()) {
			Db::getInstance()->execute("INSERT INTO ps_attribute VALUES(".$row['id_feature_value'].", ".$row['id_feature'].", '', 1)");
			Db::getInstance()->execute("INSERT INTO ps_attribute_shop VALUES(".$row['id_feature_value'].", 1)");
			Db::getInstance()->execute("INSERT INTO ps_attribute_shop VALUES(".$row['id_feature_value'].", 2)");
			Db::getInstance()->execute("INSERT INTO ps_attribute_shop VALUES(".$row['id_feature_value'].", 3)");
		}

		$result = $this->old_db->query("SELECT * FROM ps_feature_value_lang");
		while($row = $result->fetch_assoc())
			Db::getInstance()->execute("INSERT INTO ps_attribute_lang VALUES(".$row['id_feature_value'].", ".$row['id_lang'].", '".pSql(utf8_encode($row['value']))."')");
	}

	/**
	* Transfert des devis
	**/
	private function transfer_ps_activis_devis() {

		$this->connectToDB();

		if(Tools::getValue('eraze'))
			Db::getInstance()->execute("DELETE FROM "._DB_PREFIX_.Quotation::TABLE_NAME);
		else {

			$ids = Db::getInstance()->executeS("SELECT ".Quotation::TABLE_PRIMARY." FROM "._DB_PREFIX_.Quotation::TABLE_NAME);
			if($ids) {
				$ids = array_map(function($e) { return $e[Quotation::TABLE_PRIMARY]; }, $ids);
				$ids = trim(implode(',', $ids));
			}
		}

		$query = "SELECT * FROM ps_activis_devis d INNER JOIN ps_activis_devis_shop s ON (d.id_activis_devis = s.id_activis_devis)";
		if(isset($ids) and $ids) $query .= " WHERE d.id_activis_devis NOT IN ($ids)";
		$query .= "AND d.hash <> 'Deleted' GROUP BY d.id_activis_devis ORDER BY d.id_activis_devis DESC";

		$result = $this->old_db->query($query);
		while($row = $result->fetch_assoc()) {

			if($row['hash'] != "Deleted")
				Db::getInstance()->execute("INSERT INTO ps_quotation VALUES(
					".$row['id_activis_devis'].",
					'".pSql($row['hash'])."',
					".$row['id_state'].",
					".$row['id_customer'].",
					NULL,
					NULL,
					'".pSql(utf8_encode($row['email']))."',
					'".pSql(utf8_encode($row['mail_cc']))."',
					'".$row['date_add']."',
					'".$row['date_from']."',
					'".$row['date_to']."',
					'".$row['date_recall']."',
					'".pSql(utf8_encode($row['phone']))."',
					'".$row['fax']."',
					'".pSql(utf8_encode($row['comment']))."',
					'".pSql(utf8_encode($row['contact']))."',
					".$row['id_employee'].",
					".$row['active'].",
					0,
					0,
					NULL,
					".($row['id_shop'] ?? 1).",
					'".pSql($row['hash'])."'
				)");

			$this->nb_rows++;
		}
	}

	/**
	* Transfert des lignes produits pour les devis
	**/
	private function transfer_ps_activis_devis_line() {

		$this->connectToDB();

		if(Tools::getValue('eraze'))
			Db::getInstance()->execute("DELETE FROM "._DB_PREFIX_.QuotationLine::TABLE_NAME);
		else {

			$ids = Db::getInstance()->executeS("SELECT ".QuotationLine::TABLE_PRIMARY." FROM "._DB_PREFIX_.QuotationLine::TABLE_NAME);
			if($ids) {
				$ids = array_map(function($e) { return $e[QuotationLine::TABLE_PRIMARY]; }, $ids);
				$ids = trim(implode(',', $ids));
			}
		}

		$query = "SELECT * FROM ps_activis_devis_line";
		if(isset($ids) and $ids) $query .= " WHERE id_activis_devis_line NOT IN ($ids)";
		$query .= " ORDER BY id_activis_devis DESC";

		$result = $this->old_db->query($query);
		while($row = $result->fetch_assoc()) {

			Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_.QuotationLine::TABLE_NAME." VALUES(
				".$row['id_activis_devis_line'].",
				'".pSql(utf8_encode($row['reference']))."',
				NULL,
				'".pSql(utf8_encode($row['name']))."',
				'".pSql(utf8_encode($row['information']))."',
				NULL,
				".$row['devis_buying_price'].",
				0,
				".$row['devis_selling_price'].",
				".((float)$row['devis_dee'] + (float)$row['devis_m']).",
				".$row['quantity'].",
				1,
				".$row['position'].",
				".$row['id_activis_devis'].",
				NULL
			)");

			$this->nb_rows++;
		}
	}

	/**
	* Transfert des comptes employés
	**/
	private function transfer_ps_employee() {

		$this->connectToDB();

		Db::getInstance()->execute("DELETE FROM ps_employee");
		Db::getInstance()->execute("DELETE FROM ps_employee_shop");
		Db::getInstance()->execute("DELETE FROM ps_employee_supplier");

		$result = $this->old_db->query("SELECT * FROM ps_employee");
		while($row = $result->fetch_assoc()) {
			Db::getInstance()->execute("INSERT INTO ps_employee VALUES(
				".$row['id_employee'].",
				".$row['id_profile'].",
				".$row['id_lang'].",
				'".pSql(utf8_encode($row['lastname']))."',
				'".pSql(utf8_encode($row['firstname']))."',
				'".pSql(utf8_encode($row['email']))."',
				'".pSql($row['passwd'])."',
				'".pSql($row['last_passwd_gen'])."',
				'".pSql($row['stats_date_from'])."',
				'".pSql($row['stats_date_to'])."',
				NULL,
				NULL,
				1,
				NULL,
				'".pSql(utf8_encode($row['bo_color']))."',
				'".pSql(utf8_encode($row['bo_theme']))."',
				NULL,
				".$row['default_tab'].",
				".$row['bo_width'].",
				1,
				".$row['active'].",
				0,
				1,
				".$row['id_last_order'].",
				".$row['id_last_customer_message'].",
				".$row['id_last_customer'].",
				NULL,
				NULL,
				NULL
			)");

			Db::getInstance()->execute("INSERT INTO ps_employee_shop VALUES(".$row['id_employee'].", 1)");
			Db::getInstance()->execute("INSERT INTO ps_employee_shop VALUES(".$row['id_employee'].", 2)");
			Db::getInstance()->execute("INSERT INTO ps_employee_shop VALUES(".$row['id_employee'].", 3)");
		}

		$result = $this->old_db->query("SELECT * FROM ps_employee_supplier");
		while($row = $result->fetch_assoc())
			Db::getInstance()->execute("INSERT INTO ps_employee_supplier VALUES(".$row['id'].", ".$row['id_employee'].", ".$row['id_supplier'].")");
	}

	/**
	* Transfert des produits
	**/
	private function transfer_ps_product() {

		$this->connectToDB();

		if(Tools::getValue('eraze')) {
			Db::getInstance()->execute("DELETE FROM ps_product");
			Db::getInstance()->execute("DELETE FROM ps_product_shop");
			Db::getInstance()->execute("DELETE FROM ps_product_lang");
		}
		else {

			$ids = Db::getInstance()->executeS("SELECT id_product FROM ps_product");
			if($ids) {
				$ids = array_map(function($e) { return $e['id_product']; }, $ids);
				$ids = trim(implode(',', $ids));
			}
		}

		$sub_query = "SELECT DISTINCT(id_product_bundle) FROM ps_bundle";
		if(isset($ids) and $ids) $sub_query .= " WHERE id_product_bundle NOT IN ($ids)";

		$query = "SELECT * FROM ps_product WHERE id_product IN ($sub_query)";

		$result = $this->old_db->query($query);
		while($row = $result->fetch_assoc())
			Db::getInstance()->execute("INSERT INTO ps_product VALUES(
				".$row['id_product'].",
				".$row['id_supplier'].",
				".$row['id_manufacturer'].",
				".$row['id_category_default'].",
				".$row['id_shop_default'].",
				".$row['id_tax_rules_group'].",
				".$row['on_sale'].",
				".$row['online_only'].",
				'".pSql(utf8_encode($row['ean13']))."',
				NULL,
				'".pSql(utf8_encode($row['upc']))."',
				".$row['ecotax'].",
				".$row['quantity'].",
				".$row['minimal_quantity'].",
				NULL,
				0,
				".$row['price'].",
				".$row['wholesale_price'].",
				'".pSql(utf8_encode($row['unity']))."',
				".$row['unit_price_ratio'].",
				".$row['additional_shipping_cost'].",
				'".pSql(utf8_encode($row['reference']))."',
				'".pSql(utf8_encode($row['supplier_reference']))."',
				'".pSql(utf8_encode($row['location']))."',
				".$row['width'].",
				".$row['height'].",
				".$row['depth'].",
				".$row['weight'].",
				".$row['out_of_stock'].",
				0.00,
				".$row['quantity_discount'].",
				".$row['customizable'].",
				".$row['uploadable_files'].",
				".$row['text_fields'].",
				".$row['active'].",
				'".pSql(utf8_encode($row['redirect_type']))."',
				0,
				".$row['available_for_order'].",
				'".pSql(utf8_encode($row['available_date']))."',
				0,
				'".pSql(utf8_encode($row['condition']))."',
				".$row['show_price'].",
				".$row['indexed'].",
				'".pSql(utf8_encode($row['visibility']))."',
				".$row['cache_is_pack'].",
				".$row['cache_has_attachments'].",
				".$row['is_virtual'].",
				".$row['cache_default_attribute'].",
				'".pSql(utf8_encode($row['date_add']))."',
				'".pSql(utf8_encode($row['date_upd']))."',
				".$row['advanced_stock_management'].",
				3,
				1,
				0
			)");

		$query = 'SELECT * FROM ps_product_shop';
		if(isset($ids) and $ids) $query .= " WHERE id_product NOT IN ($ids)";

		$result = $this->old_db->query($query);
		while($row = $result->fetch_assoc())
			Db::getInstance()->execute("INSERT INTO ps_product_shop VALUES (
				".$row['id_product'].",
				".$row['id_shop'].",
				".$row['id_category_default'].",
				".$row['id_tax_rules_group'].",
				".$row['on_sale'].",
				".$row['online_only'].",
				".$row['ecotax'].",
				".$row['minimal_quantity'].",
				NULL,
				0,
				".$row['price'].",
				".$row['wholesale_price'].",
				'".$row['unity']."',
				".$row['unit_price_ratio'].",
				".$row['additional_shipping_cost'].",
				".$row['customizable'].",
				".$row['uploadable_files'].",
				".$row['text_fields'].",
				".$row['active'].",
				'".$row['redirect_type']."',
				".$row['id_type_redirected'].",
				".$row['available_for_order'].",
				'".$row['available_date']."',
				0,
				'".$row['condition']."',
				".$row['show_price'].",
				".$row['indexed'].",
				'".$row['visibility']."',
				".$row['cache_default_attribute'].",
				".$row['advanced_stock_management'].",
				'".$row['date_add']."',
				'".$row['date_upd']."',
				3
			)");

		$query = 'SELECT * FROM ps_product_lang';
		if(isset($ids) and $ids) $query .= " WHERE id_product NOT IN ($ids)";

		$result = $this->old_db->query($query);
		while($row = $result->fetch_assoc())
			Db::getInstance()->execute("INSERT INTO ps_product_lang VALUES (
				".$row['id_product'].",
				".$row['id_shop'].",
				".$row['id_lang'].",
				'".pSql(utf8_encode($row['description']))."',
				'".pSql(utf8_encode($row['description_short']))."',
				'".pSql(utf8_encode($row['link_rewrite']))."',
				'".pSql(utf8_encode($row['meta_description']))."',
				'".pSql(utf8_encode($row['meta_keywords']))."',
				'".pSql(utf8_encode($row['meta_title']))."',
				'".pSql(utf8_encode($row['name']))."',
				'".pSql(utf8_encode($row['available_now']))."',
				'".pSql(utf8_encode($row['available_later']))."',
				NULL,
				NULL
			)");

	}
}