<?php

class Order extends OrderCore {

	const ALL_PRODUCTS = 1;
	const ONLY_QUOTATIONS = 2;
	const ONLY_PRODUCTS = 3;

	const NOT_EXPORTED = 0;
	const STANDBY_EXPORT = 1;
	const EXPORTED = 2;

	/** Exporté vers M3 **/
	public $exported = 0;

	/** Référence commande M3 **/
	public $m3_reference;

	/** @var string Internal Reference */
	public $internal_reference;

	/** @var string Delivery Information */
	public $delivery_information;

	/** @var string Supplier Information */
	public $supplier_information;

	/** @var string Invoice comment */
	public $invoice_comment;
	
	/** @var bool No recall */
	public $no_recall = false;

	/** @var bool display with taxes **/
	public $display_with_taxes = true;

	/** variables temporaires **/
	private $payment_deadline = false;
	private $state;
	private $shop;
	private $address_invoice;
	private $address_delivery;

	/**
	* OVERRIDE : ajout de champs
	**/
	public function __construct($id_order = null, $id_lang = null, $id_shop = null) {

		self::$definition['fields']['m3_reference'] = array('type' => self::TYPE_STRING);
		self::$definition['fields']['internal_reference'] = array('type' => self::TYPE_STRING);
		self::$definition['fields']['delivery_information'] = array('type' => self::TYPE_STRING);
		self::$definition['fields']['supplier_information'] = array('type' => self::TYPE_STRING);
		self::$definition['fields']['invoice_comment'] = array('type' => self::TYPE_STRING);
		self::$definition['fields']['display_with_taxes'] = array('type' => self::TYPE_BOOL);
		self::$definition['fields']['no_recall'] = array('type' => self::TYPE_BOOL);
		self::$definition['fields']['exported'] = array('type' => self::TYPE_INT);
		
		parent::__construct($id_order, $id_lang, $id_shop);
	}

	/**
	* Retourne les informations concernant l'export vers M3
	* UTILISATION : page commande
	* @return array
	**/
	public function getExportedData() {

		$data[self::NOT_EXPORTED] = array('class'=>"danger", 'info'=>"Non exporté");
		$data[self::STANDBY_EXPORT] = array('class'=>"warning", 'info'=>"En attente de validation");
		$data[self::EXPORTED] = array('class'=>"success", 'info'=>"Exporté");;

		return $data[$this->exported];
	}

	/**
	* Retourne un objet date
	* @return DateTime
	**/
	public function getDate($name) {
		return new DateTime($this->{$name});
	}

	/**
	* Retourne l'information PROFORMA
	* @return bool
	**/
	public function isProforma() {
		return $this->getState()->proforma;
	}

	/**
	* Retourne l'information ACQUITTEE
	* @return bool
	**/
	public function isAcquitted() {
		return ($this->getState()->shipped and $this->getState()->paid);
	}

	/**
	* OVERRIDE : modification de la référence
	**/
	public static function generateReference() {

		$shop = Context::getContext()->shop;
		$id = (int)Db::getInstance()->getValue('SELECT id_order FROM '._DB_PREFIX_.'orders ORDER BY id_order DESC') + 1;

		return $shop->reference_prefix.str_pad($id, $shop->reference_length, '0', STR_PAD_LEFT);
    }
    
    /**
    * Retourne la date limite de paiement
    **/
    public function getPaymentDeadline() {

    	if(!$this->payment_deadline and $this->invoice_date and $this->invoice_date != '0000-00-00') {

    		$this->payment_deadline = DateTime::createFromFormat('Y-m-d H:i:s', $this->invoice_date);

    		$delay = Configuration::get('PAYMENT_TIME_LIMIT');
    		if($delay) $this->payment_deadline->modify("+$delay days");
    	}

    	return $this->payment_deadline;
    }

    /**
    * Retourne l'adresse de facturation
	**/
	public function getAddressInvoice() {
		
		if(!$this->address_invoice)
			$this->address_invoice = new Address($this->id_address_invoice);

		return $this->address_invoice;
	}

	/**
	* Retourne l'adresse de livraison
	**/
	public function getAddressDelivery() {

		if(!$this->address_delivery)
			$this->address_delivery = new Address($this->id_address_delivery);

		return $this->address_delivery;
	}

	/**
	* Retourne l'état actuel de la commande
	**/
	public function getState() {

		if(!$this->state)
			$this->state = new OrderState($this->current_state, 1, $this->id_shop);

		return $this->state;
	}

	/** 
	* Retourne la boutique de la commande
	**/
	public function getShop() {

		if(!$this->shop)
			$this->shop = new Shop($this->id_shop);

		return $this->shop;
	}
	
	/**
	* Retourne la liste des produits (objets)
	* @var int Id_supplier Ne retourner que les produits associés à ce fournisseur
	**/
	public function getDetails($id_supplier = null) {

		$data = array();
		if($this->id) {
			$sql = "SELECT id_order_detail FROM ps_order_detail WHERE id_order = ".$this->id;
			if($id_supplier) $sql .= " AND id_product_supplier = $id_supplier";
			
			foreach(Db::getInstance()->executeS($sql) as $row)
				$data[] = new OrderDetail($row['id_order_detail']);
		}

		return $data;
	}

	/**
	* Override : Si j'ai des produtis dans ma foutue commande, tu me retournes mes produits bordel de merde
    * @return array
    **/
    public function getCartProducts() {
    	
        $product_id_list = array();
        $products = $this->getProducts();
        foreach ($products as &$product) {
            $product['id_product_attribute'] = $product['product_attribute_id'];
            $product['cart_quantity'] = $product['product_quantity'];
            $product_id_list[] = $this->id_address_delivery.'_'
                .$product['product_id'].'_'
                .$product['product_attribute_id'].'_'
                .(isset($product['id_customization']) ? $product['id_customization'] : '0');
        }

        return $products;
    }

	/**
	* Retourne les frais de port de la commande
	**/
	public function getDeliveryPrice() {

		$total = 0;
		foreach($this->getDetails() as $details)
			$total += $this->total_shipping_price_tax_excl;

		return $total;
	}
	
	/**
	* Calcul un chiffre d'affaire sur une période de temps
	* @param bool $use_taxes
	* @param mixed $date_begin
	* @param mixed $date_end
	* @param int $id_shop
	* @return float
	**/
	public static function  sumTurnover($use_taxes = false, $date_begin = false, $date_end = false, $id_shop = null) {

		if($use_taxes) $column = 'total_paid_tax_incl';
		else $column = 'total_paid_tax_excl';

		$sql = "SELECT SUM($column) FROM ps_orders o, ps_order_state os WHERE o.current_state = os.id_order_state AND os.paid = 1";

		if($date_begin) {
			
			if(is_object($date_begin))
				$date_begin = $date_begin->format('Y-m-d');

			$sql .= " AND o.date_add >= '$date_begin 00:00:00'";
		}

		if($date_end) {

			if(is_object($date_end))
				$date_end = $date_end->format('Y-m-d');

			$sql .= " AND o.date_add <= '$date_end 23:59:59'";
		}

		if($id_shop)
			$sql .= " AND id_shop = $id_shop";

		return (float)Db::getInstance()->getValue($sql);
	}

	/**
	* Compte le nombre de commandes sur une période de temps
	* @param mixed $date_begin
	* @param mixed $date_end
	* @param int $id_shop
	* @return int 
	**/
	public static function count($date_begin = null, $date_end = null, $id_shop = null) {

		$sql = "SELECT COUNT(*) FROM ps_orders o, ps_order_state os WHERE o.current_state = os.id_order_state AND os.paid = 1";

		if($date_begin) {
			
			if(is_object($date_begin))
				$date_begin = $date_begin->format('Y-m-d');

			$sql .= " AND o.date_add >= '$date_begin 00:00:00'";
		}

		if($date_end) {

			if(is_object($date_end))
				$date_end = $date_end->format('Y-m-d');

			$sql .= " AND o.date_add <= '$date_end 23:59:59'";
		}

		if($id_shop)
			$sql .= " AND id_shop = $id_shop";
		
		return (int)Db::getInstance()->getValue($sql);
	}

	/**
	* Retourne la liste des produits commandés par un client
	* @param int $id_customer
	* @return array
	**/
	public static function findOrderedProducts($id_customer) {
		return Db::getInstance()->executeS("SELECT DISTINCT(l.id_product), l.name FROM ps_orders o, ps_order_detail d, ps_product_lang l WHERE o.id_order = d.id_order AND d.product_id = l.id_product AND o.id_customer = $id_customer");
	}
	
	/**
	* Retourne un ID command en fonction d'une reference
	* @param string $reference
	* @return int|false
	**/
	public static function getIdByReference($reference) {

		if(!$reference)
			return false;

		return Db::getInstance()->getValue("SELECT id_order FROM ps_orders WHERE reference = '$reference' OR internal_reference = '$reference'");
	}

	/**
	* Retourne une liste fitrée d'ID commande
	* UTILISATION : page de résultats
	* @param array $options
	* @return array
	**/ 
	public static function findIds($options) {

		$sql = "SELECT DISTINCT(o.id_order) FROM "._DB_PREFIX_."orders o";

		if(isset($options['payment_methods']))
			$sql .= " INNER JOIN ps_order_payment p ON o.reference = p.order_reference AND p.payment_method IN (".implode(',', $options['payment_methods']).")";

		if(isset($options['customer_types']))
			$sql .= " INNER JOIN ps_customer c ON o.id_customer = c.id_customer AND c.id_account_type IN(".implode(',', $options['customer_types']).")";

		$sql .= " WHERE 1";

		if(isset($options['quotations']))
			$sql .= " AND EXISTS (SELECT d.id_order_detail FROM ps_order_detail d WHERE id_order = o.id_order AND d.id_quotation_line IS NOT NULL)";

		if(isset($options['date_begin'])) {
			if(!is_string($options['date_begin'])) $options['date_begin'] = $options['date_begin']->format('Y-m-d 00:00:00');
			$sql .= " AND o.date_add >= '".$options['date_begin']."'";
		}

		if(isset($options['date_end'])) {
			if(!is_string($options['date_end'])) $options['date_end'] = $options['date_end']->format('Y-m-d 23:59:59');
			$sql .= " AND o.date_add <= '".$options['date_end']."'";
		}

		if(isset($options['shops']))
			$sql .= " AND o.id_shop IN (".implode(',', $options['shops']).")";

		$exclude_states = Configuration::get('EXPORT_EXCLUDED_STATES');
		if($exclude_states)
			$sql .= " AND o.current_state NOT IN ($exclude_states)";

		return array_map(function($e) { return $e['id_order']; }, Db::getInstance()->executeS($sql));
	}

	/**
	* Calcule le montant total en fonction d'une liste d'ID commande
	* UTILISATION : page de résultats
	* @param array $ids
	* @param bool $use_taxes
	* @param bool $quotation
	* @return float
	**/
	public static function sumProducts($ids, $use_taxes = false, $quotation = self::ALL_PRODUCTS) {

		if(!is_array($ids) || empty($ids))
			return 0;
		
		if($use_taxes) $tax = "incl";
		else $tax = "excl";

		$sql = "SELECT SUM(total_price_tax_$tax) FROM ps_order_detail WHERE id_order IN (".implode(',', $ids).")";
		if($quotation == self::ONLY_QUOTATIONS) $sql .= " AND id_quotation_line IS NOT NULL";
		if($quotation == self::ONLY_PRODUCTS) $sql .= " AND id_quotation_line IS NULL";

		return Db::getInstance()->getValue($sql);
	}

	/**
	* Calcule le coût d'achat total en fonction d'une liste d'ID commande
	* UTILISATION : page de résultats, page commande
	* @param array $ids
	* @param bool $use_taxes
	* @param bool $quotation
	* @return float
	**/
	public static function sumBuyingPrice($ids, $use_taxes = false, $quotation = self::ALL_PRODUCTS) {

		$value = 0;
		foreach($ids as $id) {

			$order = new Order($id);
			foreach($order->getDetails() as $detail)
				if($quotation == self::ALL_PRODUCTS OR ($quotation == self::ONLY_QUOTATIONS and $detail->id_quotation_line) OR ($quotation == self::ONLY_PRODUCTS and !$detail->id_quotation_line))
				$value += $detail->getTotalBuyingPrice();
		}

		return $value * ($use_taxes ? 1.2 : 1);
	}

	/**
	* Retourne la liste des types de paiement utilisés
	* @return array
	**/
	public function getPaymentList() {
		return Db::getInstance()->executeS("SELECT DISTINCT(payment) FROM ps_orders");
	}
	
	/**
	* Retrouve la ligne d'option de commande par pourcentage de la commande
	* @return OrderDetail
	**/
	public function getPercentOption() {

		$id = Db::getInstance()->getValue("SELECT od.id_order_detail FROM "._DB_PREFIX_."order_detail od, "._DB_PREFIX_.OrderOption::TABLE_NAME." oo WHERE od.product_reference = oo.reference AND oo.type = ".OrderOption::TYPE_PERCENT." AND od.id_order = ".$this->id);
		return new OrderDetail($id);
	}

	/**
	* Met à jour les coûts de la commande (suite à une modification)
	**/
	public function updateCosts() {

		$ids = array($this->id);

		$roll = $this->getPercentOption();
		if($roll->id) {

			$roll->unit_price_tax_excl = ((self::sumProducts($ids, false, self::ALL_PRODUCTS) - $roll->total_price_tax_excl) * 2.5) / 100;
			$roll->unit_price_tax_incl = ((self::sumProducts($ids, true, self::ALL_PRODUCTS) - $roll->total_price_tax_incl) * 2.5) / 100;

			$roll->total_price_tax_excl = $roll->unit_price_tax_excl;
			$roll->total_price_tax_incl = $roll->unit_price_tax_incl;

			$roll->save();
		}

		$this->total_products = self::sumProducts($ids, false, self::ALL_PRODUCTS);
		$this->total_products_wt = self::sumProducts($ids, true, self::ALL_PRODUCTS);

		$this->total_paid_tax_excl = $this->total_products + $this->total_shipping_tax_excl - $this->total_discounts_tax_excl;
		$this->total_paid_tax_incl = $this->total_products_wt + $this->total_shipping_tax_incl - $this->total_discounts_tax_incl;
		$this->total_paid = $this->total_paid_tax_incl;
		
		$this->save();
	}

	/**
	* Création du JSON envoyé vers M3
	* UTILISATION : CRON module export vers M3
	* @return string
	**/
	public function getJson() {
		
		$customer = $this->getCustomer();

		$addresses[1] = $this->getAddressDelivery();
		$addresses[3] = $this->getAddressInvoice();

		$date = explode(' ', $this->date_add);
		$date = str_replace("-", "", $date[0]);

		/**
		* En tête
		**/
		$data = array();

    	$data['numClient'] = $customer->reference;
    	$data['payeur'] = $customer->reference;
    	$data['langue'] = "FR";
    	$data['numCommandeClient'] = $this->reference;
    	$data['numCotation'] = $this->reference;
    	$data['remiseCom'] = $this->total_discounts_tax_incl;
    	$data['adrLivraison'] = $addresses[1]->reference;
    	$data['devise'] = "EUR";
    	$data['dateCommande'] = $date;
    	//$data['OTDP'] = "Pourcentage de remise total";
    	$json['entete'] = $data;

    	/**
    	* Adresses livraison et facturation
    	**/
    	$data = array();

    	$names[1] = "livraison";
    	$names[3] = "facturation";

    	foreach($addresses as $key => $address) {
    		$row = array();

	    	//$row['ORNO'] = "Numéro de commande temporaire retournée par l'API d'entete";
	    	$row['typeAdresse'] = $key;
	    	if($address->reference) $row['idAdresse'] = $address->reference;
	    	$row['nomClient'] = trim($address->firstname." ".$address->lastname);
	    	$row['adresse'] = $address->address1;
	    	if($address->address2) $row['adresseComp'] = $address->address2;
	    	$row['adresseLigne3'] = trim($address->postcode." ".$address->city);
	    	if($address->postcode) $row['cp'] = $address->postcode;
	    	$row['ville'] = $address->city;
	    	$row['adresseLigne4'] = $address->country;
	    	if($address->phone) $row['numTel'] = $address->phone;
	    	if($address->phone_mobile) $row['numTel2'] = $address->phone_mobile;
	    	$row['pays'] = "FR";
	    	//$row['VRNO'] = "Code TVA intracommunautaire";
	    	$row['votreReference1'] = $this->reference;

	    	$json[$names[$key]] = $row;
	    }

    	/**
    	* Lignes produits
    	**/
    	$data = array();

    	foreach($this->getProducts() as $product) {
    		$row = array();

    		//$row['ORNO'] = "Numéro de commande temporaire retournée par l'API d'entete";
    		$row['numArticle'] = $product['product_supplier_reference'];
    		$row['qteCommande'] = $product['product_quantity'];
    		$row['NumCommandeClient'] = $this->reference;
    		$row['pdvente'] = $product['total_price_tax_incl'];
    		//$row['DIP1'] = "Promo en %";
    		//$row['CUOR'] = $this->reference;

    		$data[] = $row;
    	}

    	$json['lignesArticle'] = $data;

    	return json_encode($json);
	}

}