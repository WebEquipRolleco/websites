<?php 

class Combination extends CombinationCore {

	/** @var float Delivery fees */
	public $delivery_fees;

	/** @var float Rollcash */
	public $rollcash;

	/** @var string Commentaire 1 **/
    public $comment_1;

    /** @var string Commentaire 2 **/
    public $comment_2;

	/** @var int Batch **/
    public $batch;

	/** @var float Position */
	public $position = 1;

	// Variables temporaires
	private $product;

	public function __construct($id_product_attribute = null, $id_lang = null, $id_shop = null) {

		// C'est pas beau mais ça marche...
		foreach(array('rollcash', 'position', 'delivery_fees', 'comment-1', 'comment-2') as $name) {
			if($id_shop) {
				if(Tools::getIsset($name."_".$id_product_attribute))
					Db::getInstance()->execute("UPDATE ps_product_attribute_shop SET ".str_replace('-', "_", $name)." = '".Tools::getValue($name."_".$id_product_attribute)."' WHERE id_product_attribute = $id_product_attribute AND id_shop = $id_shop");
			}
			else {
				if(Tools::getIsset($name."_".$id_product_attribute)) {
					Db::getInstance()->execute("UPDATE ps_product_attribute_shop SET ".str_replace('-', "_", $name)." = '".Tools::getValue($name."_".$id_product_attribute)."' WHERE id_product_attribute = $id_product_attribute");
					Db::getInstance()->execute("UPDATE ps_product_attribute SET ".str_replace('-', "_", $name)." = '".Tools::getValue($name."_".$id_product_attribute)."' WHERE id_product_attribute = $id_product_attribute");
				} 
						
			}
		}

		self::$definition['fields']['delivery_fees'] = array('type' => self::TYPE_FLOAT, 'shop'=>true);
		self::$definition['fields']['rollcash'] = array('type' => self::TYPE_FLOAT, 'shop'=>true);
		self::$definition['fields']['comment_1'] = array('type' => self::TYPE_STRING, 'shop'=>true);
		self::$definition['fields']['comment_2'] = array('type' => self::TYPE_STRING, 'shop'=>true);
		self::$definition['fields']['batch'] = array('type' => self::TYPE_INT, 'shop'=>true);
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

	/**
	* Retourne le produit associé
	* @param int $id_shop
	* @return Product
	**/
	public function getProduct($id_shop = null) {

		if($this->id_product and !$this->product)
			$this->product = new Product($this->id_product, true, 1, $id_shop);

		return $this->product;
	}
	
}