<?php

class Review extends ObjectModel {

	const TABLE_NAME = 'webequip_reviews';
	const TABLE_PRIMARY = 'id';

	public $id = null;
	public $id_product = null;
	public $id_shop = null;
	public $name = null;
	public $comment = null;
	public $rating = 5;
	public $id_customer = null;
	public $date_add = null;
	public $active = false;

	private $customer;

	public static $definition = array(
        'table' => self::TABLE_NAME,
        'primary' => self::TABLE_PRIMARY,
        'fields' => array(
        	'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 11),
        	'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 1),
            'name' => array('type' => self::TYPE_STRING),
            'comment' => array('type' => self::TYPE_HTML),
            'rating' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 1),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 11),
            'date_add' => array('type' => self::TYPE_DATE),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool')
        )
    );

    public static function createTable() {
    	Db::getInstance()->execute("CREATE TABLE IF NOT EXISTS "._DB_PREFIX_.self::TABLE_NAME." (
    		`id` INT NOT NULL AUTO_INCREMENT, 
    		`id_product` INT(11) NOT NULL, 
    		`id_shop` INT(1) NOT NULL, 
    		`name` VARCHAR(255) NULL, 
    		`comment` TEXT NULL, 
    		`rating` INT(1) NOT NULL, 
    		`id_customer` INT(11) NOT NULL, 
    		`date_add` DATE NOT NULL,
    		`active` TINYINT DEFAULT 0, 
    		PRIMARY KEY (`id`)
    	) ENGINE = InnoDB;");
    }

    public static function removeTable() {
    	return Db::getInstance()->execute("DROP TABLE "._DB_PREFIX_.self::TABLE_NAME);
    }
	
    public static function erazeContent() {
        return Db::getInstance()->execute("DELETE FROM "._DB_PREFIX_.self::TABLE_NAME);
    }

	public function getCustomer() {

		if(!$this->customer and $this->id_customer)
			$this->customer = new Customer($this->id_customer);

		return $this->customer;
	}

	public static function getNbRating($id_product, $id_shop = null) {

		if(!$id_shop)
			$id_shop = Context::getContext()->cart->id_shop;

		return Db::getInstance()->getValue("SELECT COUNT(rating) FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE id_product = $id_product AND id_shop = $id_shop AND active = 1");
	}

	public static function getAvgRating($id_product, $id_shop = null) {

		if(!$id_shop)
			$id_shop = Context::getContext()->cart->id_shop;

		return round(Db::getInstance()->getValue("SELECT AVG(rating) FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE id_product = $id_product AND id_shop = $id_shop AND active = 1"));
	}

	public static function getReviews($id_product, $id_shop = null) {

		if(!$id_shop)
			$id_shop = Context::getContext()->cart->id_shop;

		$data = array();
		$rows = Db::getInstance()->executeS("SELECT id FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE id_product = $id_product AND id_shop = $id_shop AND active = 1");
		foreach($rows as $row) {
			$data[] = new Review($row['id']);
		}

		return $data;
	}

    public static function findAll($options = array()) {

        $sql = "SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE 1";

        if(isset($options['active']))
            $sql .= " AND active = ".$options['active'];

    	$data = array();
    	$rows = Db::getInstance()->executeS($sql);

    	foreach($rows as $row)
    		$data[] = new Review($row['id']);

    	return $data;
    }

    /**
    * Retourne tous les avis sous forme de tableau
    * @return array
    **/
    public static function findList() {
       return Db::getInstance()->executeS("SELECT r.*, CONCAT(c.firstname, ' ', c.lastname) AS customer FROM "._DB_PREFIX_.self::TABLE_NAME." r, ps_customer c WHERE r.id_customer = c.id_customer"); 
    }

    /**
    * Retrouve un avis pour un client et un produit donné ou en crée un
    * @param int $id_customer
    * @param int $id_product
    * @param int|null $id_shop
    * @return Review
    **/
    public static function find($id_customer, $id_product, $id_shop = null) {

    	if(!$id_shop)
    		$id_shop = Context::getContext()->shop->id;

    	$id = Db::getInstance()->getValue("SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE id_customer = $id_customer AND id_product = $id_product");
    	$review = new self($id);

    	$review->id_customer = $id_customer;
    	$review->id_product = $id_product;
    	$review->id_shop = $id_shop;

    	if(!$review->id)
    		$review->date_add = date('Y-m-d H:i:s');

    	return $review;
    }
    
}