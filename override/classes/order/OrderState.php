<?php 

class OrderState extends OrderStateCore {

	/** @var int ID M3 **/
	public $id_m3 = null;

	/** @var string Day **/
	public $term_of_use = false;

	/** @var bool proforma **/
    public $proforma = false;

    /** @var bool rollcash **/
    public $rollcash = false;

	public function __construct($id_order_state = null, $id_lang = null, $id_shop = null) {

		self::$definition['fields']['id_m3'] = array('type' => self::TYPE_STRING);
		self::$definition['fields']['term_of_use'] = array('type' => self::TYPE_BOOL);
		self::$definition['fields']['proforma'] = array('type' => self::TYPE_BOOL);
		self::$definition['fields']['rollcash'] = array('type' => self::TYPE_BOOL);
		
		parent::__construct($id_order_state, $id_lang, $id_shop);
	}
	
	public static function getDefaultStateNames() {
		return array('DEFAULT_STATE_SUCCESS', 'DEFAULT_ID_STATE_OK', 'PS_OS_CHEQUE', 'PS_OS_PAYMENT', 'PS_OS_PREPARATION', 'PS_OS_WS_PAYMENT', 'PS_OS_BANKWIRE', 'PS_OS_BANKWIRE_45J', 'PS_OS_PAYPAL', 'PS_OS_BANKWIRE_ADMIN');
	}
}