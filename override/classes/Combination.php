<?php 

class Combination extends CombinationCore {

	/** @var float Rollcash */
	public $rollcash;

	/** @var float Position */
	public $position = 1;

	public function __construct($id_product_attribute = null, $id_lang = null, $id_shop = null) {

		// C'est pas beau mais ça marche...
		foreach(array('rollcash', 'position') as $name) {
			if($id_shop) {
				if(Tools::getIsset($name."_".$id_product_attribute))
					Db::getInstance()->execute("UPDATE ps_product_attribute_shop SET $name = ".Tools::getValue($name."_".$id_product_attribute)." WHERE id_product_attribute = $id_product_attribute AND id_shop = $id_shop");
			}
			else {
				if(Tools::getIsset($name."_".$id_product_attribute)) {
					Db::getInstance()->execute("UPDATE ps_product_attribute_shop SET $name = ".Tools::getValue($name."_".$id_product_attribute)." WHERE id_product_attribute = $id_product_attribute");
					Db::getInstance()->execute("UPDATE ps_product_attribute SET $name = ".Tools::getValue($name."_".$id_product_attribute)." WHERE id_product_attribute = $id_product_attribute");
				} 
						
			}
		}

		self::$definition['fields']['rollcash'] = array('type' => self::TYPE_FLOAT);
		self::$definition['fields']['position'] = array('type' => self::TYPE_FLOAT);
		parent::__construct($id_product_attribute, $id_lang, $id_shop);
		
	}
	
}