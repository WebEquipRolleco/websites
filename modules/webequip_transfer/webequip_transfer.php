<?php

class webequip_transfer extends Module {

	private $old_db;
	private $nb_rows;

	private $active_only = 1;

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
		if($this->old_db->connect_error)
    		die("<div class='alert alert-danger'>".$this->old_db->connect_error."</div>");
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
		
		$data['ps_address'] = array('name'=>"Adresses", 'lang'=>false, 'shop'=>false, 'updatable'=>true);
		$data['ps_customer'] = array('name'=>"Comptes : clients", 'lang'=>false, 'shop'=>false, 'updatable'=>true);
		$data['ps_employee'] = array('name'=>"Comptes : administration", 'lang'=>false, 'shop'=>false);
		$data['ps_orders'] = array('name'=>"Commandes", 'lang'=>false, 'shop'=>false, 'updatable'=>true);
		$data['ps_order_detail'] = array('name'=>"Commandes : liste des produits", 'lang'=>false, 'shop'=>false, 'updatable'=>true);
		$data['ps_order_state'] = array('name'=>"Commandes : liste des états", 'lang'=>true, 'shop'=>false);
		$data['ps_order_history'] = array('name'=>"Commandes : historique des états", 'lang'=>false, 'shop'=>false, 'updatable'=>true);
		$data['ps_activis_devis'] = array('name'=>"Devis", 'lang'=>false, 'shop'=>false, 'new_table'=>_DB_PREFIX_.Quotation::TABLE_NAME, 'updatable'=>true);
		$data['ps_activis_devis_line'] = array('name'=>"Devis : liste des produits", 'lang'=>false, 'shop'=>false, 'new_table'=>_DB_PREFIX_.QuotationLine::TABLE_NAME, 'updatable'=>true);
		$data['ps_supplier'] = array('name'=>"Fournisseurs", 'lang'=>true, 'shop'=>true, 'updatable'=>true);
		$data['ps_manufacturer'] = array('name'=>"Marques", 'lang'=>true, 'shop'=>true, 'updatable'=>true);
		$data['ps_product_SIMPLE'] = array('name'=>"Produits [1] Récupération des produits simples", 'preview'=>false, 'updatable'=>true);
		$data['ps_bundle'] = array('name'=>"Produits [1] Transition des bundles en produits", 'preview'=>false, 'updatable'=>true);
		$data['ps_product'] = array('name'=>"Produits [1] Transition des produits en déclinaisons", 'preview'=>false, 'updatable'=>true);
		$data['ps_category_product'] = array('name'=>"Produits [2+] Affectations des catégories");
		$data['ps_category_product_DEFAULT'] = array('name'=>"Produits [2+] Récupération de la catégorie par défaut des produits", 'preview'=>false);
		$data['ps_product_supplier_PRODUCT'] = array('name'=>"Produits [2+] Transition des données fournisseurs des produits", 'preview'=>false, 'updatable'=>true);
		$data['ps_product_supplier_COMBINATION'] = array('name'=>"Produits [2+] Transition des données fournisseurs des déclinaisons", 'preview'=>false, 'updatable'=>true);
		$data['ps_specific_price'] = array('name'=>"Produits [2+] Récupération des prix spécifiques", 'updatable'=>true);
		$data['ps_specific_price_ONE'] = array('name'=>"Produits [3+] Création des prix spécifiques de quantité 1", 'preview'=>false, 'updatable'=>true);
		$data['ps_image'] = array('name'=>"Produits [2+] Récupération des données d'images", 'updatable'=>true);
		$data['ps_accessory'] = array('name'=>"Produits [2+] Récupération des accessoires", 'preview'=>false);
		$data['ps_feature_product_SIMPLE'] = array('name'=>'Produits : Récupération des propriétés de produits simples', 'preview'=>false);
		$data['ps_feature_product'] = array('name'=>'Produits : Récupération des propriétés de déclinaisons', 'preview'=>false);
		$data['ps_feature'] = array('name'=>"Produits : liste des caractéristiques", 'preview'=>false, 'updatable'=>true);
		$data['ps_feature_value'] = array('name'=>"Produits : liste des valeurs de caractéristiques", 'preview'=>false, 'updatable'=>true);
		$data['ps_attribute_group'] = array('name'=>"Produits : liste des groupes d'attributs", 'preview'=>false, 'updatable'=>true);
		$data['ps_attribute'] = array('name'=>"Produits : liste des valeurs d'attributs", 'preview'=>false, 'updatable'=>true);
		$data['LINK_REWRITE'] = array('name'=>"[FIX] Récupération des url", 'preview'=>false, 'updatable'=>false);
		$data['DESCRIPTION'] = array('name'=>"[FIX] Récupération des descriptions", 'preview'=>false, 'updatable'=>false);
		$data['REF_ROLLECO'] = array('name'=>"[FIX] Récupération des références ROLLECO", 'preview'=>false, 'updatable'=>false);
		$data['REF_SIGNALISATION'] = array('name'=>"[FIX] Récupération des références PRO SIGNALISATION", 'preview'=>false, 'updatable'=>false);
		$data['REF_ATOUT'] = array('name'=>"[FIX] Récupération des références ATOUT CONTENANT", 'preview'=>false, 'updatable'=>false);
		$data['COMMENTS_ROLLECO'] = array('name'=>"[FIX] Récupération des commentaires ROLLECO", 'preview'=>false, 'updatable'=>false);
		$data['FIX_MATCHING'] = array('name'=>"[FIX] Correction du matching", 'preview'=>false, 'updatable'=>false);

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

		$sql = "SELECT COUNT(*) AS nb FROM ";
		$result = $this->old_db->query($sql.$table);

		$data['updatable'] = $infos['updatable'] ?? false;

		if(!isset($infos['preview']) or $infos['preview']) {

			$data['data'][0][] = $infos['name'];
			$data['data'][0][] = $result->fetch_object()->nb;
			$data['data'][0][] = Db::getInstance()->getValue($sql.$new_table);

			if($infos['lang']) {
				$result = $this->old_db->query($sql.$table."_lang");

				$data['data'][1][] = "Gestion des langues";
				$data['data'][1][] = $result->fetch_object()->nb;
				$data['data'][1][] = Db::getInstance()->getValue($sql.$new_table."_lang");
			}

			if($infos['shop']) {
				$result = $this->old_db->query($sql.$table."_shop");

				$data['data'][2][] = "Gestion des boutiques";
				$data['data'][2][] = $result->fetch_object()->nb;
				$data['data'][2][] = Db::getInstance()->getValue($sql.$new_table."_shop");
			}
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
	* Récupère les ID à ignorer (dans le cas d'une mise à jour de la BDD et non d'un remplacement)
	* @param string $key Clé primaire a sauvegarder
	* @param string $table Table concernée par le transfert de données
	**/
	private function getSavedIds($key, $table) {
		
		$ids = Db::getInstance()->executeS("SELECT $key FROM $table");
		$ids = array_map(function($e) { return array_shift($e); }, $ids);
		return trim(implode(',', $ids));
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
		else
			$ids = $this->getSavedIds("id_supplier", "ps_supplier");

		$sql = "SELECT * FROM ps_supplier s, ps_supplier_lang sl WHERE s.id_supplier = sl.id_supplier AND sl.id_lang = 1";
		if(isset($ids) and $ids) $sql .= " AND id_supplier NOT IN ($ids)";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			$supplier = new Supplier($row['id_supplier'], 1);
			$update = !empty($supplier->id);
			$split = explode('-', $row['name']);

			$supplier->id = $row['id_supplier'];
			$supplier->reference = (count($split) == 2) ? $split[0] : null;
		    $supplier->name = (count($split) == 2) ? utf8_encode($split[1]) : utf8_encode($row['name']);
		    $supplier->description = utf8_encode($row['description']);
		    $supplier->emails = $row['emails'];
		    $supplier->date_add = $row['date_add'];
		    $supplier->date_upd = $row['date_upd'];
		    $supplier->link_rewrite;
		    $supplier->meta_title = $row['meta_title'];
		    $supplier->meta_keywords = $row['meta_keywords'];
		    $supplier->meta_description = $row['meta_description'];
		    $supplier->BC = $row['BC'];
    		$supplier->BL = $row['BL'];
		    $supplier->active = $row['active'];
		    
		   	$supplier->record($update);
		   	$this->nb_rows++;
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
		else
			$ids = $this->getSavedIds("id_manufacturer", "ps_manufacturer");

		$sql = "SELECT * FROM ps_manufacturer m, ps_manufacturer_lang ml WHERE m.id_manufacturer = ml.id_manufacturer AND ml.id_lang = 1";
		if(isset($ids) and $ids) $sql .= " AND m.id_manufacturer NOT IN ($ids)";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			$manufacturer = new Manufacturer($row['id_manufacturer'], 1);
			$update = !empty($manufacturer->id);

			$manufacturer->id = $row['id_manufacturer'];
			$manufacturer->name = utf8_encode($row['name']);
			$manufacturer->description = utf8_encode($row['description']);
			$manufacturer->short_description = utf8_encode($row['short_description']);
			$manufacturer->date_add = $row['date_add'];
			$manufacturer->date_upd = $row['date_upd'];
			$manufacturer->meta_title = $row['meta_title'];
			$manufacturer->meta_keywords = $row['meta_keywords'];
			$manufacturer->meta_description = $row['meta_description'];
			$manufacturer->active = $row['active'];

			$manufacturer->record($update);
			$this->nb_rows++;
		}
	}

	/**
	* Transfert des adresses
	**/
	private function transfer_ps_address() {

		$this->connectToDB();

		if(Tools::getValue('eraze'))
			Db::getInstance()->execute("DELETE FROM ps_address");
		else
			$ids = $this->getSavedIds("id_address", "ps_address");

		$sql = "SELECT * FROM ps_address";
		if(isset($ids) and $ids) $sql .= " WHERE id_address NOT IN ($ids)";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			$address = new Address($row['id_address'], 1);
			$update = !empty($address->id);

			$address->id = $row['id_address'];
			$address->id_customer = $row['id_customer'];
			$address->id_manufacturer = $row['id_manufacturer'];
			$address->id_supplier = $row['id_supplier'];
			$address->id_warehouse = $row['id_warehouse'];
			$address->id_country = $row['id_country'];
			$address->id_state = $row['id_state'];
			$address->country = utf8_encode($row['country']);
			$address->alias = utf8_encode($row['alias']);
			$address->company = utf8_encode($row['company']);;
			$address->lastname = $row['lastname'] ? utf8_encode($row['lastname']) : '-';
			$address->firstname = $row['firstname'] ? utf8_encode($row['firstname']) : '-';
			$address->address1 = str_replace("?", "'", utf8_encode($row['address1']));
			$address->address2 = utf8_encode($row['address2']);
			$address->postcode = $row['postcode'];
			$address->city = utf8_encode($row['city']);
			$address->other = utf8_encode($row['other']);
			$address->phone = is_numeric($row['phone']) ? $row['phone'] : null;
			$address->phone_mobile = is_numeric($row['phone_mobile']) ? $row['phone_mobile'] : null;
			$address->vat_number = $row['vat_number'];
			$address->dni = $row['dni'];
			$address->date_add = $row['date_add'];
			$address->date_upd = $row['date_upd'];
			$address->deleted = $row['deleted'];

			$address->record($update);
			$this->nb_rows++;
		}
	}

	/**
	* Transfert des clients
	**/
	private function transfer_ps_customer() {

		$this->connectToDB();

		if(Tools::getValue('eraze'))
			Db::getInstance()->execute("DELETE FROM ps_customer");
		else
			$ids = $this->getSavedIds("id_customer", "ps_customer");

		$id_default_type = AccountType::getDefaultID();

		$sql = "SELECT c.*, (SELECT SUM(l.amount) FROM ps_activis_loyalty l WHERE c.id_customer = l.id_customer GROUP BY l.id_customer) AS rollcash FROM ps_customer c";
		if(isset($ids) and $ids) $sql .= " WHERE c.id_customer NOT IN ($ids)";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			$customer = new Customer($row['id_customer']);
			$update = !empty($customer->id);

    		$customer->id = $row['id_customer'];
    		$customer->id_shop = $row['id_shop'];
    		$customer->id_shop_group = ['id_shop_group'];
    		$customer->secure_key = $row['secure_key'];
    		$customer->reference = $row['reference_m3'];
    		$customer->chorus = utf8_encode($row['reference_chorus']);
    		$customer->note = utf8_encode($row['note']);
    		$customer->id_account_type = $id_default_type;
    		$customer->id_gender = $row['id_gender'];
    		$customer->id_default_group = $row['id_default_group'];
    		$customer->id_lang = $row['id_lang'];
    		$customer->lastname = utf8_encode($row['lastname']);
    		$customer->firstname = utf8_encode($row['firstname']);
    		$customer->birthday = $row['birthday'];
    		$customer->email = utf8_encode($row['email']);
    		$customer->email_invoice = $row['email_invoice'];
    		$customer->email_tracking = $row['email_tracking'];
    		$customer->rollcash = ($row['rollcash'] ?? 0);
    		$customer->newsletter = $row['newsletter'];
    		$customer->ip_registration_newsletter = $row['ip_registration_newsletter'];
    		$customer->newsletter_date_add = $row['newsletter_date_add'];
    		$customer->optin = $row['optin'];
    		$customer->website = $row['website'];
    		$customer->company = $row['company'];
    		$customer->siret = $row['siret'];
    		$customer->ape = $row['ape'];
    		$customer->tva = $row['tva'];
    		$customer->funding = $row['funding'];
    		$customer->date_funding = $row['date_funding'];
    		$customer->outstanding_allow_amount = $row['outstanding_allow_amount'];
    		$customer->show_public_prices = ($row['shop_public_prices'] ?? 0);
    		$customer->id_risk = $row['id_risk'];
    		$customer->max_payment_days = $row['max_payment_days'];
    		$customer->passwd = $row['passwd'];
    		$customer->last_passwd_gen = $row['last_passwd_gen'];
    		$customer->active = $row['active'];
    		$customer->is_guest = $row['is_guest'];
    		$customer->deleted = $row['deleted'];
    		$customer->date_add = $row['date_add'];
    		$customer->date_upd = $row['date_upd'];

			$customer->record($update);
			$this->nb_rows++;

			$customer->cleanGroups();
			$ids = array();

			$result = $this->old_db->query("SELECT * FROM ps_customer_group WHERE id_customer = ".$customer->id);
			while($row = $result->fetch_assoc())
				$ids[] = $row['id_group'];

			if(!empty($ids))
				$customer->addGroups($ids);
		}
	}

	/**
	* Transfert des commandes
	**/
	private function transfer_ps_orders() {

		$this->connectToDB();

		if(Tools::getValue('eraze'))
			Db::getInstance()->execute("DELETE FROM ps_orders");
		else
			$ids = $this->getSavedIds("id_order", "ps_orders");

		$sql = "SELECT * FROM ps_orders";
		if(isset($ids) and $ids) $sql .= " WHERE id_order NOT IN ($ids)";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			$order = new Order($row['id_order']);
			$update = !empty($order->id);

			$order->id = $row['id_order'];
			$order->id_address_delivery = $row['id_address_delivery'];
			$order->id_address_invoice = $row['id_address_invoice'];
			$order->id_shop_group = $row['id_shop_group'];
			$order->id_shop = $row['id_shop'];
			$order->id_cart = $row['id_cart'];
			$order->id_currency = $row['id_currency'];
			$order->id_lang = $row['id_lang'];
			$order->id_customer = $row['id_customer'];
			$order->id_carrier = $row['id_carrier'];
			$order->current_state = $row['current_state'];
			$order->secure_key = $row['secure_key'];
			$order->payment = utf8_encode($row['payment']);
			$order->module = $row['module'];
			$order->conversion_rate = $row['conversion_rate'];
			$order->recyclable = $row['recyclable'];
			$order->gift = $row['gift'];
			$order->gift_message = $row['gift_message'];
			$order->mobile_theme = $row['mobile_theme'];
			$order->shipping_number = $row['shipping_number'];
			$order->total_discounts = $row['total_discounts'];
			$order->total_discounts_tax_incl = $row['total_discounts_tax_incl'];
			$order->total_discounts_tax_excl = ($row['total_discount_tax_excl'] ?? 0);
			$order->total_paid = $row['total_paid'];
			$order->total_paid_tax_incl = $row['total_paid_tax_incl'];
			$order->total_paid_tax_excl = $row['total_paid_tax_excl'];
			$order->total_paid_real = $row['total_paid_real'];
			$order->total_products = $row['total_products'];
			$order->total_products_wt = $row['total_products_wt'];
			$order->total_shipping = $row['total_shipping'];
			$order->total_shipping_tax_incl = $row['total_shipping_tax_incl'];
			$order->total_shipping_tax_excl = $row['total_shipping_tax_excl'];
			$order->carrier_tax_rate = $row['carrier_tax_rate'];
			$order->total_wrapping = $row['total_wrapping'];
			$order->total_wrapping_tax_incl = $row['total_wrapping_tax_incl'];
			$order->total_wrapping_tax_excl = $row['total_wrapping_tax_excl'];
			$order->invoice_number = $row['invoice_number'];
			$order->delivery_number = $row['delivery_number'];
			$order->invoice_date = $row['invoice_date'];
			$order->delivery_date = $row['delivery_date'];
			$order->valid = $row['valid'];
			$order->date_add = $row['date_add'];
			$order->date_upd = $row['date_upd'];
			$order->reference = $row['reference'];
			$order->internal_reference = utf8_encode($row['internal_reference']);
			$order->round_mode = 2;
			$order->round_type = 1;

			$order->record($udpate);
			$this->nb_rows++;
		}
	}

	/**
	* Transfert des détails de commande
	**/
	private function transfer_ps_order_detail() {

		$this->connectToDB();

		if(Tools::getValue('eraze'))
			Db::getInstance()->execute("DELETE FROM ps_order_detail");
		else
			$ids = $this->getSavedIds("id_order_detail", "ps_order_detail");

		$sql = "SELECT * FROM ps_order_detail od, ps_activis_order_extends_detail oed WHERE od.id_order_detail = oed.id_order_detail";
		if(isset($ids) and $ids) $sql .= " AND od.id_order_detail NOT IN ($ids)";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			$detail = new OrderDetail($row['id_order_detail']);
			$update = !empty($detail->id);

			$detail->id = $row['id_order_detail'];
			$detail->id_order = $row['id_order'];
			$detail->id_order_invoice = ($row['id_order_invoice'] ?? 0);
			$detail->product_id = $row['product_id'];
			$detail->id_shop = $row['id_shop'];
			$detail->product_attribute_id = ($row['product_attribute_id'] ?? 0);
			$detail->id_customization = 0;
			$detail->product_name = str_replace('?', ' ', utf8_encode($row['product_name']));
			$detail->product_quantity = $row['product_quantity'];
			$detail->product_quantity_in_stock = $row['product_quantity_in_stock'];
			$detail->product_quantity_return = $row['product_quantity_return'];
			$detail->product_quantity_refunded = $row['product_quantity_refunded'];
			$detail->product_quantity_reinjected = $row['product_quantity_reinjected'];
			$detail->product_price = (float)$row['product_price'];
			$detail->original_product_price = $row['original_product_price'];
			$detail->unit_price_tax_incl = $row['unit_price_tax_incl'];
			$detail->unit_price_tax_excl = $row['unit_price_tax_excl'];
			$detail->total_price_tax_incl = $row['total_price_tax_incl'];
			$detail->total_price_tax_excl = $row['total_price_tax_excl'];
			$detail->reduction_percent = $row['reduction_percent'];
			$detail->reduction_amount = $row['reduction_amount'];
			$detail->reduction_amount_tax_excl = $row['reduction_amount_tax_excl'];
			$detail->reduction_amount_tax_incl = $row['reduction_amount_tax_incl'];
			$detail->group_reduction = $row['group_reduction'];
			$detail->product_quantity_discount = $row['product_quantity_discount'];
			$detail->product_ean13 = $row['product_ean13'];
			$detail->product_isbn = $row['product_isbn'];
			$detail->product_upc = $row['product_upc'];
			$detail->product_reference = utf8_encode($row['product_reference']);
			$detail->product_supplier_reference = utf8_encode($row['product_supplier_reference']);
			$detail->product_weight = $row['product_weight'];
			$detail->ecotax = $row['ecotax'];
			$detail->ecotax_tax_rate = $row['ecotax_tax_rate'];
			$detail->discount_quantity_applied = $row['discount_quantity_applied'];
			$detail->download_hash = $row['download_hash'];
			$detail->download_nb = $row['download_nb'];
			$detail->download_deadline = $row['download_deadline'];
			$detail->tax_name = $row['tax_name'];
			$detail->tax_rate = $row['tax_rate'];
			$detail->tax_computation_method = $row['tax_computation_method'];
			$detail->id_warehouse = $row['id_warehouse'];
			$detail->total_shipping_price_tax_excl = $row['total_shipping_price_tax_excl'];
			$detail->total_shipping_price_tax_incl = $row['total_shipping_price_tax_incl'];
			$detail->purchase_supplier_price = (float)$row['purchase_supplier_price'];
			$detail->original_wholesale_price = $row['product_price'];
			$detail->day = $row['day'];
			$detail->week = $row['week'];
			$detail->comment = utf8_encode($row['comment']);
			$detail->comment_product_1 = utf8_encode($row['commentaire1']);
			$detail->comment_product_2 = utf8_encode($row['commentaire2']);
			$detail->notification_sent = $row['notified'];
			$detail->prevent_notification = false;

			$detail->record($update);
			$this->nb_rows++;
		}
	}

	/**
	* Transfert des états de commande
	**/
	private function transfer_ps_order_state() {

		$this->connectToDB();

		Db::getInstance()->execute("DELETE FROM ps_order_state");
		Db::getInstance()->execute("DELETE FROM ps_order_state_lang");

		$result = $this->old_db->query("SELECT * FROM ps_order_state os, ps_order_state_lang osl WHERE os.id_order_state = osl.id_order_state");
		while($row = $result->fetch_assoc()) {

			$state = new OrderState($row['id_order_state'], 1);
			$update = !empty($state->id);

			$state->id = $row['id_order_state'];
			$state->name = utf8_encode($row['name']);
		    $state->template = $row['template'];
		    $state->send_email = $row['send_email'];
		    $state->module_name = $row['module_name'];
		    $state->invoice = $row['invoice'];
		    $state->color = $row['color'];
		    $state->unremovable = $row['unremovable'];
		    $state->logable = $row['logable'];
		    $state->delivery = $row['delivery'];
		    $state->hidden = $row['hidden'];
		    $state->shipped = $row['shipped'];
		    $state->paid = $row['paid'];
		    $state->proforma = $row['proforma'];
		    $state->pdf_invoice = false;
		    $state->pdf_delivery = false;
		    $state->deleted = $orw['deleted'];

		    $state->record($update);
		    $this->nb_rows++;
		}
	}

	/**
	* Transfert des historiques de changement de statut
	**/
	private function transfer_ps_order_history() {

		$this->connectToDB();

		if(Tools::getValue('eraze'))
			Db::getInstance()->execute("DELETE FROM ps_order_history");
		else
			$ids = $this->getSavedIds("id_order_history", "ps_order_history");

		$sql = "SELECT * FROM ps_order_history WHERE id_order IN (".$this->getSavedIds('id_order', 'ps_orders').")";
		if(isset($ids) and $ids) $sql .= " AND id_order_history NOT IN ($ids)";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			$history = new OrderHistory($row['id_order_history']);
			$update = !empty($history->id);

			$history->id = $row['id_order_history'];
			$history->id_order = $row['id_order'];
		    $history->id_order_state = $row['id_order_state'];
		    $history->id_employee = $row['id_employee'];
		    $history->date_add = $row['date_add'];
		    $history->date_upd = date('Y-m-d H:i:s');

		    $history->record($update);
		    $this->nb_rows++;
		}
	}

	/**
	* Transfert des caractéristiques
	**/
	private function transfer_ps_feature() {

		$this->connectToDB();

		if(Tools::getValue('eraze')) {
			Db::getInstance()->execute("DELETE FROM ps_feature");
			Db::getInstance()->execute("DELETE FROM ps_feature_lang");
			Db::getInstance()->execute("DELETE FROM ps_feature_shop");
		}
		else
			$ids = $this->getSavedIds("id_feature", "ps_feature");

		$sql = "SELECT * FROM ps_feature f, ps_feature_lang fl WHERE f.id_feature = fl.id_feature AND fl.id_lang = 1";
		if(isset($ids) and $ids) $sql .= " AND f.id_feature NOT IN ($ids)";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			$feature = new Feature($row['id_feature'], 1);
			$update = !empty($feature->id);

			$feature->id = $row['id_feature'];
			$feature->name = utf8_encode($row['name']);
			$feature->public_name = $feature->name;
    		$feature->position = $row['position'];

			$feature->record($update);
			$this->nb_rows++;
		}
	}

	/**
	* Transfert des valeurs de caractéristiques
	**/
	private function transfer_ps_feature_value() {

		$this->connectToDB();

		if(Tools::getValue('eraze')) {
			Db::getInstance()->execute("DELETE FROM ps_attribute");
			Db::getInstance()->execute("DELETE FROM ps_attribute_lang");
			Db::getInstance()->execute("DELETE FROM ps_attribute_shop");
		}
		else
			$ids = $this->getSavedIds("id_feature_value", "ps_feature_value");

		$sql = "SELECT * FROM ps_feature_value fv, ps_feature_value_lang fvl WHERE fv.id_feature_value = fvl.id_feature_value AND fvl.id_lang = 1";
		if(isset($ids) and $ids) $sql .= " AND fv.id_feature_value NOT IN ($ids)";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			$value = new FeatureValue($row['id_feature_value'], 1);
			$update = !empty($value->id);

			$value->id = $row['id_feature_value'];
			$value->id_feature = $row['id_feature'];
    		$value->value = utf8_encode($row['value']);

    		$value->record($update);
    		$this->nb_rows++;
		}
	}

	/**
	* Transfert des groupes d'attributs
	* INFOS : copie de la table des caractéristiques
	**/
	private function transfer_ps_attribute_group() {

		$this->connectToDB();

		if(Tools::getValue('eraze')) {
			Db::getInstance()->execute("DELETE FROM ps_attribute_group");
			Db::getInstance()->execute("DELETE FROM ps_attribute_group_lang");
			Db::getInstance()->execute("DELETE FROM ps_attribute_group_shop");
		}
		else
			$ids = $this->getSavedIds("id_attribute_group", "ps_attribute_group");

		$sql = "SELECT * FROM ps_feature f, ps_feature_lang fl WHERE f.id_feature = fl.id_feature AND fl.id_lang = 1";
		if(isset($ids) and $ids) $sql .= " AND f.id_feature NOT IN ($ids)";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			$group = new AttributeGroup($row['id_feature'], 1);
			$update = !empty($group->id);

			$group->id = $row['id_feature'];
			$group->name = utf8_encode($row['name']);
			$group->public_name = $group->name;
    		$group->is_color_group = false;
    		$group->quotation = false;
    		$group->group_type = "select";
    		$group->position = $row['position'];

			$group->record($update);
			$this->nb_rows++;
		}
	}

	/**
	* Transfert des valeurs d'attributs
	* INFOS : copie de la table des valeurs de caractéristiques
	**/
	private function transfer_ps_attribute() {

		$this->connectToDB();

		if(Tools::getValue('eraze')) {
			Db::getInstance()->execute("DELETE FROM ps_attribute");
			Db::getInstance()->execute("DELETE FROM ps_attribute_lang");
			Db::getInstance()->execute("DELETE FROM ps_attribute_shop");
		}
		else
			$ids = $this->getSavedIds("id_feature_value", "ps_feature_value");

		$sql = "SELECT * FROM ps_feature_value fv, ps_feature_value_lang fvl WHERE fv.id_feature_value = fvl.id_feature_value AND fvl.id_lang = 1";
		if(isset($ids) and $ids) $sql .= " AND fv.id_feature_value NOT IN ($ids)";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			$attribute = new Attribute($row['id_feature_value'], 1);
			$update = !empty($attribute->id);

			$attribute->id = $row['id_feature_value'];
			$attribute->id_attribute_group = $row['id_feature'];
    		$attribute->name = utf8_encode($row['value']);
    		$attribute->position = $row['position'];

    		$attribute->record($update);
    		$this->nb_rows++;
		}
	}

	/**
	* Transfert des devis
	**/
	private function transfer_ps_activis_devis() {

		$this->connectToDB();

		if(Tools::getValue('eraze'))
			Db::getInstance()->execute("DELETE FROM "._DB_PREFIX_.Quotation::TABLE_NAME);
		else
			$ids = $this->getSavedIds(Quotation::TABLE_PRIMARY, _DB_PREFIX_.Quotation::TABLE_NAME);

		$states[1] = Quotation::STATUS_REFUSED;
		$states[2] = Quotation::STATUS_WAITING;
		$states[3] = Quotation::STATUS_VALIDATED;

		$sql = "SELECT * FROM ps_activis_devis d INNER JOIN ps_activis_devis_shop s ON (d.id_activis_devis = s.id_activis_devis)";
		if(isset($ids) and $ids) $sql .= " WHERE d.id_activis_devis NOT IN ($ids)";
		$sql .= "AND d.hash <> 'Deleted' AND d.date_add >= '2016-01-01 00:00:00' GROUP BY d.id_activis_devis";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			if($row['hash'] != "Deleted") {

				$quotation = new Quotation($row['id_activis_devis']);
				$update = !empty($quotation->id);

				$quotation->id = $row['id_activis_devis'];
				$quotation->reference = $row['hash'];
				$quotation->status = (isset($states[$row['id_state']]) ? $states[$row['id_state']] : Quotation::STATUS_OVER);
				$quotation->id_customer = Customer::customerExists($row['email'], true);
				$quotation->email = $row['mail_cc'];
				$quotation->phone = utf8_encode($row['phone']);
				$quotation->fax = utf8_encode($row['fax']);
				$quotation->date_begin = $row['date_from'];
				$quotation->date_add = $row['date_add'];
				$quotation->date_end = $row['date_to'];
				$quotation->date_recall = $row['date_recall'];
				$quotation->comment = utf8_encode($row['comment']);
				$quotation->details = $row['contact'];
				$quotation->id_employee = $row['id_employee'];
				$quotation->active = $row['active'];
				$quotation->new = 0;
				$quotation->id_shop = ($row['id_shop'] ?? 1);
				$quotation->secure_key = $row['hash'];
				$quotation->mail_sent = $row['is_send'];

				$quotation->record($update);
				$this->nb_rows++;
			}
		}
	}

	/**
	* Transfert des lignes produits pour les devis
	**/
	private function transfer_ps_activis_devis_line() {

		$this->connectToDB();

		if(Tools::getValue('eraze'))
			Db::getInstance()->execute("DELETE FROM "._DB_PREFIX_.QuotationLine::TABLE_NAME);
		else
			$ids = $this->getSavedIds(QuotationLine::TABLE_PRIMARY, _DB_PREFIX_.QuotationLine::TABLE_NAME);

		$sql = "SELECT * FROM ps_activis_devis_line";
		if(isset($ids) and $ids) $sql .= " WHERE id_activis_devis_line NOT IN ($ids)";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			$line = new QuotationLine($row['id_activis_devis_line']);
			$update = !empty($line->id);

			$line->id = $row['id_activis_devis_line'];
			$line->reference = utf8_encode($row['reference']);
			$line->reference_supplier;
			$line->name = utf8_encode($row['name']);
			$line->properties = utf8_encode($row['size']);
			$line->information = utf8_encode($row['information']);
			$line->comment = utf8_encode($row['comment']);
			$line->id_product = $row['id_product'];
			$line->buying_price = $row['devis_buying_price'];
			$line->selling_price = $row['devis_selling_price'];
			$line->eco_tax = ((float)$row['devis_dee'] + (float)$row['devis_m']);
			$line->quantity = $row['quantity'];
			$line->min_quantity = $row['packaging'];
			$line->position = $row['position'];
			$line->id_quotation = $row['id_activis_devis'];

			$line->record($update);
			$this->nb_rows++;
		}
	}

	/**
	* Transfert des comptes employés
	**/
	public function transfer_ps_employee() {

		$this->connectToDB();

		if(Tools::getValue('eraze')) {
			Db::getInstance()->execute("DELETE FROM ps_employee");
			Db::getInstance()->execute("DELETE FROM ps_employee_shop");
			Db::getInstance()->execute("DELETE FROM ps_employee_supplier");
		}
		else
			$ids = $this->getSavedIds("id_employee", "ps_employee");

		$sql = "SELECT * FROM ps_employee";
		if(isset($ids) and $ids) $sql .= " WHERE id_employee NOT IN ($ids)";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			$employee = new Employee($row['id_employee']);
			$update = !empty($employee->id);

			$employee->id = $row['id_employee'];
			$employee->id_profile = $row['id_profile'];
			$employee->id_lang = $row['id_lang'];
			$employee->lastname = utf8_encode($row['lastname']);
			$employee->firstname = utf8_encode($row['firstname']);
			$employee->email = utf8_encode($row['email']);
			$employee->passwd = $row['passwd'];
			$employee->last_passwd_gen = $row['last_passwd_gen'];
			$employee->stats_date_from = $row['stats_date_from'];
			$employee->stats_date_to = $row['stats_date_to'];
			$employee->stats_compare_from;
			$employee->stats_compare_to;
			$employee->stats_compare_option = 1;
			$employee->preselect_date_range;
			$employee->bo_color = utf8_encode($row['bo_color']);
			$employee->default_tab = $row['default_tab'];
			$employee->bo_theme = utf8_encode($row['bo_theme']);
			$employee->bo_css = 'theme.css';
			$employee->bo_width = $row['bo_width'];
			$employee->bo_menu = 1;
			$employee->bo_show_screencast = false;
			$employee->active = $row['active'];
			$employee->optin = 1;
			$employee->remote_addr;
			$employee->id_last_order = $row['id_last_order'];
			$employee->id_last_customer_message = $row['id_last_customer_message'];
			$employee->id_last_customer = $row['id_last_customer'];
			$employee->reset_password_token;
			$employee->reset_password_validity;

			$employee->record($update);
			$this->nb_rows++;

			Db::getInstance()->execute("DELETE FROM ps_employee_supplier WHERE id_employee = ".$employee->id);
			$result = $this->old_db->query("SELECT * FROM ps_employee_supplier WHERE id_employee = ".$employee->id);
			while($row = $result->fetch_assoc())
				Db::getInstance()->execute("INSERT INTO ps_employee_supplier VALUES(NULL, ".$row['id_employee'].", ".$row['id_supplier'].")");
		}
	}

	/**
	* [Etape 1] : Récupération des produits simples
	**/
	private function transfer_ps_product_SIMPLE() {
		
		$sql = "SELECT DISTINCT(id_product_bundle) FROM ps_bundle";
		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc())
			$ids[] = $row['id_product_bundle'];

		$sql = "SELECT DISTINCT(id_product_item) FROM ps_bundle";
		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc())
			$ids[] = $row['id_product_item'];

		if(Tools::getValue('eraze')) {
			ProductMatching::erazeContent();
			Db::getInstance()->execute("DELETE FROM ps_product");
			Db::getInstance()->execute("DELETE FROM ps_product_shop");
			Db::getInstance()->execute("DELETE FROM ps_product_lang");
		}
		else
			$ids[] = $this->getSavedIds("id_product", "ps_product");

		$ids = implode(",", $ids);

		$sql = "SELECT * FROM ps_product p, ps_product_lang pl WHERE p.id_product = pl.id_product AND pl.id_lang = 1";
		if($this->active_only) $sql .= " AND p.active = 1";
		if(!empty($ids)) $sql .= " AND p.id_product NOT IN ($ids)";
		$sql .= " GROUP BY p.id_product";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {
			$this->recordProduct($row);
		}
	}

	/**
	* [Etape 1] : Transformation des bundles en produits
	**/
	private function transfer_ps_bundle() {

		$this->connectToDB();

		if(Tools::getValue('eraze')) {
			Db::getInstance()->execute("DELETE FROM ps_product");
			Db::getInstance()->execute("DELETE FROM ps_product_shop");
			Db::getInstance()->execute("DELETE FROM ps_product_lang");
		}
		else
			$ids = $this->getSavedIds("id_product", "ps_product");

		$sql = "SELECT * FROM ps_product p, ps_product_lang pl WHERE p.id_product = pl.id_product AND pl.id_lang = 1 AND p.id_product IN (SELECT DISTINCT(id_product_bundle) FROM ps_bundle)";
		if($this->active_only) $sql .= " AND p.active = 1";
		if(isset($ids) and $ids) $sql .= " AND p.id_product NOT IN ($ids)";
		$sql .= "  GROUP BY p.id_product";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {
			$this->recordProduct($row);
		}
	}

	/**
	* [Etape 1] : Transformation des produits en déclinaisons
	**/
	private function transfer_ps_product() {

		$this->connectToDB();

		if(Tools::getValue('eraze')) {
			Db::getInstance()->execute("DELETE FROM ps_product_attribute");
			Db::getInstance()->execute("DELETE FROM ps_product_attribute_combination");
			Db::getInstance()->execute("DELETE FROM ps_product_attribute_image");
			Db::getInstance()->execute("DELETE FROM ps_product_attribute_shop");
		}
		else
			$ids = $this->getSavedIds("id_product_attribute", "ps_product_attribute");

		$sql = "SELECT * FROM ps_bundle b, ps_product p, ps_product_lang pl WHERE b.id_product_item = p.id_product AND p.id_product = pl.id_product AND pl.id_lang = 1";
		if($this->active_only) $sql .= " AND p.active = 1";
		if(isset($ids) and $ids) $sql .= " AND b.id_product_item NOT IN ($ids)";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			$combination = new Combination($row['id_product_item'], 1);
			$update = !empty($combination->id);

			$combination->id = $row['id_product_item'];
			$combination->id_product = $row['id_product_bundle'];
			$combination->reference = str_replace('BUNDLE-', '', utf8_encode($row['reference']));
			$combination->supplier_reference = utf8_encode($row['supplier_reference']);
			$combination->location = utf8_encode($row['location']);
			$combination->ean13 = utf8_encode($row['ean13']);
			$combination->upc = utf8_encode($row['upc']);
			$combination->wholesale_price = $row['wholesale_price'];
			$combination->price = $row['price'];
			$combination->unit_price_impact;
			$combination->ecotax = $row['ecotax'];
			$combination->minimal_quantity = $row['minimal_quantity'];
			$combination->quantity = $row['quantity'];
			$combination->weight = $row['weight'];
			$combination->batch = $row['packaging'];

			$combination->record($update);
			ProductMatching::recordRow($row['id_product_item'], $row['id_product_bundle'], $row['id_product_item']);
			$this->nb_rows++;
		}
	}

	/**
	* Produits : Récupération des propriétés de produits simples
	**/
	private function transfer_ps_feature_product_SIMPLE() {

		$sql = "SELECT DISTINCT(id_product_bundle) FROM ps_bundle";
		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc())
			$ids[] = $row['id_product_bundle'];

		$sql = "SELECT DISTINCT(id_product_item) FROM ps_bundle";
		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc())
			$ids[] = $row['id_product_item'];

		Db::getInstance()->execute("DELETE FROM ps_feature_product");
		$sql = "SELECT * FROM ps_feature_product WHERE id_product NOT IN (".implode(',', $ids).")";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {
			Db::getInstance()->execute("INSERT IGNORE INTO ps_feature_product VALUES (".$row['id_feature'].", ".$row['id_product'].", ".$row['id_feature_value'].")");
			$this->nb_rows++;
		}
	}

	/**
	* Produits : Récupération des propriétés de déclinaisons
	**/
	private function transfer_ps_feature_product() {

		Db::getInstance()->execute("DELETE FROM ps_product_attribute_combination");
		$sql = "SELECT fp.* FROM ps_feature_product fp, ps_bundle b WHERE fp.id_product = b.id_product_item";	

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {
			Db::getInstance()->execute("INSERT IGNORE INTO ps_product_attribute_combination VALUES(".$row['id_feature_value'].", ".$row['id_product'].")");
			$this->nb_rows++;
		}
	}

	/**
	* [Etape 2+] Récupération des affectations de catégories
	**/
	private function transfer_ps_category_product() {

		Db::getInstance()->execute("DELETE FROM ps_category_product");
		$sql = "SELECT cp.*, c.new_id FROM ps_category_product cp, ps_category c WHERE cp.id_category = c.id_category AND c.new_id IS NOT NULL";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			$matching = new ProductMatching($row['id_product']);
			if($matching->id){
				Db::getInstance()->execute("INSERT IGNORE INTO ps_category_product VALUES (".$row['new_id'].", ".$matching->id_product.", ".$row['position'].")");
				$this->nb_rows++;
			}
		}
	}

	/**
	* [Etape 2+] Récupération des catégories par défaut
	**/
	private function transfer_ps_category_product_DEFAULT() {

		$ids = $this->getSavedIds("id_product", _DB_PREFIX_.ProductMatching::TABLE_NAME);
		$sql = "SELECT p.id_product, c.new_id FROM ps_product p, ps_category c WHERE p.id_category_default = c.id_category AND c.new_id IS NOT NULL AND p.id_product IN ($ids)";
		if($this->active_only) $sql .= " AND p.active = 1";
		
		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			$matching = new ProductMatching($row['id_product']);
			if($matching->id) {
				Db::getInstance()->execute("UPDATE ps_product SET id_category_default = ".$row['new_id']." WHERE id_product = ".$matching->id_product);
				$this->nb_rows++;
			}
		}
	}

	/**
	* [Etape 2+] Transfert des infos fournisseurs
	**/
	private function transfer_ps_product_supplier_PRODUCT() {

		$this->connectToDB();

		if(Tools::getValue('eraze'))
			Db::getInstance()->execute("DELETE FROM ps_product_supplier WHERE id_product_attribute = 0");
		else
			$ids = $this->getSavedIds("id_product", "ps_product_supplier");

		$sql = "SELECT id_product, id_supplier, supplier_reference FROM ps_product WHERE supplier_reference IS NOT NULL";
		if(isset($ids) and $ids) $sql .= " AND id_product NOT IN ($ids)";

		foreach(Db::getInstance()->executeS($sql) as $row) {

			$data = new ProductSupplier();

			$data->id_product = $row['id_product'];
			$data->id_product_attribute = 0;
			$data->id_supplier = $row['id_supplier'];
			$data->product_supplier_reference = $row['supplier_reference'];
			$data->id_currency = 1;
			$data->product_supplier_price_te = 0;

			$data->save();
		}
	}

	/**
	* [Etape 2+] Transfert des infos fournisseurs
	**/
	private function transfer_ps_product_supplier_COMBINATION() {

		$this->connectToDB();

		if(Tools::getValue('eraze'))
			Db::getInstance()->execute("DELETE FROM ps_product_supplier WHERE id_product_attribute <> 0");
		else
			$ids = $this->getSavedIds("id_product_attribute", "ps_product_supplier");

		$sql = "SELECT id_product, id_product_attribute, supplier_reference FROM ps_product_attribute WHERE supplier_reference IS NOT NULL";
		if(isset($ids) and $ids) $sql .= " AND id_product_attribute NOT IN ($ids)";

		foreach(Db::getInstance()->executeS($sql) as $row) {
			if($id_supplier = Db::getInstance()->getValue("SELECT id_supplier FROM ps_product WHERE id_product = ".$row['id_product'])) {

				$data = new ProductSupplier();

				$data->id_product = $row['id_product'];
				$data->id_product_attribute = $row['id_product_attribute'];
				$data->id_supplier = $id_supplier;
				$data->product_supplier_reference = $row['supplier_reference'];
				$data->id_currency = 1;
				$data->product_supplier_price_te = 0;

				$data->save();
			}
		}
	}

	/**
	* [Etape 2+] : Récupération des prix spécifiques
	**/
	private function transfer_ps_specific_price() {

		$this->connectToDB();

		if(Tools::getValue('eraze')) {
			Db::getInstance()->execute("DELETE FROM ps_specific_price");
			Db::getInstance()->execute("DELETE FROM ps_specific_price_priority");
			Db::getInstance()->execute("DELETE FROM ps_specific_price_rule");
			Db::getInstance()->execute("DELETE FROM ps_specific_price_rule_condition_group");
		}
		else
			$ids = $this->getSavedIds("id_specific_price", "ps_specific_price");

		$sql = "SELECT * FROM ps_specific_price";
		if(isset($ids) and $ids) $sql .= " WHERE id_specific_price NOT IN ($ids)";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			// Ne récupérer que les prix des produits importés (+ récupération des nouvelles données)
			$matching = new ProductMatching($row['id_product']);
			if($matching->id) {

				$price = new SpecificPrice($row['id_specific_price']);
				$update = !empty($price->id);

				$price->id = $row['id_specific_price'];
				$price->id_product = $matching->id_product;
				$price->id_specific_price_rule = $row['id_specific_price_rule'];
				$price->id_cart = $row['id_cart'];
				$price->id_product_attribute = $matching->id_combination;
				$price->id_shop = $row['id_shop'];
				$price->id_shop_group = $row['id_shop_group'];
				$price->id_currency = $row['id_currency'];
				$price->id_country = $row['id_country'];
				$price->id_group = $row['id_group'];
				$price->id_customer = $row['id_customer'];
				$price->price = $row['price'];
				$price->from_quantity = $row['from_quantity'];
				$price->reduction = $row['reduction'];
				$price->reduction_type = $row['reduction_type'];
				$price->from = $row['from'];
				$price->to = $row['to'];
				$price->buying_price = $row['purchasing_price_ws'];
				$price->delivery_fees = $row['shipping_price'];
				$price->comment_1 = utf8_encode($row['first_comment']);
				$price->comment_2 = utf8_encode($row['second_comment']);

				$price->record($update);
				$this->nb_rows++;
			}
		}
	}

	/**
	* [Etape 3+]
	**/
	private function transfer_ps_specific_price_ONE() {

		$this->connectToDB();

		if(Tools::getValue('eraze'))
			Db::getInstance()->execute("DELETE FROM ps_specific_price WHERE from_quantity = 1");
		else {
			$ids = Db::getInstance()->executeS("SELECT pm.id_product_matching FROM ps_product_matching pm, ps_specific_price sp WHERE sp.from_quantity = 1 AND pm.id_product = sp.id_product AND pm.id_combination = sp.id_product_attribute");
			$ids = array_map(function($e) { return $e['id_product_matching']; }, $ids);
			$ids = trim(implode(",", $ids));
		}

		$sql = "SELECT id_product, price FROM ps_product";
		if(isset($ids) and $ids) $sql .= " WHERE id_product NOT IN ($ids)";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			// Ne récupérer que les prix des produits importés (+ récupération des nouvelles données)
			$matching = new ProductMatching($row['id_product']);
			if($matching->id) {

				$price = new SpecificPrice();
				
				$price->id_product = $matching->id_product;
				$price->id_product_attribute = $matching->id_combination;
				$price->price = $row['price'];
				$price->from_quantity = 1;
				$price->reduction_type = 'amount';
				$price->id_shop = 0;
				$price->id_currency = 1;
				$price->id_country = 8;
				$price->id_group = 0;
				$price->id_customer = 0;
				$price->reduction = 0;
				$price->from = "0000-00-00 00:00:00";
				$price->to = "0000-00-00 00:00:00";

				$price->record($update);
				$this->nb_rows++;
			}
		}
	}

	/**
	* [Etape 2+] Copie des informations relatives aux images
	**/
	private function transfer_ps_image() {

		$this->connectToDB();

		if(Tools::getValue('eraze')) {
			Db::getInstance()->execute("DELETE FROM ps_image");
			Db::getInstance()->execute("DELETE FROM ps_image_lang");
			Db::getInstance()->execute("DELETE FROM ps_image_shop");
		}
		else
			$ids = $this->getSavedIds("id_image", "ps_image");

		$sql = "SELECT * FROM ps_image i, ps_image_lang il WHERE i.id_image = il.id_image";
		if(isset($ids) and $ids) $sql .= " AND i.id_image NOT IN ($ids)";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			// N'importer les images que des produits importés
			$matching = new ProductMatching($row['id_product']);
			if($matching->id and $matching->id == $matching->id_product) {

				$image = new Image($row['id_image']);
				$update = !empty($image->id);

				$image->id = $row['id_image'];
				$image->id_product = $matching->id_product;
				$image->position = $row['position'];
				$image->cover = $row['cover'];
				$image->legend = utf8_encode($row['title'] ?? $row['legend']);
				$image->image_format = 'jpg';

				$image->record($update);
				$this->nb_rows++;
			}
		}
	}

	/**
	* [Etape 2+] Récupération des accessoires
	**/
	private function transfer_ps_accessory() {

		$this->connectToDB();

		Db::getInstance()->execute("DELETE FROM ps_accessory");
		$result = $this->old_db->query("SELECT * FROM ps_activis_product_accessories");
		while($row = $result->fetch_assoc()) {

			$matching_1 = new ProductMatching($row['id_product_1']);
			$matching_2 = new ProductMatching($row['id_product_2']);
			if($matching_1->id and $matching_2->id) {
				Db::getInstance()->execute("INSERT IGNORE INTO ps_accessory VALUES (".$matching_1->id_product.", ".$matching_2->id_product.")");
				$this->nb_rows++;
			}
		}
	}
	
	/**
	* [FIX] link_rewrite
	**/
	private function transfer_LINK_REWRITE() {
		
		$this->connectToDB();

		$ids = $this->getSavedIds("id_product", "ps_product");
		$sql = "SELECT id_product, link_rewrite FROM ps_product_lang WHERE id_product IN ($ids) GROUP BY id_product";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			Db::getInstance()->execute("UPDATE ps_product_lang SET link_rewrite = '".utf8_encode($row['link_rewrite'])."' WHERE id_product = ".$row['id_product']);
			$this->nb_rows++;
		}
	}

	/**
	* [FIX] Description
	**/
	private function transfer_DESCRIPTION() {

		$this->connectToDB();

		$ids = $this->getSavedIds("id_product", "ps_product");

		$sql = "SELECT id_product, description FROM ps_product_lang WHERE id_product IN ($ids) AND description IS NOT NULL AND description <> '' GROUP BY id_product";
		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {
			if(!Db::getInstance()->execute("UPDATE ps_product_lang SET description = '".pSql(utf8_encode($row['description']), true)."' WHERE id_product = ".$row['id_product']))
				$this->nb_rows++;
		}

		$sql = "SELECT id_product, description_short FROM ps_product_lang WHERE id_product IN ($ids) AND description_short IS NOT NULL AND description_short <> '' GROUP BY id_product";
		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {
			Db::getInstance()->execute("UPDATE ps_product_lang SET description_short = '".pSql(utf8_encode($row['description_short']), true)."' WHERE id_product = ".$row['id_product']);
		}
	}

	/**
	* [FIX] Références Rolléco
	**/
	private function transfer_REF_ERAZE() {

		$this->connectToDB();

		Db::getInstance()->execute("UPDATE ps_product SET reference = NULL)");
		Db::getInstance()->execute("UPDATE ps_product_shop SET reference = NULL)");
		Db::getInstance()->execute("UPDATE ps_product_attribute SET reference = NULL)");
		Db::getInstance()->execute("UPDATE ps_product_attribute_shop SET reference = NULL)");
	}

	/**
	* [FIX] Références Rolléco
	**/
	private function transfer_REF_ROLLECO() {

		$this->connectToDB();

		$sql = "SELECT id_product, reference FROM ps_product_shop WHERE id_shop = 1";
		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			$reference = str_replace('BUNDLE-', '', $row['reference']);
			$matching = new ProductMatching($row['id_product']);
			if($matching->id) {

				if($matching->id_combination) {
					Db::getInstance()->execute("UPDATE ps_product_attribute SET reference = '$reference' WHERE id_product_attribute = ".$matching->id_combination);
					Db::getInstance()->execute("UPDATE ps_product_attribute_shop SET reference = '$reference' WHERE id_shop = 1 AND id_product_attribute = ".$matching->id_combination);
				}
				else {
					Db::getInstance()->execute("UPDATE ps_product SET reference = '$reference' WHERE id_product = ".$matching->id_product);
					Db::getInstance()->execute("UPDATE ps_product_shop SET reference = '$reference' WHERE id_shop = 1 AND id_product = ".$matching->id_product);
				}
			}
		}
	}

	/**
	* [FIX] Références Pro-signalisation
	**/
	private function transfer_REF_SIGNALISATION() {

		$this->connectToDB();

		$sql = "SELECT id_product, reference FROM ps_product_shop WHERE id_shop = 2";
		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			$reference = str_replace('BUNDLE-', '', $row['reference']);
			$matching = new ProductMatching($row['id_product']);
			if($matching->id) {

				if($matching->id_combination)
					Db::getInstance()->execute("UPDATE ps_product_attribute_shop SET reference = '$reference' WHERE id_shop = 2 AND id_product_attribute = ".$matching->id_combination);
				else
					Db::getInstance()->execute("UPDATE ps_product_shop SET reference = '$reference' WHERE id_shop = 2 AND id_product = ".$matching->id_product);
			}
		}
	}

	/**
	* [FIX] Références Atout-contenant
	**/
	private function transfer_REF_ATOUT() {

		$this->connectToDB();

		$sql = "SELECT id_product, reference FROM ps_product_shop WHERE id_shop = 3";
		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			$reference = str_replace('BUNDLE-', '', $row['reference']);
			$matching = new ProductMatching($row['id_product']);
			if($matching->id) {

				if($matching->id_combination)
					Db::getInstance()->execute("UPDATE ps_product_attribute_shop SET reference = '$reference' WHERE id_shop = 3 AND id_product_attribute = ".$matching->id_combination);
				else
					Db::getInstance()->execute("UPDATE ps_product_shop SET reference = '$reference' WHERE id_shop = 3 AND id_product = ".$matching->id_product);
			}
		}
	}

	/**
	* [FIX] Transfert les commentaires de Rolléco
	**/
	private function transfer_COMMENTS_ROLLECO() {

		$this->connectToDB();

		$sql = "SELECT id_product, commentaire1, commentaire2 FROM ps_product_lang WHERE id_shop = 1 GROUP BY id_product";
		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			$matching = new ProductMatching($row['id_product']);
			if($matching->id) {
				if($matching->id_combination) {
					Db::getInstance()->execute("UPDATE ps_product_attribute SET comment_1 = '".pSql(utf8_encode($row['commentaire1']))."', comment_2 = '".pSql(utf8_encode($row['commentaire2']))."' WHERE id_product_attribute = ".$matching->id_combination);
					Db::getInstance()->execute("UPDATE ps_product_attribute_shop SET comment_1 = '".pSql(utf8_encode($row['commentaire1']))."', comment_2 = '".pSql(utf8_encode($row['commentaire2']))."' WHERE id_shop = 1 AND id_product_attribute = ".$matching->id_combination);
					$this->nb_rows++;
				}
				else {
					Db::getInstance()->execute("UPDATE ps_product SET comment_1 = '".pSql(utf8_encode($row['commentaire1']))."', comment_2 = '".pSql(utf8_encode($row['commentaire2']))."' WHERE id_product = ".$matching->id_product);
					Db::getInstance()->execute("UPDATE ps_product_shop SET comment_1 = '".pSql(utf8_encode($row['commentaire1']))."', comment_2 = '".pSql(utf8_encode($row['commentaire2']))."' WHERE id_shop = 1 AND id_product = ".$matching->id_product);
					$this->nb_rows++;
				}
			}
		}
	}

	/**
	* [FIX] Répare ou met à jour la table de matching
	**/
	private function transfer_FIX_MATCHING() {

		ProductMatching::erazeContent();

		// Produits
		foreach(Db::getInstance()->executeS("SELECT id_product FROM ps_product") as $row) {
			$matching = new ProductMatching($row['id_product']);

			$matching->id_product_matching = $row['id_product'];
			$matching->id_product = $row['id_product'];
			$matching->id_combination = 0;

			$matching->force_id = true;
			$matching->save();
			$this->nb_rows++;
		}

		// Déclinaisons
		foreach(Db::getInstance()->executeS("SELECT id_product_attribute, id_product FROM ps_product_attribute") as $row) {
			$matching = new ProductMatching($row['id_product_attribute']);

			$matching->id_product_matching = $row['id_product_attribute'];
			$matching->id_product = $row['id_product'];
			$matching->id_combination = $row['id_product_attribute'];

			$matching->force_id = true;
			$matching->save();
			$this->nb_rows++;
		}
	}

	/**
	* Transforme un resultat SQL en produit
	* @param array
	**/
	private function recordProduct($row) {

		$product = new Product($row['id_product'], true, 1);
		$update = !empty($product->id);

		$product->id = $row['id_product'];
		$product->id_manufacturer = $row['id_manufacturer'];
		$product->id_supplier = $row['id_supplier'];
		//$product->id_category_default = $row['id_category_default'];
		$product->id_shop_default = $row['id_shop_default'];
		$product->name = str_replace("?", " ", utf8_encode($row['name']));
		$product->description = utf8_encode($row['description']);
		$product->description_short = utf8_encode($row['description_short']);
		$product->quantity = $row['quantity'];
		$product->minimal_quantity = $row['minimal_quantity'];
		$product->price = $row['price'];
		$product->additional_shipping_cost = $row['additional_shipping_cost'];
		$product->wholesale_price = $row['wholesale_price'];
		$product->on_sale = $row['on_sale'];
		$product->online_only = $row['online_only'];
		$product->unity = utf8_encode($row['unity']);
		$product->unit_price = $row['price'];
		$product->unit_price_ratio = $row['unit_price_ratio'];
		$product->ecotax = $row['ecotax'];
		$product->reference = str_replace('BUNDLE-', '', utf8_encode($row['reference']));
		$product->supplier_reference = utf8_encode($row['supplier_reference']);
		$product->location = utf8_encode($row['location']);
		$product->width = $row['width'];
		$product->height = $row['height'];
		$product->depth = $row['depth'];
		$product->weight = $row['weight'];
		$product->ean13 = utf8_encode($row['ean13']);
		$product->upc = utf8_encode($row['upc']);
		$product->link_rewrite = $row['ling_rewrite'];
		$product->meta_description = utf8_encode($row['meta_description']);
		$product->meta_keywords = utf8_encode($row['meta_keywords']);
		$product->meta_title = utf8_encode($row['meta_title']);
		$product->quantity_discount = $row['quantity_discount'];
		$product->customizable = $row['customizable'];
		$product->uploadable_files = $row['uploadable_files'];
		$product->text_fields = $row['text_fields'];
		$product->active = $row['active'];
		$product->redirect_type = utf8_encode($row['redirect_type']);
		$product->available_for_order = $row['available_for_order'];
		$product->available_date = $row['available_date'];
		$product->condition = $row['condition'];
		$product->visibility = $row['visibility'];
		$product->date_add = ($row['date_add'] != "0000-00-00 00:00:00") ? $row['date_add'] : date('Y-m-d H:i:s');
		$product->date_upd = ($row['date_upd'] != "0000-00-00 00:00:00") ? $row['date_upd'] : date('Y-m-d H:i:s');
		$product->id_tax_rules_group = $row['id_tax_rules_group'];
		$product->advanced_stock_management = $row['advanced_stock_management'];
		$product->out_of_stock = $row['out_of_stock'];
		$product->cache_is_pack = $row['cache_is_pack'];
		$product->cache_has_attachments = $row['cache_has_attachments'];
		$product->is_virtual = $row['is_virtual'];
		$product->cache_default_attribute = $row['cache_default_attribute'];
		$product->batch = $row['packaging'];

		$product->record($update);
		ProductMatching::recordRow($row['id_product'], $row['id_product']);
		$this->nb_rows++;
	}

}