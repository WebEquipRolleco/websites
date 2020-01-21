<?php

class Category extends CategoryCore {

	/** @var string Bottom Description */
	public $bottom_description;

	public function __construct($id_category = null, $id_lang = null, $id_shop = null) {

		self::$definition['fields']['bottom_description'] = array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml');
		parent::__construct($id_category, $id_lang, $id_shop);
	}

	/**
	* Calcule le nombre de produits actifs
	* @return int
	**/
	public function getNbActiveProducts() {
		return (int)Db::getInstance()->getValue("SELECT COUNT(DISTINCT(p.id_product)) FROM ps_product p, ps_category_product cp WHERE cp.id_product = p.id_product AND p.active = 1 AND cp.id_category = ".$this->id);
	}
	
}