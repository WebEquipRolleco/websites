<?php 

class Combination extends CombinationCore {

	/** @var float Rollcash */
	public $rollcash;

	public function __construct($id_product_attribute = null, $id_lang = null, $id_shop = null) {

		self::$definition['fields']['rollcash'] = array('type' => self::TYPE_FLOAT);
		parent::__construct($id_product_attribute, $id_lang, $id_shop);

		// C'est pas beau mais ça marche...
		if(Tools::getIsset("rollcash_".$this->id))
			$this->rollcash = Tools::getValue("rollcash_".$this->id);
	}
	
}