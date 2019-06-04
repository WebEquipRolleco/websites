<?php

class ProductIcon extends ObjectModel {

	const TABLE_NAME = 'product_icon';
	const TABLE_PRIMARY = 'id';

	const DELIMITER = ",";

	/** @var string Name **/
	public $name;

	/** @var string Title **/
	public $title;

	/** @var string Url **/
	public $url;

	/** @var string Extension **/
	public $extension;

	/** @var int height **/
	public $height;

	/** @var int width **/
	public $width;

	/** @var string White list **/
	public $white_list;

	/** @var string Black list **/
	public $black_list;

	/** @var int Position **/
	public $position = 1;

	/** @var bool Active **/
	public $active = true;

	// Variables temporaires
	private $shops;

	/**
	* @see ObjectModel::$definition
	**/
	public static $definition = array(
		'table' => self::TABLE_NAME,
		'primary' => self::TABLE_PRIMARY,
		'fields' => array(
			'name' 			=> array('type' => self::TYPE_STRING),
			'title' 		=> array('type' => self::TYPE_STRING),
			'url' 			=> array('type' => self::TYPE_STRING),
			'extension' 	=> array('type' => self::TYPE_STRING),
			'height'		=> array('type' => self::TYPE_INT),
			'width'			=> array('type' => self::TYPE_INT),
			'white_list'	=> array('type' => self::TYPE_STRING),
			'black_list' 	=> array('type' => self::TYPE_STRING),
			'position'		=> array('type' => self::TYPE_INT),
			'active' 		=> array('type' => self::TYPE_BOOL)
		)
	);

	/**
	* Retourne la liste des icones
	**/
	public static function getList($active = true) {

		$sql = "SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME;
		if($active) $sql .= " WHERE active = 1";
		$sql .= " ORDER BY position ASC";

		$data = array();
		foreach(Db::getInstance()->executeS($sql) as $row)
			$data[] = new self($row[self::TABLE_PRIMARY]);

		return $data;
	}

	/**
    * Retourne la liste blanche des produits
    **/
    public function getWhiteList($full = false) {
        
        if(!$full)
            return array_filter(explode(self::DELIMITER, $this->white_list));
        elseif($this->white_list)
            return Db::getInstance()->executeS("SELECT id_product, name FROM ps_product_lang WHERE id_lang = 1 AND id_product IN (".$this->white_list.") ORDER BY id_product ASC");
        else
            return array();
    }

    /**
    * Retourne la liste noire des produits
    **/
    public function getBlackList($full = false) {
        
        if(!$full)
            return array_filter(explode(self::DELIMITER, $this->black_list));
        elseif($this->black_list)
            return Db::getInstance()->executeS("SELECT id_product, name FROM ps_product_lang WHERE id_lang = 1 AND id_product IN (".$this->black_list.") ORDER BY id_product ASC");
        else
            return array();
    }

    /**
    * Vérifie si l'icône doit être affichée sur la page d'un produit
    **/
    public function display($id_product, $id_shop = null) {

    	if(!in_array($id_shop, $this->getShops()))
    		return false;

    	if($this->black_list)
    		if(in_array($id_product, $this->getBlackList()))
    			return false;

    	if($this->white_list)
    		if(in_array($id_product, $this->getWhiteList()))
    			return true;

    	return true;
    }

    /**
    * Vérifie si un fichier image est présent
    **/
    public function hasFile() {

        if(!$this->extension)
            return false;

        return is_file(__DIR__.'/../../img/icons/'.$this->id.'.'.$this->extension);
    }

    /**
    * Retourne le chemin relatif vers l'image
    **/
    public function getImgPath() {
    	return '/img/icons/'.$this->id.'.'.$this->extension."?rnd=".uniqid();
    }

    /**
    * Retourne la liste des boutiques associées
    **/
    public function getShops() {

    	if(!$this->shops) {
    		$rows = Db::getInstance()->executeS("SELECT id_shop FROM "._DB_PREFIX_.self::TABLE_NAME."_shop WHERE id_product_icon = ".$this->id);
    		$this->shops = array_map(function($e) { return $e['id_shop']; }, $rows);
    	}

    	return $this->shops;
    }

    /**
    * Gestion multiboutique
    **/
    public function hasShop($id_shop, $default = true) {

    	if($default and empty($this->getShops()))
    		return true;

    	return in_array($id_shop, $this->getShops());
    }

    /**
    * Efface la table multiboutique
    **/
    public function eraseShops() {
    	Db::getInstance()->execute("DELETE FROM "._DB_PREFIX_.self::TABLE_NAME."_shop WHERE id_product_icon = ".$this->id);
    }

    /**
    * Ajoute une boutique dans la liste
    **/
    public function addShop($id_shop) {
    	Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_.self::TABLE_NAME."_shop VALUES(".$this->id.", $id_shop)");
    }
}