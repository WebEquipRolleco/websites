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

	public $order_slip_number;

	/** variables temporaires **/
	private $payment_deadline = false;
	private $state;
	private $shop;
	private $address_invoice;
	private $address_delivery;
	private $cart;

	/**
	* OVERRIDE : ajout de champs
    * @see ObjectModel::$definition
    **/
    public static $definition = array(
        'table' => 'orders',
        'primary' => 'id_order',
        'fields' => array(
            'id_address_delivery' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_address_invoice' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_cart' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_currency' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_shop_group' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_shop' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_lang' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_customer' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_carrier' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'current_state' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'secure_key' =>                array('type' => self::TYPE_STRING, 'validate' => 'isMd5'),
            'payment' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'module' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isModuleName', 'required' => true),
            'recyclable' =>                array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'gift' =>                        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'gift_message' =>                array('type' => self::TYPE_STRING, 'validate' => 'isMessage'),
            'mobile_theme' =>                array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'total_discounts' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_discounts_tax_incl' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_discounts_tax_excl' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_paid' =>                array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'total_paid_tax_incl' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_paid_tax_excl' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_paid_real' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'total_products' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'total_products_wt' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'total_shipping' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_shipping_tax_incl' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_shipping_tax_excl' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'carrier_tax_rate' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'total_wrapping' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_wrapping_tax_incl' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_wrapping_tax_excl' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'round_mode' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'round_type' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'shipping_number' =>            array('type' => self::TYPE_STRING, 'validate' => 'isTrackingNumber'),
            'conversion_rate' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => true),
            'invoice_number' =>            array('type' => self::TYPE_INT),
            'delivery_number' =>            array('type' => self::TYPE_INT),
            'invoice_date' =>                array('type' => self::TYPE_DATE),
            'delivery_date' =>                array('type' => self::TYPE_DATE),
            'valid' =>                        array('type' => self::TYPE_BOOL),
            'reference' =>                    array('type' => self::TYPE_STRING),
            'internal_reference' =>				array('type' => self::TYPE_STRING),
            'm3_reference' =>					array('type' => self::TYPE_STRING),
            'delivery_information' => 			array('type' => self::TYPE_STRING),
			'supplier_information' => 			array('type' => self::TYPE_STRING),
			'invoice_comment' =>			 	array('type' => self::TYPE_STRING),
			'display_with_taxes' => 			array('type' => self::TYPE_BOOL),
			'no_recall' => 						array('type' => self::TYPE_BOOL),
			'exported' => 						array('type' => self::TYPE_INT),
            'date_add' =>                    array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>                    array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'order_slip_number' =>             array('type' => self::TYPE_STRING),
        ),
    );

	/**
    * Get a collection of orders using invoice number
    * @param string $reference
    * @return PrestaShopCollection Collection of Order
    **/
    public static function getByInvoiceReference($reference)
    {
        $orders = new PrestaShopCollection('Order');
        $orders->where('invoice_number', 'like', $reference);

        return $orders;
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
	* Retourne le panier client associé
	* @return Cart
	**/
	public function getCart() {

		if($this->id_cart and !$this->cart)
			$this->cart = new Cart($this->id_cart);

		return $this->cart;
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
    		if($delay) $this->payment_deadline->modify("+$delay day");
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
	* Override : forcer les frais de ports de la commande
	* @return array
	**/
	public function getProductsDetail() {

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        	SELECT *, od.delivery_fees
        	FROM `'._DB_PREFIX_.'order_detail` od
        	LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.id_product = od.product_id)
        	LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON (ps.id_product = p.id_product AND ps.id_shop = od.id_shop)
        	WHERE od.`id_order` = '.(int)$this->id);
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
	* Retourne la liste des produits commandés par un client
	* @param int $id_customer
	* @return array
	**/
	public static function findOrderedProducts($id_customer) {
		
		$products = array(); 

		if($id_customer)
			foreach(Db::getInstance()->executeS("SELECT DISTINCT(d.product_id) FROM ps_orders o, ps_order_detail d WHERE o.id_order = d.id_order AND o.id_customer = $id_customer") as $row)
				$products[] = new Product($row['product_id'], true, 1);

		return $products;
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

		return $value;
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

	/**
	* Retourne une liste fitrée d'ID commande
	* FONCTION GLOBALE DE RECHERCHE DE COMMANDE
	* @param array $options
	* @return array
	**/ 
	public static function findIds($options) {

		$sql = "SELECT DISTINCT(o.id_order) FROM "._DB_PREFIX_."orders o";

		// Filtrer en fonction des méthodes de paiement
		if(isset($options['payment_methods']))
			$sql .= " INNER JOIN ps_order_payment p ON (o.reference = p.order_reference AND p.payment_method IN (".implode(',', $options['payment_methods'])."))";

		// Filter en fonction des types de clients
		if(isset($options['customer_types']))
			$sql .= " INNER JOIN ps_customer c ON (o.id_customer = c.id_customer AND c.id_account_type IN(".implode(',', $options['customer_types'])."))";

		// Filter sur l'état 'payée' de la commande
		if(isset($options['paid']))
			$sql .= " INNER JOIN ps_order_state os ON (o.current_state = os.id_order_state AND os.paid = ".$options['paid'].")";

		$sql .= " WHERE 1";

		// Filter en fonction du type de produit concerné (nature ou devis)
		if(isset($options['quotations']))
			$sql .= " AND EXISTS (SELECT d.id_order_detail FROM ps_order_detail d WHERE d.id_order = o.id_order AND (d.id_quotation_line IS NOT NULL AND d.id_quotation_line != 0))";

		// Borner la recherche à une date minimum
		if(isset($options['date_begin'])) {
			if(!is_string($options['date_begin'])) $options['date_begin'] = $options['date_begin']->format('Y-m-d 00:00:00');
			$sql .= " AND o.date_add >= '".$options['date_begin']."'";
		}

		// Borner la recherche à un date maximum
		if(isset($options['date_end'])) {
            $options['date_end'] =  date('Y-m-d H:i:s', strtotime($options['date_end'] . ' +1 day'));
			if(!is_string($options['date_end'])) $options['date_end'] = $options['date_end']->format('Y-m-d 00:00:00');
			$sql .= " AND o.date_add <= '".$options['date_end']."'";
		}

		// Filtrer en fonction des boutiques
		if(isset($options['shops']))
			$sql .= " AND o.id_shop IN (".implode(',', $options['shops']).")";

		// Etats exclus de la recherche
		$exclude_states = Configuration::get('EXPORT_EXCLUDED_STATES');
		if($exclude_states)
			$sql .= " AND o.current_state NOT IN ($exclude_states)";

		return array_map(function($e) { return $e['id_order']; }, Db::getInstance()->executeS($sql));
	}

	/**
	* Compte le nombre de commandes sur une période de temps
	* @param mixed $date_begin
	* @param mixed $date_end
	* @param int $id_shop
	* @return int 
	**/
	public static function count($date_begin = null, $date_end = null, $id_shop = null) {

		$options['date_begin'] = $date_begin;
		$options['date_end'] = $date_end;
		if($id_shop) $options['shops'][] = $id_shop;

		return count(self::findIds($options));
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

		//$options['paid'] = 1;
		if($date_begin) $options['date_begin'] = $date_begin;
        if($date_end) $options['date_end'] = $date_end;
        if($id_shop) $options['shops'][] = $id_shop;

        $ids = Order::findIds($options);
        if(!$ids) return 0;

		if($use_taxes) $column = 'total_paid_tax_incl';
		else $column = 'total_paid_tax_excl';

		$sql = "SELECT SUM($column) FROM ps_orders o WHERE o.id_order IN (".implode(',', $ids).")";
		return (float)Db::getInstance()->getValue($sql);
	}

    /**
     * Methode pour recuperer le status de paiement
     *
     * @return bool un boolean true si la commande a ete paye,
     * false sinon
     */
	public function isPaid(){
        foreach($this -> getStatusHistory() as $status){
            if ($status["paid"])
                return true;
        }
        return false;
    }

    /**
     * Methode pour recuperer la date de paiement
     *
     * @return string la date si la commande est paye, vide sinon
     */
    public function getDatePaid(){
        if (!$this -> isPaid())
            return "";

        foreach($this -> getStatusHistory() as $status)
            if ($status["paid"])
                return $status["date_add"] ;

        return "";
    }


    /**
     * Methode pour recuperer tous les status d'une commande
     *
     * @return la liste des status de la commande
     * @throws PrestaShopDatabaseException
     */
    public function getStatusHistory(){
	    return DB::getInstance()->executeS("select * from ps_order_history as oh inner join ps_order_state as os where id_order =" . $this->id . " and oh.id_order_state = os.id_order_state" );
    }

    /**
     * Methode pour recuperer le nom
     */
    public function renderString($name){
        return str_replace('{order_reference}', $this ->reference, $name);
    }

    /**
     * Methode pour recuperer la date
     */
    public function getDateOrder(){
        return DateTime::createFromFormat('d/m/Y', $this -> date_add);
    }

    /**
     * Methode pour recuperer la date d'echeance
     */
    public function getDeadline(){
        $date = DateTime::createFromFormat("Y-m-d H:i:s", $this -> invoice_date);
        $date -> modify("+45 day");
        return $date;

    }

    public function getQuotation(){
        if($this->id_cart) {
            $id = Db::getInstance()->getValue("SELECT q.id_quotation FROM "._DB_PREFIX_."quotation q, "._DB_PREFIX_.QuotationAssociation::TABLE_NAME." qa WHERE qa.id_quotation = q.id_quotation AND qa.id_cart = ".$this->id_cart);
            if($id)
                return new Quotation($id);
        }
        
        return false;
    }


    /**
     * This method allows to generate first invoice of the current order
     */
    public function setInvoice($use_existing_payment = false)
    {
        if (!$this->hasInvoice()) {
            if ($id = (int)$this->getOrderInvoiceIdIfHasDelivery()) {
                $order_invoice = new OrderInvoice($id);
            } else {
                $order_invoice = new OrderInvoice();
            }
            $order_invoice->id_order = $this->id;
            if (!$id) {
                $order_invoice->number = 0;
            }

            // Save Order invoice

            $this->setInvoiceDetails($order_invoice);

            if (Configuration::get('PS_INVOICE')) {
                $this->setLastInvoiceNumber($order_invoice->id, $this->id_shop);
            }



            // Update order_carrier
            $id_order_carrier = Db::getInstance()->getValue('
                SELECT `id_order_carrier`
                FROM `'._DB_PREFIX_.'order_carrier`
                WHERE `id_order` = '.(int)$order_invoice->id_order.'
                AND (`id_order_invoice` IS NULL OR `id_order_invoice` = 0)');

            if ($id_order_carrier) {
                $order_carrier = new OrderCarrier($id_order_carrier);
                $order_carrier->id_order_invoice = (int)$order_invoice->id;
                $order_carrier->update();
            }

            // Update order detail
            Db::getInstance()->execute('
                UPDATE `'._DB_PREFIX_.'order_detail`
                SET `id_order_invoice` = '.(int)$order_invoice->id.'
                WHERE `id_order` = '.(int)$order_invoice->id_order);

            // Update order payment
            if ($use_existing_payment) {
                $id_order_payments = Db::getInstance()->executeS('
                    SELECT DISTINCT op.id_order_payment
                    FROM `'._DB_PREFIX_.'order_payment` op
                    INNER JOIN `'._DB_PREFIX_.'orders` o ON (o.reference = op.order_reference)
                    LEFT JOIN `'._DB_PREFIX_.'order_invoice_payment` oip ON (oip.id_order_payment = op.id_order_payment)
                    WHERE (oip.id_order != '.(int)$order_invoice->id_order.' OR oip.id_order IS NULL) AND o.id_order = '.(int)$order_invoice->id_order);

                if (count($id_order_payments)) {
                    foreach ($id_order_payments as $order_payment) {
                        Db::getInstance()->execute('
                            INSERT INTO `'._DB_PREFIX_.'order_invoice_payment`
                            SET
                                `id_order_invoice` = '.(int)$order_invoice->id.',
                                `id_order_payment` = '.(int)$order_payment['id_order_payment'].',
                                `id_order` = '.(int)$order_invoice->id_order);
                    }
                    // Clear cache
                    Cache::clean('order_invoice_paid_*');
                }
            }

            // Update order cart rule
            Db::getInstance()->execute('
                UPDATE `'._DB_PREFIX_.'order_cart_rule`
                SET `id_order_invoice` = '.(int)$order_invoice->id.'
                WHERE `id_order` = '.(int)$order_invoice->id_order);

            if (!$this->invoice_date)
                $this->invoice_date = $order_invoice->date_add;

            if (!$this->invoice_number && Configuration::get('PS_INVOICE')) {
                $this->invoice_number = $this->getInvoiceNumber($order_invoice->id);
                $invoice_number = Hook::exec('actionSetInvoice', array(
                    get_class($this) => $this,
                    get_class($order_invoice) => $order_invoice,
                    'use_existing_payment' => (bool)$use_existing_payment
                ));

                if (is_numeric($invoice_number)) {
                    $this->invoice_number = (int)$invoice_number;
                } else {
                    $this->invoice_number = $this->getInvoiceNumber($order_invoice->id);
                }
            }

            $this->update();
        }
    }
}