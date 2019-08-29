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
	* @param bool $quotation
	* @return array
	**/
	public static function getSimpleActiveProducts($id_lang = 1, $quotation = false) {
        
        $context = Context::getContext();

        $front = true;
        if(!in_array($context->controller->controller_type, array('front', 'modulefront')))
            $front = false;

        $sub_sql = "SELECT GROUP_CONCAT(al.name)
					FROM ps_product_attribute_combination pac, ps_attribute a, ps_attribute_group g, ps_attribute_lang al 
					WHERE pac.id_attribute = al.id_attribute
					AND pac.id_attribute = a.id_attribute
					AND a.id_attribute_group = g.id_attribute_group
					AND pac.id_product_attribute = pa.id_product_attribute
					AND al.id_lang = 1".
					($quotation ? " AND g.quotation = 1" : "")."
					GROUP BY pac.id_product_attribute";

        $sql = 'SELECT p.`id_product`, p.`reference`, pl.`name`, pa.`id_product_attribute`, pa.`reference` AS reference_attribute, ('.$sub_sql.') AS name_attribute
				FROM `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (p.`id_product` = pa.`id_product`)
				WHERE pl.`id_lang` = '.(int)$id_lang.'
				AND p.active = 1
				'.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    /**
    * Get product COVER
    * @param int $id_lang Language id for multilingual legends
    * @return array Product images and legends
    */
    public function getCoverImage($id_lang, Context $context = null) {
    	
        return Db::getInstance()->executeS('
			SELECT image_shop.`cover`, i.`id_image`, il.`legend`, i.`position`
			FROM `'._DB_PREFIX_.'image` i
			'.Shop::addSqlAssociation('image', 'i').'
			LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
			WHERE i.`id_product` = '.(int)$this->id.' AND i.cover = 1'
        );
    }

    /**
    * Retourne la nom d'une déclinaison (liste de caractéristiques)
    * @param int $id_product_attribute
    * @return string
    **/
    public static function getCombinationName($id_product_attribute) {

    	$sql = "SELECT GROUP_CONCAT(CONCAT(pagl.public_name, ' : ', pal.name) separator ' | ')
				FROM ps_product_attribute_combination pac, ps_attribute pa, ps_attribute_lang pal, ps_attribute_group pag, ps_attribute_group_lang pagl
				WHERE pac.id_product_attribute = $id_product_attribute
				AND pac.id_attribute = pa.id_attribute
				AND pa.id_attribute_group = pag.id_attribute_group
				AND pa.id_attribute_group = pagl.id_attribute_group
				AND pa.id_attribute = pal.id_attribute
				AND pal.id_lang = 1
				AND pagl.id_lang = 1";

		return Db::getInstance()->getValue($sql);
    }

    /**
    * Retourne la référence fournisseur d'une déclinaison
    * @param int $id_product
    * @param int $id_product_attribute
    * @return string
    **/
    public static function getSupplierReference($id_product, $id_product_attribute) {
    	return Db::getInstance()->getValue("SELECT product_supplier_reference FROM ps_product_supplier WHERE id_product = $id_product AND id_product_attribute = $id_product_attribute");
    }

    /**
    * Retourne l'image de couverture d'un produit ou d'une de ses déclinaisons
    * UTILISATION : devis
    * @param int $id_product
    * @param int $id_product_attribute
    * @return Image
    **/
    public static function getCoverPicture($id_product, $id_product_attribute = null) {

    	if($id_product_attribute)
    		$id = Db::getInstance()->getValue("SELECT i.id_image FROM ps_image i, ps_product_attribute_image ai WHERE i.id_image = ai.id_image AND ai.id_product_attribute = $id_product_attribute ORDER BY i.position");
    	else
    		$id = Db::getInstance()->getValue("SELECT id_image FROM ps_image WHERE id_product = $id_product AND cover = 1 ORDER BY position");

    	return new Image($id);
    }
}