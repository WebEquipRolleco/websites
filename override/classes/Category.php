<?php

class Category extends CategoryCore {

	/** @var string Bottom Description */
	public $bottom_description;

	/**
    * @see ObjectModel::$definition
    **/
    public static $definition = array(
        'table' => 'category',
        'primary' => 'id_category',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => array(
            'nleft' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'nright' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'level_depth' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'id_parent' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_shop_default' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'is_root_category' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position' => array('type' => self::TYPE_INT),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            /* Lang fields */
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => true, 'size' => 128),
            'link_rewrite' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isLinkRewrite', 'required' => true, 'size' => 128),
            'description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'meta_title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 128),
            'meta_description' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'meta_keywords' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'bottom_description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml')
        ),
    );

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