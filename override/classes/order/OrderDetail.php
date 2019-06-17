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