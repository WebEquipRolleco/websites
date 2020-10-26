<?php

class Customer extends CustomerCore {

	const QUOTATION_NEW = 1;
	const QUOTATION_OK = 2;

	/** @var string Reference **/
	public $reference = null;

	/** @var string Chorus **/
	public $chorus = null;

	/** @var string Tva **/
	public $tva = null;

	/** @var int id_type **/
	public $id_account_type = null;

	/** @var int id_state **/
	public $id_customer_state = null;

	/** @var string Comment **/
	public $comment = null;

	/** @var bool Funding **/
	public $funding = true;

	/** @var date Date funding **/
    public $date_funding = null;

	/** @var string Email invoice **/
	public $email_invoice = null;

	/** @var string Email tracking **/
	public $email_tracking = null;

	/** @var float Rollcash **/
	public $rollcash = 0;

	/** @var int Rollcash **/
	public $quotation = 0;

	// Variables temporaires
	private $type = null;
	private $state = null;
	private $shop = null;

	/**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'customer',
        'primary' => 'id_customer',
        'fields' => array(
            'secure_key' => array('type' => self::TYPE_STRING, 'validate' => 'isMd5', 'copy_post' => false),
            'reference' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'lastname' => array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 255),
            'firstname' => array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 255),
            'email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail'),
            'passwd' => array('type' => self::TYPE_STRING, 'validate' => 'isPasswd', 'required' => true, 'size' => 60),
            'last_passwd_gen' => array('type' => self::TYPE_STRING, 'copy_post' => false),
            'id_gender' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'birthday' => array('type' => self::TYPE_DATE, 'validate' => 'isBirthDate'),
            'newsletter' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'newsletter_date_add' => array('type' => self::TYPE_DATE, 'copy_post' => false),
            'ip_registration_newsletter' => array('type' => self::TYPE_STRING, 'copy_post' => false),
            'optin' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'website' => array('type' => self::TYPE_STRING, 'validate' => 'isUrl'),
            'company' => array('type' => self::TYPE_STRING),
            'siret' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'ape' => array('type' => self::TYPE_STRING, 'validate' => 'isApe'),
            'outstanding_allow_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'copy_post' => false),
            'show_public_prices' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'id_risk' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'max_payment_days' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'deleted' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'note' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'size' => 65000, 'copy_post' => false),
            'is_guest' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'id_shop_group' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'id_default_group' => array('type' => self::TYPE_INT, 'copy_post' => false),
            'id_lang' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'reset_password_token' => array('type' => self::TYPE_STRING, 'validate' => 'isSha1', 'size' => 40, 'copy_post' => false),
            'reset_password_validity' => array('type' => self::TYPE_DATE, 'validate' => 'isDateOrNull', 'copy_post' => false),
            'id_account_type' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'id_customer_state' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'comment' => array('type' => self::TYPE_STRING),
			'chorus' => array('type' => self::TYPE_STRING),
			'tva' => array('type' => self::TYPE_STRING),
			'funding' => array('type' => self::TYPE_BOOL),
			'date_funding' => array('type' => self::TYPE_DATE),
			'email_invoice' => array('type' => self::TYPE_STRING),
			'email_tracking' => array('type' => self::TYPE_STRING),
			'rollcash' => array('type' => self::TYPE_FLOAT),
			'quotation' => array('type' => self::TYPE_INT)
        )
    );

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
	* Retourne la boutique associée au client
	**/
	public function getShop() {

		if(!$this->shop)
			$this->shop = new Shop($this->id_shop);

		return $this->shop;
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
		return $this->quotation == self::QUOTATION_NEW;
	}

	/**
	* Recherche un client
	* UTILISATION : devis
	* @param string $term
	* @param int|null $id_shop
	* @return array
	**/
	public static function search($term, $id_shop = null) {

		if(!$term)
			return array();
		
		$term = "'%".pSql(strtolower($term))."%'";

		$sql = "SELECT id_customer AS id, firstname, lastname, email, company FROM ps_customer WHERE (LOWER(firstname) LIKE $term collate utf8_bin OR LOWER(lastname) LIKE $term collate utf8_bin OR LOWER(email) LIKE $term OR LOWER(company) LIKE $term collate utf8_bin)";
		if($id_shop) $sql .= " AND id_shop = $id_shop";
		
		return Db::getInstance()->executeS($sql);	
	}

	public function getPhone() {
	    $sql = "SELECT phone FROM ps_address WHERE phone is not null AND id_customer=".$this->id;
        $phone = db::getInstance()->getValue($sql);
        if ($phone)
            return $phone;
        return false;
    }

    public function getLastOrder() {
        $sql = "SELECT MAX(invoice_date), id_order FROM ps_orders WHERE id_customer=".$this->id;
        $date = db::getInstance()->executeS($sql);
        return new Order($date[0]['id_order']);
    }
    public function getPreLastOrder()
    {
        $sql = "SELECT invoice_date, id_order FROM ps_orders WHERE id_customer=" . $this->id . " ORDER BY invoice_date DESC";
        $orders = db::getInstance()->executeS($sql);

        if (sizeof($orders) >= 2) {
            return new Order($orders[1]['invoice_date']);
        }
	    return false;
    }

    public function getDateCustomer(){
        return DateTime::createFromFormat('d/m/Y', $this->date_add);
    }

    public function getCustomerType() {
	    if (!$this->id_account_type)
	        return '';
	    $sql = "SELECT name FROM ps_account_type WHERE id_account_type =".$this->id_account_type;
	    $customerType = db::getInstance()->getValue($sql);
	    return $customerType;
    }

}