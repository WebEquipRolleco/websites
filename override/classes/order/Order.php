<?php

class Order extends OrderCore {

	/** @var string Internal Reference */
	public $internal_reference;

	/** @var string Delivery Information */
	public $delivery_information;

	/** @var string Supplier Information */
	public $supplier_information;

	/** @var bool No recall */
	public $no_recall = false;

	/** @var bool display with taxes **/
	public $display_with_taxes = false;

	/** variables temporaires **/
	private $payment_deadline = false;

	/**
	* OVERRIDE : ajout de champs
	**/
	public function __construct($id_order = null, $id_lang = null, $id_shop = null) {

		self::$definition['fields']['internal_reference'] = array('type' => self::TYPE_STRING);
		self::$definition['fields']['delivery_information'] = array('type' => self::TYPE_STRING);
		self::$definition['fields']['supplier_information'] = array('type' => self::TYPE_STRING);
		self::$definition['fields']['display_with_taxes'] = array('type' => self::TYPE_BOOL);
		self::$definition['fields']['no_recall'] = array('type' => self::TYPE_BOOL);
		
		parent::__construct($id_order, $id_lang, $id_shop);
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
    	
    	if(!$this->payment_deadline and $this->invoice_date) {

    		$this->payment_deadline = DateTime::createFromFormat('Y-m-d H:i:s', $this->invoice_date);

    		$delay = Configuration::get('PAYMENT_TIME_LIMIT');
    		if($delay) $this->payment_deadline->modify("+$delay days");
    	}

    	return $this->payment_deadline;
    }

}