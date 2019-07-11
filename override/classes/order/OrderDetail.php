<?php

class OrderDetail extends OrderDetailCore {

	/** @var int Supplier **/
	public $id_supplier;

	/** @var string Day **/
	public $day;

	/** @var string Week **/
	public $week;

	/** @var string Week **/
	public $comment;

	/** variables temporaires **/
	private $supplier;

	public function __construct($id_order = null, $id_lang = null, $id_shop = null) {

		self::$definition['fields']['id_supplier'] = array('type' => self::TYPE_INT);
		self::$definition['fields']['day'] = array('type' => self::TYPE_DATE);
		self::$definition['fields']['week'] = array('type' => self::TYPE_STRING);
		self::$definition['fields']['comment'] = array('type' => self::TYPE_HTML);

		parent::__construct($id_order, $id_lang, $id_shop);
	}

	/**
    * Create a list of order detail for a specified id_order using cart
    * Override : ajout des options de commandes
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

        foreach ($product_list as $product) {
            $this->create($order, $cart, $product, $id_order_state, $id_order_invoice, $use_taxes, $id_warehouse);
        }

        foreach(OrderOptionCart::findByCart($cart->id) as $option) {

        	$price_ht = $option->getPrice($cart);
        	$price_ttc = $price_ht * 1.2;

        	$details = new OrderDetail();
        	$details->id_order = $order->id;
        	$details->id_order_invoice = $id_order_invoice;
        	$details->id_warehouse = $id_warehouse;
        	$details->id_shop = $order->id_shop;
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

        unset($this->vat_address);
        unset($products);
        unset($this->customer);
    }

	/**
	* Retourne le fournisseur
	**/
	public function getSupplier() {

		if(!$this->supplier)
			$this->supplier = new Supplier($this->id_supplier);

		return $this->supplier;
	}

	/**
	* Retourne le prix d'achat total (achat + frais de ports)
	**/
	public function getTotalBuyingPrice() {
		return $this->detail->purchase_supplier_price * $this->product_quantity + $this->total_shipping_price_tax_excl;
	}

}