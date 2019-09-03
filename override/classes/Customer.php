<?php

class Customer extends CustomerCore {

	const QUOTATION_NEW = 1;
	const QUOTATION_OK = 2;

	/** @var string Reference **/
	public $reference;

	/** @var string Chorus **/
	public $chorus;

	/** @var string Tva **/
	public $tva;

	/** @var int id_type **/
	public $id_account_type;

	/** @var int id_state **/
	public $id_customer_state;

	/** @var string Comment **/
	public $comment;

	/** @var bool Funding **/
	public $funding = true;

	/** @var date Date funding **/
    public $date_funding;

	/** @var string Email invoice **/
	public $email_invoice;

	/** @var string Email tracking **/
	public $email_tracking;

	/** @var flat Rollcash **/
	public $rollcash;

	// Variables temporaires
	private $type = null;
	private $state = null;

	public function __construct($id_category = null, $id_lang = null, $id_shop = null) {

		self::$definition['fields']['reference'] = array('type' => self::TYPE_STRING, 'validate' => 'isString');
		self::$definition['fields']['id_account_type'] = array('type' => self::TYPE_INT, 'validate' => 'isInt');
		self::$definition['fields']['id_customer_state'] = array('type' => self::TYPE_INT, 'validate' => 'isInt');
		self::$definition['fields']['comment'] = array('type' => self::TYPE_STRING);
		self::$definition['fields']['chorus'] = array('type' => self::TYPE_STRING);
		self::$definition['fields']['tva'] = array('type' => self::TYPE_STRING);
		self::$definition['fields']['funding'] = array('type' => self::TYPE_BOOL);
		self::$definition['fields']['date_funding'] = array('type' => self::TYPE_DATE);
		self::$definition['fields']['email_invoice'] = array('type' => self::TYPE_STRING);
		self::$definition['fields']['email_tracking'] =array('type' => self::TYPE_STRING);
		self::$definition['fields']['rollcash'] = array('type' => self::TYPE_FLOAT);
		
		parent::__construct($id_category, $id_lang, $id_shop);
	}

	// SHORTCUTS
	public function getType() { return $this->getAccountType(); }
	
	/**
	* Retourne les e-mails associées à la facturation
	**/
	public function getInvoiceEmails() {

		$data[] = $this->email;
		if($this->email_invoice)
			$data[] = $this->email_invoice;

		return $data;
	}

	/**
	* Retourne les e-mails associées à la livraison
	**/
	public function getTrackingEmails() {

		$data[] = $this->email;
		if($this->email_tracking)
			$data[] = $this->email_tracking;

		return $data;
	}

	/**
	* Retourne le type de compte
	**/
	public function getAccountType() {

		if(!$this->type)
			$this->type = new AccountType($this->id_account_type);

		return $this->type;
	}

	/**
	* Retourne le statut du client
	**/
	public function getState() {

		if(!$this->state and $this->id_customer_state)
			$this->state = new CustomerState($this->id_customer_state);

		return $this->state;
	}

	/**
	* Vérifie la TVA interne 
	**/
	public function checkTVA() {

		if($type = $this->getAccountType() and $this->getAccountType()->tva)
			return (bool)$this->tva;

		return true;
	}
	
	/**
	* Retourne la liste des commandes du client
	* @return array
	**/
	public function getOrders() {

		$data = array();
		foreach(Db::getInstance()->executeS("SELECT id_order FROM ps_orders WHERE id_customer = ".$this->id." ORDER BY id_order DESC") as $row)
			$data[] = new Order($row['id_order']);

		return $data;
	}

	/**
	* Retourne le dernier panier du client
	* @param int $id_customer
	* @return Cart
	**/
	public static function getLastCart($id_customer) {

		$carts = Cart::getCustomerCarts($id_customer, false);
        if (!empty($carts)) {
            
            $cart = array_shift($carts);
        	return new Cart($cart['id_cart']);
        }

        $cart = new Cart();
        $cart->id_customer = $id_customer;
        $cart->save();

        return $cart;
	}

	/**
	* Vérifie si un client a été notifié de la création d'un compte pour un devis
	* @return bool
	**/
	public function isNewFromQuotation() {
		return true;
	}

}