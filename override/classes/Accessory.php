<?php

class Accessory extends ObjectModel {

	const TABLE_NAME = 'webequip_accessory';
	const TABLE_PRIMARY = 'id_accessory';

	/** @var int ID product **/
    public $id_product;

    /** @var int ID product accessorty **/
    public $id_product_accessory;

    /** @var int ID combination accessorty **/
    public $id_combination_accessory;

    // Variables temporaires
    private $product;
    private $combination;

	/**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => self::TABLE_PRIMARY,
        'fields' => array(
        	'id_product' => array('type'=>self::TYPE_INT),
            'id_product_accessory' => array('type'=>self::TYPE_INT),
            'id_combination_accessory' => array('type'=>self::TYPE_INT)
        )
    );

    /**
    * Retourne le produit associé 
    * @return Product
    **/
    public function getProduct() {
        
        if(!$this->product)
            $this->product = new Product($this->id_product_accessory, true, 1);

        return $this->product;
    }

    /**
    * Retourne la déclinaison associée
    * @return Combination
    **/
    public function getCombination() {

        if($this->id_combination_accessory and !$this->combination)
            $this->combination = new Combination($this->id_combination_accessory);

        return $this->combination;
    }

    /**
    * retourne la déclinaison ou le produit concerné
    * @return Combination|Product
    **/
    public function getTarget() {

        if($this->getCombination())
            return $this->getCombination();

        return $this->getProduct();
    }

    /**
    * Cherche si une association existe déjà dans la BDD
    * @param int $id_product
    * @param int $id_product_accessory
    * @param int $id_combination_accessory
    * @return int|false
    **/
    public static function exists($id_product, $id_product_accessory, $id_combination_accessory) {
        return Db::getInstance()->getValue("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE id_product = $id_product AND id_product_accessory = $id_product_accessory AND id_combination_accessory = $id_combination_accessory");
    }

    /**
    * Retourne la liste des accessoires liés à un produit
    * @param int $id_product
    * @param bool $active
    * @return array
    **/
    public static function find($id_product, $active = true) {

        $sql = "SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." a, ps_product p WHERE a.id_product = p.id_product AND a.id_product = $id_product";
        if($active) $sql .= " AND p.active = 1";

        $data = array();
        foreach(Db::getInstance()->executeS($sql) as $row)
            $data[] = new self($row[self::TABLE_PRIMARY]);

        return $data;
    }

}