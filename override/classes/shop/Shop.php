<?php

class Shop extends ShopCore {

	/** @var string Préfix de la référence commande */
    public $reference_prefix;

    /** @var int Longueur de la référence */
    public $reference_length;

    /**
    * OVERRIDE : ajout de variables
    * @param int $id
    * @param int $id_lang
    * @param int $id_shop
    **/
    public function __construct($id = null, $id_lang = null, $id_shop = null) {
        
		self::$definition['fields']['reference_prefix'] = array('type'=>self::TYPE_STRING, 'validate' => 'isString');
		self::$definition['fields']['reference_length'] = array('type'=>self::TYPE_INT, 'validate' => 'isUnsignedId');

		parent::__construct($id, $id_lang, $id_shop);
	}
}