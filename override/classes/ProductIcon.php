<?php

class ProductIcon extends ObjectModel {

	const TABLE_NAME = 'product_icon';
	const TABLE_PRIMARY = 'id_product_icon';

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

	/** @var string Product White list **/
	public $product_white_list;

	/** @var string Product Black list **/
	public $product_black_list;

    /** @var string Category White list **/
    public $category_white_list;

    /** @var string Category Black list **/
    public $category_black_list;

    /** @var string Supplier White list **/
    public $supplier_white_list;

    /** @var string Supplier Black list **/
    public $supplier_black_list;

	/** @var int Position **/
	public $position = 1;

    /** @var int Position **/
    public $location = 1;

	/** @var bool Active **/
	public $active = true;

	// Variables temporaires
	private $shops = array();

	/**
	* @see ObjectModel::$definition
	**/
	public static $definition = array(
		'table' => self::TABLE_NAME,
		'primary' => self::TABLE_PRIMARY,
		'fields' => array(
			'name' 			       => array('type' => self::TYPE_STRING),
			'title' 		       => array('type' => self::TYPE_STRING),
			'url' 			       => array('type' => self::TYPE_STRING),
			'extension' 	       => array('type' => self::TYPE_STRING),
			'height'		       => array('type' => self::TYPE_INT),
			'width'			       => array('type' => self::TYPE_INT),
			'product_white_list'   => array('type' => self::TYPE_STRING),
			'product_black_list'   => array('type' => self::TYPE_STRING),
            'category_white_list'  => array('type' => self::TYPE_STRING),
            'category_black_list'  => array('type' => self::TYPE_STRING),
            'supplier_white_list'  => array('type' => self::TYPE_STRING),
            'supplier_black_list'  => array('type' => self::TYPE_STRING),
            'position'             => array('type' => self::TYPE_INT),
			'location'		       => array('type' => self::TYPE_INT),
			'active' 		       => array('type' => self::TYPE_BOOL)
		)
	);

	/**
	* Retourne la liste des icones
    * @param int $position
    * @param bool $active
    * @return array
	**/
	public static function getList($position = false, $active = true) {

		$sql = "SELECT ".self::TABLE_PRIMARY." FROM "._DB_PREFIX_.self::TABLE_NAME." WHERE 1";
        if($position) $sql .= " AND location = $position";
		if($active) $sql .= " AND active = 1";
		$sql .= " ORDER BY position ASC";

		$data = array();
		foreach(Db::getInstance()->executeS($sql) as $row)
			$data[] = new self($row[self::TABLE_PRIMARY]);

		return $data;
	}

	/**
    * Retourne la liste blanche des produits
    * @param bool $full
    * @return array
    **/
    public function getWhiteList($full = false) {
        $id_shop = Context::getContext()->shop->id;

        if(!$full)
            return array_filter(explode(self::DELIMITER, $this->product_white_list));
        elseif($this->product_white_list)
            return Db::getInstance()->executeS("SELECT id_product, name FROM ps_product_lang WHERE id_lang = 1 AND id_shop = $id_shop AND id_product IN (".$this->product_white_list.") ORDER BY id_product ASC");
        else
            return array();
    }

    /**
    * Retourne la liste noire des produits
    * @param bool $full
    * @return array
    **/
    public function getBlackList($full = false) {
        $id_shop = Context::getContext()->shop->id;

        if(!$full)
            return array_filter(explode(self::DELIMITER, $this->product_black_list));
        elseif($this->product_black_list)
            return Db::getInstance()->executeS("SELECT id_product, name FROM ps_product_lang WHERE id_lang = 1 AND id_shop = $id_shop AND id_product IN (".$this->product_black_list.") ORDER BY id_product ASC");
        else
            return array();
    }

    /**
    * Retourne la liste blanche des catégories
    * @param bool $full
    * @return array
    **/
    public function getCategoryWhiteList($full = false) {
        $id_shop = Context::getContext()->shop->id;

        if(!$full)
            return array_filter(explode(self::DELIMITER, $this->category_white_list));
        elseif($this->category_white_list)
            return Db::getInstance()->executeS("SELECT id_category, name FROM ps_category_lang WHERE id_lang = 1 AND id_shop = $id_shop AND id_category IN (".$this->category_white_list.") ORDER BY id_category ASC");
        else
            return array();
    }

    /**
    * Retourne la liste noire des catégories
    * @param bool $full
    * @return array
    **/
    public function getCategoryBlackList($full = false) {
        $id_shop = Context::getContext()->shop->id;

        if(!$full)
            return array_filter(explode(self::DELIMITER, $this->category_black_list));
        elseif($this->category_black_list)
            return Db::getInstance()->executeS("SELECT id_category, name FROM ps_category_lang WHERE id_lang = 1 AND id_shop = $id_shop AND id_category IN (".$this->category_black_list.") ORDER BY id_category ASC");
        else
            return array();
    }

    /**
    * Retourne la liste blanche des fournisseurs
    * @param bool $full
    * @return array
    **/
    public function getSupplierWhiteList($full = false) {
        $id_shop = Context::getContext()->shop->id;

        if(!$full)
            return array_filter(explode(self::DELIMITER, $this->supplier_white_list));
        elseif($this->supplier_white_list)
            return Db::getInstance()->executeS("SELECT id_supplier, name FROM ps_supplier WHERE id_supplier IN (".$this->supplier_white_list.") ORDER BY id_supplier ASC");
        else
            return array();
    }

    /**
    * Retourne la liste noire des fournisseurs
    * @param bool $full
    * @return array
    **/
    public function getSupplierBlackList($full = false) {
        $id_shop = Context::getContext()->shop->id;

        if(!$full)
            return array_filter(explode(self::DELIMITER, $this->supplier_black_list));
        elseif($this->supplier_black_list)
            return Db::getInstance()->executeS("SELECT id_supplier, name FROM ps_supplier WHERE id_supplier IN (".$this->supplier_black_list.") ORDER BY id_supplier ASC");
        else
            return array();
    }

    /**
    * Vérifie si l'icône doit être affichée sur la page d'un produit
    **/
    public function display($product) {

        // Vérification de la boutique
    	if(isset($product['id_shop']) and !in_array($product['id_shop'], $this->getShops()))
    		return false;

        // Vérification des listes blanches
        if(isset($product['id_product']) and $this->product_white_list)
            if(in_array($product['id_product'], $this->getWhiteList()))
                return true;

        if(isset($product['id_category_default']) and $this->category_white_list)
            if(in_array($product['id_category_default'], $this->getCategoryWhiteList()))
                return true;

        if(isset($product['id_supplier']) and $this->supplier_white_list)
            if(in_array($product['id_supplier'], $this->getSupplierWhiteList()))
                return true;

        // Vérification des listes noires
        if(isset($product['id_product']) and $this->product_black_list)
        	if(in_array($product['id_product'], $this->getBlackList()))
        		return false;

        if(isset($product['id_category_default']) and $this->category_black_list)
            if(in_array($product['id_category_default'], $this->getCategoryBlackList()))
                return false;

        if(isset($product['id_supplier']) and $this->supplier_black_list)
            if(in_array($product['id_supplier'], $this->getSupplierBlackList()))
                return false;

        // Affichage par défaut
    	return true;
    }

    /**
    * Vérifie si un fichier image est présent
    **/
    public function hasFile() {

        if(!$this->extension)
            return false;

        return is_file(_PS_ROOT_DIR_._PS_IMG_.'icons/'.$this->id.'.'.$this->extension);
    }

    /**
    * Retourne le chemin relatif vers l'image
    **/
    public function getImgPath() {
    	return _PS_IMG_.'icons/'.$this->id.'.'.$this->extension."?rnd=".uniqid();
    }

    /**
    * Retourne la liste des boutiques associées
    **/
    public function getShops() {

    	if(!$this->shops and $this->id) {
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

    /**
    * Retourne la liste des emplacements possibles
    **/
    public static function getLocations() {

        $data[1] = "Colonne du milieu";
        $data[2] = "Prix produit";

        return $data;
    }
    
}