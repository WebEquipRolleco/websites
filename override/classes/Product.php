<?php 

class Product extends ProductCore {

	/** @var float Rollcash **/
	public $rollcash;

	/** @var string Commentaire 1 **/
    public $comment_1;

    /** @var string Commentaire 2 **/
    public $comment_2;

    /** @var int Batch **/
    public $batch = 1;

	/** @var bool Destocking **/
	public $destocking = false;

	/** Variables temporaires **/
	private $supplier;

	public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, Context $context = null) {

		self::$definition['fields']['rollcash'] = array('type' => self::TYPE_FLOAT);
		self::$definition['fields']['comment_1'] = array('type' => self::TYPE_STRING, 'shop'=>true);
		self::$definition['fields']['comment_2'] = array('type' => self::TYPE_STRING, 'shop'=>true);
		self::$definition['fields']['batch'] = array('type' => self::TYPE_INT, 'shop'=>true);
		self::$definition['fields']['destocking'] = array('type' => self::TYPE_BOOL);
		parent::__construct($id_product, $full, $id_lang, $id_shop, $context);
	}
	
	/**
	* Retourne le fournisseur du produit
	**/
	public function getSupplier() {

		if(!$this->supplier and $this->id_supplier)
			$this->supplier = new Supplier($this->id_supplier);

		return $this->supplier;
	}

	/**
    * Get all available attribute groups
    * OVERRIDE : modification ordre d'affichage dans la liste
    *
    * @param int $id_lang Language id
    * @return array Attribute groups
    **/
    public function getAttributesGroups($id_lang)
    {
        if (!Combination::isFeatureActive()) {
            return array();
        }
        $sql = 'SELECT ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, agl.`public_name` AS public_group_name,
					a.`id_attribute`, al.`name` AS attribute_name, a.`color` AS attribute_color, product_attribute_shop.`id_product_attribute`,
					IFNULL(stock.quantity, 0) as quantity, product_attribute_shop.`price`, product_attribute_shop.`ecotax`, product_attribute_shop.`weight`,
					product_attribute_shop.`default_on`, pa.`reference`, product_attribute_shop.`unit_price_impact`,
					product_attribute_shop.`minimal_quantity`, product_attribute_shop.`available_date`, ag.`group_type`
				FROM `'._DB_PREFIX_.'product_attribute` pa
				'.Shop::addSqlAssociation('product_attribute', 'pa').'
				'.Product::sqlStock('pa', 'pa').'
				LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
				LEFT JOIN `'._DB_PREFIX_.'attribute` a ON (a.`id_attribute` = pac.`id_attribute`)
				LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON (ag.`id_attribute_group` = a.`id_attribute_group`)
				LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute`)
				LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group`)
				'.Shop::addSqlAssociation('attribute', 'a').'
				WHERE pa.`id_product` = '.(int)$this->id.'
					AND al.`id_lang` = '.(int)$id_lang.'
					AND agl.`id_lang` = '.(int)$id_lang.'
				GROUP BY id_attribute_group, id_product_attribute
				ORDER BY pa.position ASC, ag.`position` ASC, a.`position` ASC, agl.`name` ASC';
        return Db::getInstance()->executeS($sql);
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
    public static function getSupplierReference($id_product, $id_product_attribute = null) {

    	if($id_product_attribute)
    		return Db::getInstance()->getValue("SELECT product_supplier_reference FROM ps_product_supplier WHERE id_product = $id_product AND id_product_attribute = $id_product_attribute");
    	else
    		return Db::getInstance()->getValue("SELECT product_supplier_reference FROM ps_product_supplier WHERE id_product = $id_product");
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
    	
    	if(!isset($id) or !$id)
    		$id = Db::getInstance()->getValue("SELECT id_image FROM ps_image WHERE id_product = $id_product AND cover = 1 ORDER BY position");

    	return new Image($id);
    }

    /**
	* Retourne les informations contenues dans la colonne 1 des declinaisons
	* @param int $id_product
	* @param int $nb_column
	* @return string
	**/
	public static function loadColumn($id_product, $nb_column) {
		return Db::getInstance()->executeS("SELECT fl.public_name AS name, fvl.value FROM ps_feature_product fp, ps_feature f, ps_feature_lang fl, ps_feature_value_lang fvl WHERE fp.id_feature = f.id_feature AND fl.id_feature = fp.id_feature AND fp.id_product = $id_product AND fp.id_feature_value = fvl.id_feature_value AND f.column = $nb_column ORDER BY f.position ASC");
	}
}