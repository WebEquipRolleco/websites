<?php

class OrderDetail extends OrderDetailCore {

    /** @var float Delivery fees **/
    public $delivery_fees;

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

    /**
    * @see ObjectModel::$definition
    **/
    public static $definition = array(
        'table' => 'order_detail',
        'primary' => 'id_order_detail',
        'fields' => array(
            'id_order' =>                       array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order_invoice' =>               array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_warehouse' =>                   array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_shop' =>                        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'product_id' =>                     array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'product_attribute_id' =>           array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_customization' =>               array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'product_name' =>                   array('type' => self::TYPE_STRING),
            'product_quantity' =>               array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'product_quantity_in_stock' =>      array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'product_quantity_return' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'product_quantity_refunded' =>      array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'product_quantity_reinjected' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'product_price' =>                  array('type' => self::TYPE_FLOAT),
            'reduction_percent' =>              array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'reduction_amount' =>               array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'reduction_amount_tax_incl' =>      array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'reduction_amount_tax_excl' =>      array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'group_reduction' =>                array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'product_quantity_discount' =>      array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'product_ean13' =>                  array('type' => self::TYPE_STRING, 'validate' => 'isEan13'),
            'product_isbn' =>                   array('type' => self::TYPE_STRING, 'validate' => 'isIsbn'),
            'product_upc' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isUpc'),
            'product_reference' =>              array('type' => self::TYPE_STRING, 'validate' => 'isReference'),
            'product_supplier_reference' =>     array('type' => self::TYPE_STRING, 'validate' => 'isReference'),
            'product_weight' =>                 array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'tax_name' =>                       array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'tax_rate' =>                       array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'tax_computation_method' =>         array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_tax_rules_group' =>             array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'ecotax' =>                         array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'ecotax_tax_rate' =>                array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'discount_quantity_applied' =>      array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'download_hash' =>                  array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'download_nb' =>                    array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'download_deadline' =>              array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'unit_price_tax_incl' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'unit_price_tax_excl' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_price_tax_incl' =>           array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_price_tax_excl' =>           array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_shipping_price_tax_excl' =>  array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_shipping_price_tax_incl' =>  array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'purchase_supplier_price' =>        array('type' => self::TYPE_FLOAT),
            'original_product_price' =>         array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'original_wholesale_price' =>       array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'delivery_fees' =>                  array('type' => self::TYPE_FLOAT),
            'id_quotation_line' =>              array('type' => self::TYPE_INT),
            'id_product_supplier' =>            array('type' => self::TYPE_INT),
            'day' =>                            array('type' => self::TYPE_DATE),
            'week' =>                           array('type' => self::TYPE_STRING),
            'comment' =>                        array('type' => self::TYPE_STRING),
            'comment_product_1' =>              array('type' => self::TYPE_STRING),
            'comment_product_2' =>              array('type' => self::TYPE_STRING),
            'notification_sent' =>              array('type' => self::TYPE_BOOL),
            'prevent_notification' =>           array('type' => self::TYPE_BOOL)
        )
    );

    /**
    * Apply tax to the product
    * Override : Modification ecotaxe
    *
    * @param object $order
    * @param array $product
    **/
    protected function setProductTax(Order $order, $product) {

        $this->ecotax = Tools::convertPrice(floatval($product['custom_ecotax']), intval($order->id_currency));

        // Exclude VAT
        if(!Tax::excludeTaxeOption()) {
            
            $this->setContext((int)$product['id_shop']);
            $this->id_tax_rules_group = (int)Product::getIdTaxRulesGroupByIdProduct((int)$product['id_product'], $this->context);

            $tax_manager = TaxManagerFactory::getManager($this->vat_address, $this->id_tax_rules_group);
            $this->tax_calculator = $tax_manager->getTaxCalculator();
            $this->tax_computation_method = (int)$this->tax_calculator->computation_method;
        }

        $this->ecotax_tax_rate = 0;
        if(!empty($product['ecotax']))
            $this->ecotax_tax_rate = Tax::getProductEcotaxRate($order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
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

        foreach(QuotationAssociation::find($cart->id) as $quotation) {
            foreach($quotation->getProducts() as $line)
                $this->createQuotationLine($order, $cart, $line, $id_order_invoice, $id_warehouse);

            $quotation->state = Quotation::STATUS_VALIDATED;
            $quotation->save();
        }

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

        $this->purchase_supplier_price = isset($product['specific_prices']) ? $product['specific_prices']['buying_price'] : 0;
        $this->delivery_fees = isset($product['specific_prices']) ? $product['specific_prices']['delivery_fees'] : 0;
        $this->ecotax = $product['ecotax'];

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
    * Set detailed product price to the order detail
    * @param object $order
    * @param object $cart
    * @param array $product
    **/
    protected function setDetailProductPrice(Order $order, Cart $cart, $product) {

        $this->setContext((int)$product['id_shop']);
        Product::getPriceStatic((int)$product['id_product'], true, (int)$product['id_product_attribute'], 6, null, false, true, $product['cart_quantity'], false, (int)$order->id_customer, (int)$order->id_cart, (int)$order->{Configuration::get('PS_TAX_ADDRESS_TYPE')}, $specific_price, true, true, $this->context);
        $this->specificPrice = $specific_price;
        $this->original_product_price = Product::getPriceStatic($product['id_product'], false, (int)$product['id_product_attribute'], 6, null, false, false, 1, false, null, null, null, $null, true, true, $this->context);
        $this->product_price = $this->original_product_price;
        $this->unit_price_tax_incl = (float)$product['price_wt'];
        $this->unit_price_tax_excl = (float)$product['price'];
        $this->total_price_tax_incl = (float)$product['total_wt'];
        $this->total_price_tax_excl = (float)$product['total'];

        // OVERRIDE : ne pas écraser le prix d'achat des prix spécifiques
        if(!$this->purchase_supplier_price) {
            $this->purchase_supplier_price = (float)$product['wholesale_price'];
            if ($product['id_supplier'] > 0 && ($supplier_price = ProductSupplier::getProductPrice((int)$product['id_supplier'], $product['id_product'], $product['id_product_attribute'], true)) > 0) {
                $this->purchase_supplier_price = (float)$supplier_price;
            }
        }
        
        $this->setSpecificPrice($order, $product);

        $this->group_reduction = (float)Group::getReduction((int)$order->id_customer);

        $shop_id = $this->context->shop->id;

        $quantity_discount = SpecificPrice::getQuantityDiscount(
            (int)$product['id_product'],
            $shop_id,
            (int)$cart->id_currency,
            (int)$this->vat_address->id_country,
            (int)$this->customer->id_default_group,
            (int)$product['cart_quantity'],
            false,
            null,
            null,
            $null,
            true,
            true,
            $this->context
        );

        $unit_price = Product::getPriceStatic(
            (int)$product['id_product'],
            true,
            ($product['id_product_attribute'] ? intval($product['id_product_attribute']) : null),
            2,
            null,
            false,
            true,
            1,
            false,
            (int)$order->id_customer,
            null,
            (int)$order->{Configuration::get('PS_TAX_ADDRESS_TYPE')},
            $null,
            true,
            true,
            $this->context
        );
        $this->product_quantity_discount = 0.00;
        if ($quantity_discount) {
            $this->product_quantity_discount = $unit_price;
            if (Product::getTaxCalculationMethod((int)$order->id_customer) == PS_TAX_EXC) {
                $this->product_quantity_discount = Tools::ps_round($unit_price, 2);
            }

            if (isset($this->tax_calculator)) {
                $this->product_quantity_discount -= $this->tax_calculator->addTaxes($quantity_discount['price']);
            }
        }

        $this->discount_quantity_applied = (($this->specificPrice && $this->specificPrice['from_quantity'] > 1) ? 1 : 0);
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

    /**
    * Compte le nombre de références uniques vendues
    * @param $string|DateTime $date_begin
    * @param $string|DateTime $date_end
    * @return int
    **/
    public static function countReferences($date_begin, $date_end) {

        $options['date_begin'] = $date_begin;
        $options['date_end'] = $date_end;
        $ids = Order::findIds($options);

        if(!$ids)
            return 0;

        return Db::getInstance()->getValue("SELECT COUNT(DISTINCT(od.product_reference)) FROM ps_order_detail od WHERE od.id_order IN (".implode(',', $ids).")");
    }

    /**
    * Calcul un chiffre d'affaire sur une période de temps
    * @param mixed $date_begin
    * @param mixed $date_end
    * @param bool $use_taxes
    * @param int $type
    * @return float
    **/
    public static function sumTurnover($date_begin, $date_end, $use_taxes = false, $type = Order::ALL_PRODUCTS) {

        $options['date_begin'] = $date_begin;
        $options['date_end'] = $date_end;
        $ids = Order::findIds($options);

        if(!$ids)
            return 0;

        if($use_taxes) $field = "od.total_price_tax_incl";
        else $field = "od.total_price_tax_excl";

        $sql = "SELECT SUM($field) FROM ps_order_detail od WHERE od.id_order IN (".implode(',', $ids).")";
        if($type == ORDER::ONLY_PRODUCTS) $sql .= " AND (od.id_quotation_line IS NULL OR od.id_quotation_line = 0)";
        if($type == ORDER::ONLY_QUOTATIONS) $sql .= " AND (od.id_quotation_line IS NOT NULL AND od.id_quotation_line <> 0)";

        return (float)Db::getInstance()->getValue($sql);
    }

    /**
    * Calcul un chiffre d'affaire sur une période de temps
    * @param mixed $date_begin
    * @param mixed $date_end
    * @param string $reference
    * @param bool $use_taxes
    * @return float
    **/
    public static function sumProductTurnover($date_begin, $date_end, $reference, $use_taxes = false) {

        $options['date_begin'] = $date_begin;
        $options['date_end'] = $date_end;
        $ids = Order::findIds($options);

        if(!$ids)
            return 0;

        if($use_taxes) $field = "od.total_price_tax_incl";
        else $field = "od.total_price_tax_excl";

        $sql = "SELECT SUM($field) FROM ps_order_detail od WHERE od.id_order IN (".implode(',', $ids).") AND od.product_reference = '$reference'";

        return (float)Db::getInstance()->getValue($sql);

    }
}