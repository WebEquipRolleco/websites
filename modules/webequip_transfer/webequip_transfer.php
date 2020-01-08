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
		if($this->old_db->connect_error)
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
		$data['ps_product'] = array('name'=>"Produits [1] Transition des bundles en produits", 'lang'=>true, 'shop'=>true, 'updatable'=>true);
		$data['ps_feature'] = array('name'=>"Produits : liste des caractéristiques", 'lang'=>true, 'shop'=>true, 'updatable'=>true);
		$data['ps_feature_value'] = array('name'=>"Produits : liste des valeurs de caractéristiques", 'lang'=>true, 'shop'=>false, 'updatable'=>true);
		$data['ps_attribute_group'] = array('name'=>"Produits : liste des groupes d'attributs", 'lang'=>true, 'shop'=>true, 'updatable'=>true);
		$data['ps_attribute'] = array('name'=>"Produits : liste des valeurs d'attributs", 'lang'=>true, 'shop'=>false, 'updatable'=>true);

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
		$sql .= " ORDER BY id_address DESC";

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
		$sql .= " ORDER BY c.id_customer DESC";

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
		$sql .= " ORDER BY id_order DESC";

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
		$sql .= " ORDER BY od.id_order_detail DESC";

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
		$sql .= " ORDER BY id_order_history DESC";

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

		$query = "SELECT * FROM ps_activis_devis d INNER JOIN ps_activis_devis_shop s ON (d.id_activis_devis = s.id_activis_devis)";
		if(isset($ids) and $ids) $query .= " WHERE d.id_activis_devis NOT IN ($ids)";
		$query .= "AND d.hash <> 'Deleted' GROUP BY d.id_activis_devis ORDER BY d.id_activis_devis DESC";

		$result = $this->old_db->query($query);
		while($row = $result->fetch_assoc()) {

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

	/**
	* Transfert des lignes produits pour les devis
	**/
	private function transfer_ps_activis_devis_line() {

		$this->connectToDB();

		if(Tools::getValue('eraze'))
			Db::getInstance()->execute("DELETE FROM "._DB_PREFIX_.QuotationLine::TABLE_NAME);
		else
			$ids = $this->getSavedIds(QuotationLine::TABLE_PRIMARY, _DB_PREFIX_.QuotationLine::TABLE_NAME);

		$query = "SELECT * FROM ps_activis_devis_line";
		if(isset($ids) and $ids) $query .= " WHERE id_activis_devis_line NOT IN ($ids)";
		$query .= " ORDER BY id_activis_devis DESC";

		$result = $this->old_db->query($query);
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
	* Transfert des produits
	**/
	private function transfer_ps_product() {

		$this->connectToDB();

		if(Tools::getValue('eraze')) {
			Db::getInstance()->execute("DELETE FROM ps_product");
			Db::getInstance()->execute("DELETE FROM ps_product_shop");
			Db::getInstance()->execute("DELETE FROM ps_product_lang");
		}
		else
			$ids = $this->getSavedIds("id_product", "ps_product");

		$sub_query = "SELECT DISTINCT(id_product_bundle) FROM ps_bundle";
		if(isset($ids) and $ids) $sub_query .= " WHERE id_product_bundle NOT IN ($ids)";

		$query = "SELECT * FROM ps_product p, product_lang pl WHERE p.id_product_ = pl.id_product AND pl.id_lang = 1 AND p.id_product IN ($sub_query)";

		$result = $this->old_db->query($query);
		while($row = $result->fetch_assoc()) {

			$product = new Product($row['id_product'], true, 1);
			$update = !empty($product->id);

			$product->id = $row['id_product'];
			$product->id_manufacturer = $row['id_manufacturer'];
			$product->id_supplier = $row['id_supplier'];
			//$product->id_category_default = $row['id_category_default'];
			$product->id_shop_default = $row['id_shop_default'];
			$product->name = $row['name'];
			$product->description = $row['description'];
			$product->description_short = $row['description_short'];
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
			$product->reference = utf8_encode($row['reference']);
			$product->supplier_reference = utf8_encode($row['supplier_reference']);
			$product->location = utf8_encode($row['location']);
			$product->width = $row['width'];
			$product->height = $row['height'];
			$product->depth = $row['depth'];
			$product->weight = $row['weight'];
			$product->ean13 = utf8_encode($row['ean13']);
			$product->upc = utf8_encode($row['upc']);
			$product->link_rewrite = $row['ling_rewrite'];
			$product->meta_description = $row['meta_description'];
			$product->meta_keywords = $row['meta_keywords'];
			$product->meta_title = $row['meta_title'];
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
			$product->date_add = $row['date_add'];
			$product->date_upd = $row['date_upd'];
			$product->id_tax_rules_group = $row['id_tax_rules_group'];
			$product->advanced_stock_management = $row['advanced_stock_management'];
			$product->out_of_stock = $row['out_of_stock'];
			$product->cache_is_pack = $row['cache_is_pack'];
			$product->cache_has_attachments = $row['cache_has_attachments'];
			$product->is_virtual = $row['is_virtual'];
			$product->cache_default_attribute = $row['cache_default_attribute'];

			$product->record($update);
			$this->nb_rows++;
		}

		/*$query = 'SELECT * FROM ps_product_shop';
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
			)");*/
	}
}