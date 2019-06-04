<?php

class Customer extends CustomerCore {

	/** @var string Reference **/
	public $reference;

	/** @var string Chorus **/
	public $chorus;

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

	// Variables temporaires
	private $type = null;
	private $state = null;

	public function __construct($id_category = null, $id_lang = null, $id_shop = null) {

		self::$definition['fields']['reference'] = array('type' => self::TYPE_STRING, 'validate' => 'isString');
		self::$definition['fields']['id_account_type'] = array('type' => self::TYPE_INT, 'validate' => 'isInt');
		self::$definition['fields']['id_customer_state'] = array('type' => self::TYPE_INT, 'validate' => 'isInt');
		self::$definition['fields']['comment'] = array('type' => self::TYPE_STRING);
		self::$definition['fields']['chorus'] = array('type' => self::TYPE_STRING);
		self::$definition['fields']['funding'] = array('type' => self::TYPE_BOOL);
		self::$definition['fields']['date_funding'] = array('type' => self::TYPE_DATE);
		self::$definition['fields']['email_invoice'] = array('type' => self::TYPE_STRING);
		self::$definition['fields']['email_tracking'] =array('type' => self::TYPE_STRING);

		parent::__construct($id_category, $id_lang, $id_shop);
	}

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

}