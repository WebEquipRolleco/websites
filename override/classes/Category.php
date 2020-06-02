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

		$nb_products = 0;
		$nb_combinations = 0;

		$ids_category[] = $this->id;

		$tree = $this->recurseLiteCategTree();
		if(isset($tree['children']))
			foreach($tree['children'] as $child) 
				$ids_category[] = $child['id'];

		$ids_category = implode(',', $ids_category);

		$ids_products = Db::getInstance()->executeS("SELECT p.id_product FROM ps_product p, ps_category_product cp, ps_category c WHERE cp.id_product = p.id_product AND p.active = 1 AND cp.id_category = c.id_category AND c.active = 1 AND p.visibility <> 'none' AND cp.id_category IN ($ids_category)");
		if($ids_products) {

			$ids_products = array_map(function($e) { return $e['id_product']; }, $ids_products);
			$ids_products = implode(',', $ids_products);

			$nb_products = (int)Db::getInstance()->getValue("SELECT COUNT(id_product) FROM ps_product WHERE id_product IN ($ids_products) AND id_product NOT IN (SELECT id_product FROM ps_product_attribute)");
			$nb_combinations = (int)Db::getInstance()->getValue("SELECT COUNT(id_product_attribute) FROM ps_product_attribute WHERE id_product IN ($ids_products)");
		}

		return $nb_products + $nb_combinations;
	}
	
}