<?php

class OrderDetail extends OrderDetailCore {

    /** @var int Id quotation line **/
    public $id_quotation_line;

	/** @var int Supplier **/
	public $id_product_supplier;

	/** @var string Day **/
	public $day;

	/** @var string Week **/
	public $week;

	/** @var string comment **/
	public $comment;

    /** @var string Comment Product 1 **/
    public $comment_product_1;

    /** @var string Comment Product 2 **/
    public $comment_product_2;

    /** @var bool Notification Sent **/
    public $notification_sent = false;

    /** @var bool Prevent Notification **/
    public $prevent_notification = false;

	/** variables temporaires **/
	private $supplier;
    private $quotation_line;

	public function __construct($id_order = null, $id_lang = null, $id_shop = null) {

        self::$definition['fields']['id_quotation_line'] = array('type' => self::TYPE_INT);
		self::$definition['fields']['id_product_supplier'] = array('type' => self::TYPE_INT);
		self::$definition['fields']['day'] = array('type' => self::TYPE_DATE);
		self::$definition['fields']['week'] = array('type' => self::TYPE_STRING);
        self::$definition['fields']['comment'] = array('type' => self::TYPE_STRING);
        self::$definition['fields']['comment_product_1'] = array('type' => self::TYPE_STRING);
        self::$definition['fields']['comment_product_2'] = array('type' => self::TYPE_STRING);
        self::$definition['fields']['notification_sent'] = array('type' => self::TYPE_BOOL);
		self::$definition['fields']['prevent_notification'] = array('type' => self::TYPE_BOOL);

		parent::__construct($id_order, $id_lang, $id_shop);
	}

	/**
    * Create a list of order detail for a specified id_order using cart
    * Override : ajout des options de commandes et des lignes devis
    *
    * @param object $order
    * @param object $cart
    * @param int $id_order_status
    * @param int $id_order_invoice
    * @param bool $use_taxes set to false if you don't want to use taxes
    **/
    public function createList(Order $order, Cart $cart, $id_order_state, $product_list, $id_order_invoice = 0, $use_taxes = true, $id_warehouse = 0) {

        $this->vat_address = new Address((int)$order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
        $this->customer = new Customer((int)$order->id_customer);

        $this->id_order = $order->id;
        $this->outOfStock = false;

        foreach ($product_list as $product)
            $this->create($order, $cart, $product, $id_order_state, $id_order_invoice, $use_taxes, $id_warehouse);

        foreach(QuotationAssociation::find($cart->id) as $quotation)
            foreach($quotation->getProducts() as $line)
                $this->createQuotationLine($order, $cart, $line, $id_order_invoice, $id_warehouse);

        foreach(OrderOptionCart::findByCart($cart->id) as $option)
        	$this->createOption($order, $cart, $option, $id_order_invoice, $id_warehouse);

        unset($this->vat_address);
        unset($products);
        unset($this->customer);
    }

    /**
    * Create an order detail liable to an id_order
    * OVERRIDE : Ajout ID supplier, commentaire produit 1 & 2
    * @param object $order
    * @param object $cart
    * @param array $product
    * @param int $id_order_status
    * @param int $id_order_invoice
    * @param bool $use_taxes set to false if you don't want to use taxes
    **/
    protected function create(Order $order, Cart $cart, $product, $id_order_state, $id_order_invoice, $use_taxes = true, $id_warehouse = 0) {

        if($use_taxes)
            $this->tax_calculator = new TaxCalculator();

        $this->id = null;

        $this->product_id = (int)$product['id_product'];
        $this->product_attribute_id = $product['id_product_attribute'] ? (int)$product['id_product_attribute'] : 0;
        $this->id_customization = $product['id_customization'] ? (int)$product['id_customization'] : 0;
        $this->product_name = $product['name'].((isset($product['attributes']) && $product['attributes'] != null) ? ' - '.$product['attributes'] : '');
        $this->product_quantity = (int)$product['cart_quantity'];
        $this->product_ean13 = empty($product['ean13']) ? null : pSQL($product['ean13']);
        $this->product_isbn = empty($product['isbn']) ? null : pSQL($product['isbn']);
        $this->product_upc = empty($product['upc']) ? null : pSQL($product['upc']);
        $this->product_reference = empty($product['reference']) ? null : pSQL($product['reference']);
        $this->product_supplier_reference = empty($product['supplier_reference']) ? null : pSQL($product['supplier_reference']);
        $this->product_weight = $product['id_product_attribute'] ? (float)$product['weight_attribute'] : (float)$product['weight'];
        $this->id_product_supplier = $product['id_supplier'] ?? null;
        $this->id_warehouse = $id_warehouse;

        $this->comment_product_1 = $product['comment_1'];
        $this->comment_product_2 = $product['comment_2'];

        $product_quantity = (int)Product::getQuantity($this->product_id, $this->product_attribute_id, null, $cart);
        $this->product_quantity_in_stock = ($product_quantity - (int)$product['cart_quantity'] < 0) ? $product_quantity : (int)$product['cart_quantity'];

        $this->setVirtualProductInformation($product);
        $this->checkProductStock($product, $id_order_state);

        if($use_taxes) $this->setProductTax($order, $product);
        $this->setShippingCost($order, $product);
        $this->setDetailProductPrice($order, $cart, $product);

        // Set order invoice id
        $this->id_order_invoice = (int)$id_order_invoice;

        // Set shop id
        $this->id_shop = (int)$product['id_shop'];

        // Add new entry to the table
        $this->save();

        if ($use_taxes)
            $this->saveTaxCalculator($order);
    
        unset($this->tax_calculator);
    }

    /**
    * Créer une ligne commande à partir d'une ligne devis
    **/
    private function createQuotationLine($order, $cart, $line, $id_order_invoice = 0, $id_warehouse = 0) {

        $price_ht = $line->selling_price;
        $price_ttc = $price_ht * 1.2;

        $details = new OrderDetail();
        $details->id_order = $order->id;
        $details->id_order_invoice = $id_order_invoice;
        $details->id_warehouse = $id_warehouse;
        $details->id_shop = $order->id_shop;
        $details->product_reference = $line->reference;
        $details->product_name = $line->name;
        $details->product_quantity = $line->quantity;
        $details->id_quotation_line = $line->id;
        $details->id_product_supplier = $line->id_supplier;
        
        $details->product_price = $price_ttc;
        $details->unit_price_tax_incl = $price_ttc;
        $details->unit_price_tax_excl = $price_ht;
        $details->total_price_tax_incl = $details->unit_price_tax_incl * $details->product_quantity;
        $details->total_price_tax_excl = $details->unit_price_tax_excl * $details->product_quantity;
        $details->original_product_price = $price_ttc;

        $details->save();

        $line->getQuotation()->state = Quotation::STATUS_VALIDATED;
        $line->getQuotation()->save();
    }

    /**
    * Créer une ligne commande a partir d'une option de commande
    **/
    private function createOption($order, $cart, $option, $id_order_invoice = 0, $id_warehouse = 0) {

    	$price_ht = $option->getPrice($cart);
        $price_ttc = $price_ht * 1.2;

        $details = new OrderDetail();
        $details->id_order = $order->id;
        $details->id_order_invoice = $id_order_invoice;
        $details->id_warehouse = $id_warehouse;
        $details->id_shop = $order->id_shop;
        $details->product_reference = $option->reference;
        $details->product_name = $option->name;
        $details->product_quantity = 1;

		$details->product_price = $price_ttc;
        $details->unit_price_tax_incl = $price_ttc;
        $details->unit_price_tax_excl = $price_ht;
        $details->total_price_tax_incl = $price_ttc;
        $details->total_price_tax_excl = $price_ht;
        $details->original_product_price = $price_ttc;

        $details->save();
    }

	/**
	* Retourne le fournisseur
	**/
	public function getSupplier() {

		if(!$this->supplier and $this->id_product_supplier)
			$this->supplier = new Supplier($this->id_product_supplier);

		return $this->supplier;
	}

    /**
    * Retourne la ligne de devis concernée
    **/
    public function getQuotationLine() {

        if(!$this->quotation_line and $this->id_quotation_line)
            $this->quotation_line = new QuotationLine($this->id_quotation_line);

        return $this->quotation_line;
    }

	/**
	* Retourne le prix d'achat total (achat + frais de ports)
	**/
	public function getTotalBuyingPrice() {
		return $this->purchase_supplier_price * $this->product_quantity + $this->total_shipping_price_tax_excl;
	}

}