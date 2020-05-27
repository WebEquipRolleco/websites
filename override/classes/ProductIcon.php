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

	/** @var int Position **/
	public $position = 1;

    /** @var int Position **/
    public $location = 1;

	/** @var bool Active **/
	public $active = true;

    /** @var int id_group **/
    public $id_group;

    /** Variables temporaires **/
    private $group;

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
            'position'             => array('type' => self::TYPE_INT),
			'location'		       => array('type' => self::TYPE_INT),
            'id_group'             => array('type' => self::TYPE_INT),
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
    * Retourne le groupe associé
    **/
    public function getGroup() {

        if($this->id_group and !$this->group)
            $this->group = new ProductIconGroup($this->id_group);

        return $this->group;
    }

    /**
    * Vérifie si l'icône doit être affichée sur la page d'un produit
    * @param int $id_product
    * @return bool
    **/
    public function display($id_product) {

        if(!$this->active) return false;
        return $this->hasProduct($id_product);
    }

    /**
    * Ajoute un produit 
    * @param int $id_product
    **/
    public function addProduct($id_product) {
        Db::getInstance()->execute("INSERT INTO ps_product_icon_association VALUES(NULL, $id_product, {$this->id});");
    }

    /**
    * Supprimer un produit
    * @param int $id_product
    **/
    public function removeProduct($id_product) {
        Db::getInstance()->execute("DELETE FROM ps_product_icon_association WHERE id_product = $id_product AND id_product_icon = {$this->id};");
    }

    /**
    * Vérifie si un produit est affécté à l'icone
    * @param int $id_product
    * @return bool
    **/
    public function hasProduct($id_product) {
        return (bool)Db::getInstance()->getValue("SELECT id_product_icon_association FROM ps_product_icon_association WHERE id_product = $id_product AND id_product_icon = {$this->id};");
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
    * Retourne la liste des emplacements possibles
    **/
    public static function getLocations() {

        $data[1] = "Colonne du milieu";
        $data[2] = "Prix produit";

        return $data;
    }
    
}