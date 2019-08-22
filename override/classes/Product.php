<?php 

class Product extends ProductCore {

	/** @var float Rollcash */
	public $rollcash;

	public function __construct($id_product = null, $id_lang = null, $id_shop = null) {

		self::$definition['fields']['rollcash'] = array('type' => self::TYPE_FLOAT);
		parent::__construct($id_product, $id_lang, $id_shop);
	}
	
	/**
	* Retourne la valeur du Rollcash d'un produit ou d'une de ces déclinaisons
	**/
	public static function findRollcash($id_product = null, $id_combination = null) {

		if($id_combination)
			return Db::getInstance()->getValue("SELECT rollcash FROM ps_product_attribute WHERE id_product_attribute = $id_combination");
		else
			return Db::getInstance()->getValue("SELECT rollcash FROM ps_product WHERE id_product = $id_product");
	}
	
	/**
	* Retourne une liste des produits actifs
	* UTILISATION : devis
	* @param int $id_lang
	* @param Context $context
	* @return array
	**/
	public static function getSimpleActiveProducts($id_lang = 1, Context $context = null) {
        
        if (!$context)
            $context = Context::getContext();

        $front = true;
        if(!in_array($context->controller->controller_type, array('front', 'modulefront')))
            $front = false;

        $sql = 'SELECT p.`id_product`, pl.`name`
				FROM `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.Shop::addSqlRestrictionOnLang('pl').')
				WHERE pl.`id_lang` = '.(int)$id_lang.'
				AND p.active = 1
				'.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').'
				ORDER BY pl.`name`';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

}