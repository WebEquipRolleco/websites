<?php

require_once dirname(__FILE__)."/../../modules/webequip_reviews/classes/Review.php";

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
		
		$data['ps_address'] = array('name'=>"Adresses", 'lang'=>false, 'shop'=>false);
		$data['FIX_ps_address'] = array('name'=>"[FIX] Téléphones des adresses");
		$data['ps_customer'] = array('name'=>"Comptes : clients", 'lang'=>false, 'shop'=>false);
		$data['ps_employee'] = array('name'=>"Comptes : administration", 'lang'=>false, 'shop'=>false);
		$data['ps_orders'] = array('name'=>"Commandes", 'lang'=>false, 'shop'=>false);
		$data['ps_order_detail'] = array('name'=>"Commandes : liste des produits", 'lang'=>false, 'shop'=>false);
		$data['FIX_DELIVERY_FEES'] = array('name'=>"[FIX] Frais de port des lignes produits", 'preview'=>false);
		$data['ps_order_state'] = array('name'=>"Commandes : liste des états", 'lang'=>true, 'shop'=>false);
		$data['ps_order_history'] = array('name'=>"Commandes : historique des états", 'lang'=>false, 'shop'=>false);
		$data['FIX_HISTORY'] = array('name'=>"[FIX] Dates des historique des états", 'preview'=>false);
		$data['ps_order_payment'] = array('name'=>"Commandes : liste des paiements", 'preview'=>false);
		$data['ps_activis_devis'] = array('name'=>"Devis", 'lang'=>false, 'shop'=>false, 'new_table'=>_DB_PREFIX_.Quotation::TABLE_NAME);
		$data['ps_FIX_QUOTATION'] = array('name'=>"[FIX] Provenance devis et email client", 'preview'=>false);
		$data['ps_activis_devis_line'] = array('name'=>"Devis : liste des produits", 'lang'=>false, 'shop'=>false, 'new_table'=>_DB_PREFIX_.QuotationLine::TABLE_NAME);
		$data['FIX_MATCHING'] = array('name'=>"[FIX] Correction du matching", 'preview'=>false, 'updatable'=>false);
		$data['FIX_QUOTATIONS'] = array('name'=>"[FIX] Correction des fournisseurs produits devis", 'preview'=>false, 'updatable'=>false);
		$data['FIX_DELIVERY_ORDERS'] = array('name'=>"[UPDATE] Récupère les données de livraison des produits", 'preview'=>false);
		$data['FIX_DATE_ORDERS'] = array('name'=>"[UPDATE] Récupère les dates de commandes", 'preview'=>false);
		$data['FIX_DATE_CUSTOMERS'] = array('name'=>"[UPDATE] Récupère les dates des clients", 'preview'=>false);
		$data['ps_oa'] = array('name'=>"[UPDATE] Récupère des OA", 'preview'=>false);
		$data['FIX_FACTURATION'] = array('name'=>"[UPDATE] Information de facturation", 'preview'=>false);
		
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
			if(!Tools::getValue('skip_test'))
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

				case 'load_customers':
					if(Tools::getValue('update') == 1) $this->updateOldCustomers();
					$this->context->smarty->assign('nb', Db::getInstance()->getValue('SELECT COUNT(*) FROM ps_customer WHERE id_shop <> 1'));
					die($this->display(__FILE__, 'bloc_old_customers.tpl'));
				break;

				case 'load_orders':
					if(Tools::getValue('update') == 1) $this->updateOldOrders();
				$this->context->smarty->assign('nb', Db::getInstance()->getValue('SELECT COUNT(*) FROM ps_orders WHERE id_shop <> 1'));
					die($this->display(__FILE__, 'bloc_old_orders.tpl'));
				break;

				case 'load_carts':
					if(Tools::getValue('update') == 1) $this->updateOldCarts();
				$this->context->smarty->assign('nb', Db::getInstance()->getValue('SELECT COUNT(*) FROM ps_cart WHERE id_shop <> 1'));
					die($this->display(__FILE__, 'bloc_old_carts.tpl'));
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
	* Retourne une valeur max enregistrée en BDD
	* @param string $key Clé primaire à recherchée
	* @param string $table Table concernée par le transfert de données
	* @return int
	**/
	private function getMax($key, $table) {
		return Db::getInstance()->getValue("SELECT MAX($key) FROM $table");
	}

	/**
	* Transfert des adresses
	**/
	public function transfer_ps_address($min_id = false) {

		$this->connectToDB();
		$this->nb_rows = 0;

		if($min_id) $id = $min_id;
		else $id = $this->getMax("id_address", "ps_address");

		$sql = "SELECT * FROM ps_address";
		if($id) $sql .= " WHERE id_address > $id";

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
			$address->company = str_replace(array("?", '='), "", utf8_encode($row['company']));
			$address->lastname = $row['lastname'] ? str_replace('?', ' ', utf8_encode($row['lastname'])) : '-';
			$address->firstname = $row['firstname'] ? utf8_encode($row['firstname']) : '-';
			$address->address1 = str_replace("?", "'", utf8_encode($row['address1']));
			$address->address2 = str_replace("?", "'", utf8_encode($row['address2']));
			$address->postcode = $row['postcode'];
			$address->city = str_replace('?', ' ', utf8_encode($row['city']));
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

		return $this->nb_rows;
	}

	public function transfer_FIX_ps_address() {

		$this->connectToDB();
		$this->nb_rows = 0;
		
		$result = $this->old_db->query("SELECT id_address, phone, phone_mobile FROM ps_address ORDER BY id_address DESC");
		while($row = $result->fetch_assoc()) {

			$address = new Address($row['id_address'], 1);
			if(!$address->id) continue;

			$address->phone = $row['phone'];
			$address->phone_mobile = $row['phone_mobile'];

			$address->save();
			$this->nb_rows++;
		}

		return $this->nb_rows;
	}

	/**
	* Transfert des clients
	**/
	public function transfer_ps_customer($min_id = null) {

		$this->connectToDB();
		$this->nb_rows = 0;

		if($min_id) $id = $min_id;
		else $id = $this->getMax("id_customer", "ps_customer");

		$id_default_type = AccountType::getDefaultID();

		$sql = "SELECT c.*, (SELECT SUM(l.amount) FROM ps_activis_loyalty l WHERE c.id_customer = l.id_customer GROUP BY l.id_customer) AS rollcash FROM ps_customer c";
		if($id) $sql .= " WHERE c.id_customer > $id";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			$customer = new Customer($row['id_customer']);
			$update = !empty($customer->id);

    		$customer->id = $row['id_customer'];
    		$customer->id_shop = $row['id_shop'];
    		$customer->id_shop_group = $row['id_shop_group'];
    		$customer->secure_key = $row['secure_key'];
    		$customer->reference = $row['reference_m3'];
    		$customer->chorus = utf8_encode($row['reference_chorus']);
    		$customer->note = utf8_encode($row['note']);
    		$customer->id_account_type = $id_default_type;
    		$customer->id_gender = $row['id_gender'];
    		$customer->id_default_group = $row['id_default_group'];
    		$customer->id_lang = $row['id_lang'];
    		$customer->lastname = str_replace('?', ' ', utf8_encode($row['lastname']));
    		$customer->firstname = $row['firstname'] ? utf8_encode($row['firstname']) : '-';
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
    		$customer->siret = utf8_encode($row['siret']);
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

			$sub_result = $this->old_db->query("SELECT * FROM ps_customer_group WHERE id_customer = ".$customer->id);
			while($row = $sub_result->fetch_assoc())
				$ids[] = $row['id_group'];

			if(!empty($ids))
				$customer->addGroups($ids);
		}

		return $this->nb_rows;
	}

	/**
	* Transfert des commandes
	**/
	public function transfer_ps_orders($min_id = null) {

		$this->connectToDB();
		$this->nb_rows = 0;

		if($min_id) $id = $min_id;
		else $id = $this->getMax('id_order', 'ps_orders');

		$sql = "SELECT * FROM ps_orders";
		if($id) $sql .= " WHERE id_order > $id";

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
			$order->secure_key = ($row['secure_key'] ? $row['secure_key'] : md5(uniqid(rand(), true)));
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
			$order->total_products = ($row['total_products'] > 0) ? $row['total_products'] : 0;
			$order->total_products_wt = ($row['total_products_wt'] > 0) ? $row['total_products_wt'] : 0;
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

		return $this->nb_rows;
	}

	/**
	* Transfert des détails de commande
	**/
	public function transfer_ps_order_detail($id_min = null) {

		$this->connectToDB();
		$this->nb_rows = 0;

		if($id_min) $id = $id_min;
		else $id = $this->getMax('id_order_detail', 'ps_order_detail');

		$sql = "SELECT *, od.id_order_detail FROM ps_order_detail od LEFT JOIN ps_activis_order_extends_detail oed ON (od.id_order_detail = oed.id_order_detail)";
		if($id) $sql .= " WHERE od.id_order_detail > $id";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			$detail = new OrderDetail($row['id_order_detail']);
			$update = !empty($order->id);

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

			$detail->record($udpate);
			$this->nb_rows++;
		}

		return $this->nb_rows;
	}

	/**
	* [FIX] Récupération des frais de ports
	**/
	public function transfer_FIX_DELIVERY_FEES() {

		$this->connectToDB();
		$this->nb_rows = 0;

		$result = $this->old_db->query("SELECT id_order_detail, specific_price FROM ps_order_detail ORDER BY id_order_detail DESC");
		while($row = $result->fetch_assoc()) {

			$detail = new OrderDetail($row['id_order_detail']);
			if(!$detail->id) continue;

			$infos = unserialize($row['specific_price']);
			if(isset($infos['shipping_price'])) {

				$detail->delivery_fees = $infos['shipping_price'];
				$detail->save();
				$this->nb_rows++;
			}
		}

		return $this->nb_rows;
	}

	/**
	* Transfert des états de commande
	**/
	private function transfer_ps_order_state() {

		$this->connectToDB();
		$this->nb_rows = 0;

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

		return $this->nb_rows;
	}

	/**
	* Transfert des historiques de changement de statut
	**/
	public function transfer_ps_order_history($min_id = null) {

		$this->connectToDB();
		$this->nb_rows = 0;

		if($min_id) $id = $min_id;
		else $id = $this->getMax('id_order_history', 'ps_order_history');

		$sql = "SELECT * FROM ps_order_history";
		if(isset($id) and $id) $sql .= " WHERE id_order_history > $id";

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

		return $this->nb_rows;
	}

	/**
	* [FIX] Récupération des dates d'historiques de commandes
	**/
	public function transfer_FIX_HISTORY() {

		$this->connectToDB();
		$this->nb_rows = 0;

		$result = $this->old_db->query("SELECT id_order_history, date_add FROM ps_order_history ORDER BY id_order_history DESC");
		while($row = $result->fetch_assoc()) {

			$history = new OrderHistory($row['id_order_history']);
			if(!$history->id) continue;

			$history->date_add = $row['date_add'];
			$history->save();

			$this->nb_rows++;
		}

		return $this->nb_rows;
	}

	/**
	* Transfert des modes de paiements
	**/
	public function transfer_ps_order_payment() {

		$this->connectToDB();
		$this->nb_rows = 0;

		$result = $this->old_db->query("SELECT * FROM ps_order_payment ORDER BY id_order_payment");
		while($row = $result->fetch_assoc()) {

			$payment = new OrderPayment($row['id_order_payment']);
			$update = !empty($payment->id);

			$payment->id = $prow['id_order_payment'];
			$payment->order_reference = $row['order_reference'];
		    $payment->id_currency = $row['id_currency'];
		    $payment->amount = $row['amount'];
		    $payment->payment_method = utf8_encode($row['payment_method']);
		    $payment->conversion_rate = $row['conversion_rate'];
		    $payment->transaction_id = $row['transaction_id'];
		    $payment->card_number = $row['card_number'];
		    $payment->card_brand = $row['card_brand'];
		    $payment->card_expiration = $row['card_expiration'];
		    $payment->card_holder = $row['card_holder'];
		    $payment->date_add = $row['date_add'];

			$payment->record($update);
		    $this->nb_rows++;
		}

		return $this->nb_rows;
	}

	/**
	* Transfert des devis
	**/
	public function transfer_ps_activis_devis($min_id = null) {

		$this->connectToDB();
		$this->nb_rows = 0;

		if($min_id) $id = $min_id;
		else $id = $this->getMax(Quotation::TABLE_PRIMARY, _DB_PREFIX_.Quotation::TABLE_NAME);

		$states[1] = Quotation::STATUS_REFUSED;
		$states[2] = Quotation::STATUS_WAITING;
		$states[3] = Quotation::STATUS_VALIDATED;

		$sql = "SELECT * FROM ps_activis_devis d INNER JOIN ps_activis_devis_shop s ON (d.id_activis_devis = s.id_activis_devis) WHERE 1";
		if($id) $sql .= " AND d.id_activis_devis > $id";
		$sql .= " AND d.hash <> 'Deleted' AND d.date_add >= '2016-01-01 00:00:00' GROUP BY d.id_activis_devis";

		$result = $this->old_db->query($sql);
		while($row = $result->fetch_assoc()) {

			if($row['hash'] != "Deleted") {

				$quotation = new Quotation($row['id_activis_devis']);
				$update = !empty($quotation->id);

				$quotation->id = $row['id_activis_devis'];
				$quotation->reference = $row['hash'];
				$quotation->status = (isset($states[$row['id_state']]) ? $states[$row['id_state']] : Quotation::STATUS_OVER);
				$quotation->id_customer = Customer::customerExists($row['email'], true);
				$quotation->email = utf8_encode($row['mail_cc']);
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

		return $this->nb_rows;
	}

	/**
	* Récupère les emails et la provenance des devis
	**/
	public function transfer_FIX_QUOTATION() {

		$this->connectToDB();
		$this->nb_rows = 0;

		$origins['telephone'] = Quotation::ORIGIN_PHONE;
		$origins['mail'] = Quotation::ORIGIN_MAIL;
		$origins['fax'] = Quotation::ORIGIN_FAX;
		$origins['autres'] = Quotation::ORIGIN_OTHERS;

		$result = $this->old_db->query("SELECT id_activis_devis, email, customer_origin FROM ps_activis_devis ORDER BY id_activis_devis DESC");
		while($row = $result->fetch_assoc()) {

			$quotation = new Quotation($row['id_activis_devis']);
			if(!$quotation->id) continue;

			if($row['email']) $quotation->email = $row['email'];
			if($row['customer_origin'] and isset($origins[$row['customer_origin']])) $quotation->origin = $origins[$row['customer_origin']];

			$quotation->save();
			$this->nb_rows++;
		}

		return $this->nb_rows;
	}

	/**
	* Transfert des lignes produits pour les devis
	**/
	public function transfer_ps_activis_devis_line($min_id = null) {

		$this->connectToDB();
		$this->nb_rows = 0;

		if($min_id) $id = $min_id;
		else $id = $this->getMax(QuotationLine::TABLE_PRIMARY, _DB_PREFIX_.QuotationLine::TABLE_NAME);

		$sql = "SELECT * FROM ps_activis_devis_line";
		if($id) $sql .= " WHERE id_activis_devis_line NOT IN ($id)";

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

		return $this->nb_rows;
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
	* [FIX] Récupère les fournisseurs des lignes devis
	**/
	private function transfer_FIX_QUOTATIONS() {

		$ids = Db::getInstance()->executeS("SELECT DISTINCT(id_product) FROM "._DB_PREFIX_.QuotationLine::TABLE_NAME." WHERE id_product IS NOT NULL AND id_product <> 0");
		$ids = implode(',', array_map(function($e) { return $e['id_product']; }, $ids));

		foreach(Db::getInstance()->executeS("SELECT id_product, id_supplier FROM ps_product WHERE id_product IN($ids) AND id_supplier <> 0") as $row)
			Db::getInstance()->execute("UPDATE "._DB_PREFIX_.QuotationLine::TABLE_NAME." SET id_supplier = ".$row['id_supplier']." WHERE id_product = ".$row['id_product']);
	}

	/**
	* [FIX] Récupère les données de livraison des produits (en cas de modification après la récupération des données)
	**/
	private function transfer_FIX_DELIVERY_ORDERS() {

		$this->connectToDB();
		$this->nb_rows = 0;

		$result = $this->old_db->query("SELECT * FROM ps_activis_order_extends_detail ORDER BY id_activis_order_extends_detail");
		while($row = $result->fetch_assoc()) {

			$detail = new OrderDetail($row['id_order_detail']);
			if(!$detail->id) continue;

			$detail->day = $row['day'];
			$detail->week = $row['week'];
			$detail->comment = utf8_encode($row['comment']);
			$detail->notification_sent = $row['notified'];

			$detail->save();
			$this->nb_rows++;
		}

		return $this->nb_rows;
	}

	/**
	* [FIX] récupèration des dates de commandes
	**/
	private function transfer_FIX_DATE_ORDERS() {

		$this->connectToDB();
		$this->nb_rows = 0;

		$result = $this->old_db->query("SELECT id_order, date_add, date_upd FROM ps_orders ORDER BY id_order DESC");
		while($row = $result->fetch_assoc()) {

			$order = new Order($row['id_order']);
			if(!$order->id) continue;

			$order->date_add = $row['date_add'];
			$order->date_upd = $row['date_upd'];
			
			$order->save();
			$this->nb_rows++;
		}

		return $this->nb_rows;
	}

	/**
	* [FIX] récupèration des dates de clients
	**/
	private function transfer_FIX_DATE_CUSTOMERS() {

		$this->connectToDB();
		$this->nb_rows = 0;

		$result = $this->old_db->query("SELECT id_customer, date_add, date_upd FROM ps_customer ORDER BY id_customer DESC");
		while($row = $result->fetch_assoc()) {

			$customer = new Customer($row['id_customer']);
			if(!$customer->id) continue;

			$customer->date_add = $row['date_add'];
			$customer->date_upd = $row['date_upd'];
			
			$customer->save();
			$this->nb_rows++;
		}

		return $this->nb_rows;
	}

	/**
	* [FIX] Facturation commandes
	**/
	private function transfer_FIX_FACTURATION() {

		$this->connectToDB();
		$this->nb_rows = 0;

		$result = $this->old_db->query("SELECT id_order, invoice_number, invoice_date, no_recall FROM ps_activis_order_extends_invoice_customization ORDER BY id_order DESC");
		while($row = $result->fetch_assoc()) {

			$order = new Order($row['id_order']);
			if(!$order->id) continue;

			$order->invoice_number = $row['invoice_number'];
			$order->invoice_date = $row['invoice_date'];
			$order->no_recall = $row['no_recall'];

			$order->save();
			$this->nb_rows++;
		}

		return $this->nb_rows;
	}

	/**
	* [FIX] récupèration des OA
	**/
	private function transfer_ps_oa() {

		$this->connectToDB();
		$this->nb_rows = 0;

		$result = $this->old_db->query("SELECT * FROM ps_oa");
		while($row = $result->fetch_assoc()) {

			$oa = new OA($row['id']);
			$update = !empty($oa->id);

			$oa->id_order = $row['id_order'];
			$oa->id_supplier = $row['id_supplier'];
			$oa->code = $row['code'];
			$oa->date_BC = $row['date_BC'];
			$oa->date_BL = $row['date_BL'];

			$oa->record($update);
			$this->nb_rows++;
		}

		return $this->nb_rows++;
	}

	/**
	* Transfert des comptes clients vers Rolléco
	**/
	public function updateOldCustomers() {

		foreach(Db::getInstance()->executeS("SELECT id_customer, email, rollcash FROM ps_customer WHERE id_shop <> 1") as $row) {

			// Vérifier la présence du client sur rolléco
			$id = Db::getInstance()->getValue("SELECT id_customer FROM ps_customer WHERE email ='".$row['email']."' AND id_shop = 1");

			// Le client n'est pas sur Rolléco, on le transfère
			if(!$id)
				Db::getInstance()->execute("UPDATE ps_customer SET id_shop = 1 WHERE id_customer = ".$row['id_customer']);
			// Le client est déjà sur Rolléco, on affecte ses données au client trouvé avant de la supprimer
			else {

				// On transfère son solde si nécessaire
				if($row['rollcash'] > 0)
					Db::getInstance()->execute("UPDATE ps_customer SET rollcash = rollcash + ".(float)$row['rollcash']." WHERE id_customer = $id");

				// Adresses, paniers, messages, commandes, réductions, devis, prix personnalisés
				foreach(array('ps_address', 'ps_cart', 'ps_message', 'ps_orders', 'ps_order_slip', 'ps_quotation', 'ps_specific_price') as $table)
					Db::getInstance()->execute("UPDATE $table SET id_customer = ".$row['id_customer']." WHERE id_customer = $id");

				// On supprime le client sur l'ancienne boutique
				Db::getInstance()->execute("DELETE FROM ps_customer WHERE id_customer = ".$row['id_customer']);
			}

		}
	}

	/**
	* Transfert des commandes clients vers Rolléco
	**/
	public function updateOldOrders() {

		// On affecte simplement les commandes chez Rolléco
		Db::getInstance()->execute("UPDATE ps_orders SET id_shop = 1");
		// On change aussi le détail des commandes
		Db::getInstance()->execute("UPDATE ps_order_detail SET id_shop = 1");
	}

	/**
	* Transfert des paniers clients vers Rolléco
	**/
	public function updateOldCarts() {

		// On affecte simplement les paniers chez Rolléco
		Db::getInstance()->execute("UPDATE ps_cart SET id_shop = 1");
		// On change aussi le détail des paniers
		Db::getInstance()->execute("UPDATE ps_cart_product SET id_shop = 1");
	}

}