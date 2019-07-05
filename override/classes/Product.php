<?php 

class Product extends ProductCore {

	/** @var float Rollcash */
	public $rollcash;

	public function __construct($id_product = null, $id_lang = null, $id_shop = null) {

		self::$definition['fields']['rollcash'] = array('type' => self::TYPE_FLOAT);
		parent::__construct($id_product, $id_lang, $id_shop);
	}
	
}