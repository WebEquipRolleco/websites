<?php

class SpecificPrice extends SpecificPriceCore {

	/** @var float Full price */
	public $full_price;

	/** @var float Buying price */
	public $buying_price;

	/** @var float Delivery fees */
	public $delivery_fees;

	/** @var string Comment 1 */
	public $comment_1;

	/** @var string Comment 2 */
	public $comment_2;

	/** Variables temporaires **/
	private $product;
	private $combination;

	/**
	* OVERRIDE : ajout des champs de gestion Web-équip
	**/
	public function __construct($id_specific_price = null, $id_lang = null, $id_shop = null) {

		self::$definition['fields']['full_price'] = array('type' => self::TYPE_FLOAT);
		self::$definition['fields']['buying_price'] = array('type' => self::TYPE_FLOAT);
		self::$definition['fields']['delivery_fees'] = array('type' => self::TYPE_FLOAT);
		self::$definition['fields']['comment_1'] = array('type' => self::TYPE_STRING);
		self::$definition['fields']['comment_2'] = array('type' => self::TYPE_STRING);

		parent::__construct($id_specific_price, $id_lang, $id_shop);
	}

	/**
	* Retourne le produit associé
	**/
	public function getProduct() {

		if($this->id_product and !$this->product)
			$this->product = new Product($this->id_product, true, 1, $this->id_shop);

		return $this->product;
	}

	/**
	* Retourne la déclinaison associée
	**/
	public function getCombination() {

		if($this->id_product_attribute and !$this->combination)
			$this->combination = new Combination($this->id_product_attribute, 1, $this->id_shop);

		return $this->combination;
	}

	/**
	* Retourne la déclinaison ou le produit
	**/
	public function getTarget() {

		if($this->getCombination())
			return $this->getCombination();

		return $this->getProduct();
	}
	
	/**
	* OVERRIDE : forcer l'ordre en fonction du nombre de produits
	**/
	public static function getByProductId($id_product, $id_product_attribute = false, $id_cart = false)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'specific_price`
			WHERE `id_product` = '.(int)$id_product.
            ($id_product_attribute ? ' AND id_product_attribute = '.(int)$id_product_attribute : '').'
			AND id_cart = '.(int)$id_cart.
        	' ORDER BY from_quantity');
    }

    /**
    * Retourne le prix spécifique par défaut d'une ligne produit (quantité = 1)
    * @return array
    **/
    public static function getDefaultPrices($id_product, $id_product_attribute = false) {
    	return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM `'._DB_PREFIX_.'specific_price` WHERE `id_product` = '.(int)$id_product.($id_product_attribute ? ' AND id_product_attribute = '.(int)$id_product_attribute : ''));
    }

    /**
    * Retourne le prix spécifique minimum d'un produit ou d'une déclinaison
	* @param int $id_product
	* @param int $id_combination
	* @param bool $use_taxes
	* @return float
    **/
    public static function getMinimumPrice($id_product, $id_combination = null, $use_taxes = false, $full_price = false) {

    	if($full_price) $column = "full_price";
    	else $column = "price";

    	$sql = "SELECT MIN($column) FROM ps_specific_price WHERE id_product = $id_product";
    	if($id_combination) $sql .= " AND id_product_attribute = $id_combination";

    	$price = Db::getInstance()->getValue($sql);
    	if($use_taxes) $price *= 1.2;

    	return $price;
    }

    /**
    * Retourne le prix spécifique maximum d'un produit ou d'une déclinaison
	* @param int $id_product
	* @param int $id_combination
	* @param bool $use_taxes
	* @param bool $full_price
	* @return float
    **/
    public static function getMaximumPrice($id_product, $id_combination = null, $use_taxes = false, $full_price = false) {

    	if($full_price) $column = "full_price";
    	else $column = "price";

    	$sql = "SELECT MAX($column) FROM ps_specific_price WHERE id_product = $id_product";
    	if($id_combination) $sql .= " AND id_product_attribute = $id_combination";

    	$price = Db::getInstance()->getValue($sql);
    	if($use_taxes) $price *= 1.2;

    	return $price;
    }

}