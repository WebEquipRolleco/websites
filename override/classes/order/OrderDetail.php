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

	public function __construct($id_order = null, $id_lang = null, $id_shop = null) {

		self::$definition['fields']['id_supplier'] = array('type' => self::TYPE_INT);
		self::$definition['fields']['day'] = array('type' => self::TYPE_DATE);
		self::$definition['fields']['week'] = array('type' => self::TYPE_STRING);
		self::$definition['fields']['comment'] = array('type' => self::TYPE_HTML);

		parent::__construct($id_order, $id_lang, $id_shop);
	}

}