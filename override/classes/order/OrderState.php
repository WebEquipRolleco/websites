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
	
}