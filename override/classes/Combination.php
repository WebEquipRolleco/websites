<?php 

class Combination extends CombinationCore {

	/** @var float Delivery fees */
	public $delivery_fees;

	/** @var float Rollcash */
	public $rollcash;

	/** @var float Position */
	public $position = 1;

	public function __construct($id_product_attribute = null, $id_lang = null, $id_shop = null) {

		// C'est pas beau mais ça marche...
		foreach(array('rollcash', 'position', 'delivery_fees') as $name) {
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

		self::$definition['fields']['delivery_fees'] = array('type' => self::TYPE_FLOAT, 'shop'=>true);
		self::$definition['fields']['rollcash'] = array('type' => self::TYPE_FLOAT, 'shop'=>true);
		self::$definition['fields']['position'] = array('type' => self::TYPE_FLOAT);
		parent::__construct($id_product_attribute, $id_lang, $id_shop);
		
	}

	/**
	* Retourne la liste des déclinaisons d'un produit
	**/
	public function getCombinations($id_product) {

		$data = array();
		foreach(Db::getInstance()->executeS("SELECT id_product_attribute FROM ps_product_attribute WHERE id_product = ".$id_product) as $row)
			$data[] = new Combination($row['id_product_attribute'], 1);

		return $data;
	}
	
}