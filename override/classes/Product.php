<?php 

class Product extends ProductCore {

	/** @var float Custom Ecotax **/
	public $custom_ecotax;
	
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

		self::$definition['fields']['reference'] = array('type'=>self::TYPE_STRING, 'validate'=>'isReference', 'size'=>32, 'shop'=>true);
		self::$definition['fields']['name'] = array('type'=>self::TYPE_STRING, 'lang'=>true);
		self::$definition['fields']['description'] = array('type'=>self::TYPE_HTML, 'lang'=>true);
		self::$definition['fields']['meta_description'] = array('type'=>self::TYPE_STRING, 'lang'=>true);
		self::$definition['fields']['meta_keywords'] = array('type'=>self::TYPE_STRING, 'lang'=>true);
		self::$definition['fields']['rollcash'] = array('type'=>self::TYPE_FLOAT);
		self::$definition['fields']['comment_1'] = array('type'=>self::TYPE_STRING, 'shop'=>true);
		self::$definition['fields']['comment_2'] = array('type'=>self::TYPE_STRING, 'shop'=>true);
		self::$definition['fields']['batch'] = array('type'=>self::TYPE_INT, 'shop'=>true);
		self::$definition['fields']['destocking'] = array('type'=>self::TYPE_BOOL);
		self::$definition['fields']['custom_ecotax'] = array('type'=>self::TYPE_FLOAT, 'shop'=>true, 'validate'=>'isPrice');

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
        $sql = 'SELECT ag.`id_attribute_group`, pa.`batch`, ag.`is_color_group`, agl.`name` AS group_name, agl.`public_name` AS public_group_name,
					a.`id_attribute`, al.`name` AS attribute_name, a.`color` AS attribute_color, product_attribute_shop.`id_product_attribute`,
					IFNULL(stock.quantity, 0) as quantity, product_attribute_shop.`price`, product_attribute_shop.`ecotax`, product_attribute_shop.`weight`,
					product_attribute_shop.`default_on`, product_attribute_shop.`reference`, product_attribute_shop.`unit_price_impact`,
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
	* @param int|null $id_shop
	* @return array
	**/
	public static function getSimpleActiveProducts($id_lang = 1, $quotation = false, $id_shop = null) {
        
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
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (p.`id_product` = pa.`id_product`)'
				.($id_shop ? ' INNER JOIN `'._DB_PREFIX_.'product_shop` ps ON (p.`id_product` = ps.`id_product` AND ps.`id_shop` = '.$id_shop.') ' : '').
				'WHERE pl.`id_lang` = '.(int)$id_lang.'
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

    	if($id_product) {
	    	if($id_product_attribute)
	    		return Db::getInstance()->getValue("SELECT product_supplier_reference FROM ps_product_supplier WHERE id_product = $id_product AND id_product_attribute = $id_product_attribute");
	    	else
	    		return Db::getInstance()->getValue("SELECT product_supplier_reference FROM ps_product_supplier WHERE id_product = $id_product");
	    }

	    return null;
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
		return Db::getInstance()->executeS("SELECT fl.public_name AS name, fv.reference, fvl.value FROM ps_feature_product fp, ps_feature f, ps_feature_lang fl, ps_feature_value_lang fvl, ps_feature_value fv WHERE fp.id_feature = f.id_feature AND fl.id_feature = fp.id_feature AND fp.id_product = $id_product AND fp.id_feature_value = fvl.id_feature_value AND fv.id_feature_value = fvl.id_feature_value AND f.column = $nb_column ORDER BY f.position ASC");
	}

	/**
	* Retourne une liste de produits/déclinaisons en cherchant par les différentes références
	* @param string $search
	* @return array
	**/
	public static function searchByReference($search) {
		$id_shop = Context::getContext()->shop->id;
    	return Db::getInstance()->executeS("SELECT p.id_product, pas.id_product_attribute, ps.reference, pp.product_supplier_reference, pl.name, pas.reference AS combination_reference FROM ps_product p LEFT JOIN ps_product_lang pl ON (p.id_product = pl.id_product AND pl.id_lang = 1) LEFT JOIN ps_product_supplier pp ON (p.id_product = pp.id_product AND pp.product_supplier_reference LIKE '%$search%') LEFT JOIN ps_product_shop ps ON (p.id_product = ps.id_product AND ps.reference LIKE '%$search%' AND ps.id_shop = $id_shop) LEFT JOIN ps_product_attribute_shop pas ON (p.id_product = pas.id_product AND pas.reference LIKE '%$search%' AND pas.id_shop = $id_shop) WHERE (ps.reference IS NOT NULL or pp.product_supplier_reference IS NOT NULL OR pas.reference IS NOT NULL) GROUP BY pas.id_product_attribute");
    }

    /**
    * Vérifie si un produit possède des prix spécifiques
    **/
    public static function hasDegressivePrices($id_product) {

    	$nb_combinations = Db::getInstance()->getValue("SELECT COUNT(*) FROM ps_product_attribute WHERE id_product = $id_product");
    	if(!$nb_combinations) $nb_combinations = 1;

    	$nb_prices = Db::getInstance()->getValue("SELECT COUNT(*) FROM ps_specific_price WHERE id_product = $id_product");
    	return $nb_prices > $nb_combinations;
    }

    /**
    * Retourne le nombre de déclinaisons d'un produit
    * @param id_product
    * @return int
    **/
    public static function getNbCombinations($id_product) {
    	return (int)Db::getInstance()->getValue("SELECT COUNT(id_product_attribute) FROM ps_product_attribute WHERE id_product = $id_product");
    }

    /**
    * OVERRIDE : Suppression erreur pour export BEEZUP
    * Returns product price
    * @param int      $id_product            Product id
    * @param bool     $usetax                With taxes or not (optional)
    * @param int|null $id_product_attribute  Product attribute id (optional). If set to false, do not apply the combination price impact. NULL does apply the default combination price impact.
    * @param int      $decimals              Number of decimals (optional)
    * @param int|null $divisor               Useful when paying many time without fees (optional)
    * @param bool     $only_reduc            Returns only the reduction amount
    * @param bool     $usereduc              Set if the returned amount will include reduction
    * @param int      $quantity              Required for quantity discount application (default value: 1)
    * @param bool     $force_associated_tax  DEPRECATED - NOT USED Force to apply the associated tax. Only works when the parameter $usetax is true
    * @param int|null $id_customer           Customer ID (for customer group reduction)
    * @param int|null $id_cart               Cart ID. Required when the cookie is not accessible (e.g., inside a payment module, a cron task...)
    * @param int|null $id_address            Customer address ID. Required for price (tax included) calculation regarding the guest localization
    * @param null     $specific_price_output If a specific price applies regarding the previous parameters, this variable is filled with the corresponding SpecificPrice object
    * @param bool     $with_ecotax           Insert ecotax in price output.
    * @param bool     $use_group_reduction
    * @param Context  $context
    * @param bool     $use_customer_price
    * @return float                          Product price
    **/
    public static function getPriceStatic(
        $id_product,
        $usetax = true,
        $id_product_attribute = null,
        $decimals = 6,
        $divisor = null,
        $only_reduc = false,
        $usereduc = true,
        $quantity = 1,
        $force_associated_tax = false,
        $id_customer = null,
        $id_cart = null,
        $id_address = null,
        &$specific_price_output = null,
        $with_ecotax = true,
        $use_group_reduction = true,
        Context $context = null,
        $use_customer_price = true,
        $id_customization = null
    ) {
        if (!$context) {
            $context = Context::getContext();
        }

        $cur_cart = $context->cart;

        if ($divisor !== null) {
            Tools::displayParameterAsDeprecated('divisor');
        }

        if (!Validate::isBool($usetax) || !Validate::isUnsignedId($id_product)) {
            die(Tools::displayError());
        }

        // Initializations
        $id_group = null;
        if ($id_customer) {
            $id_group = Customer::getDefaultGroupId((int)$id_customer);
        }
        if (!$id_group) {
            $id_group = (int)Group::getCurrent()->id;
        }

        // If there is cart in context or if the specified id_cart is different from the context cart id
        if (!is_object($cur_cart) || (Validate::isUnsignedInt($id_cart) && $id_cart && $cur_cart->id != $id_cart)) {
            /*
            * When a user (e.g., guest, customer, Google...) is on PrestaShop, he has already its cart as the global (see /init.php)
            * When a non-user calls directly this method (e.g., payment module...) is on PrestaShop, he does not have already it BUT knows the cart ID
            * When called from the back office, cart ID can be inexistant
            */
            if (!$id_cart && !isset($context->employee)) {
                //die(Tools::displayError());
            }
            $cur_cart = new Cart($id_cart);
            // Store cart in context to avoid multiple instantiations in BO
            if (!Validate::isLoadedObject($context->cart)) {
                $context->cart = $cur_cart;
            }
        }

        $cart_quantity = 0;
        if ((int)$id_cart) {
            $cache_id = 'Product::getPriceStatic_'.(int)$id_product.'-'.(int)$id_cart;
            if (!Cache::isStored($cache_id) || ($cart_quantity = Cache::retrieve($cache_id) != (int)$quantity)) {
                $sql = 'SELECT SUM(`quantity`)
				FROM `'._DB_PREFIX_.'cart_product`
				WHERE `id_product` = '.(int)$id_product.'
				AND `id_cart` = '.(int)$id_cart;
                $cart_quantity = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                Cache::store($cache_id, $cart_quantity);
            } else {
                $cart_quantity = Cache::retrieve($cache_id);
            }
        }

        $id_currency = Validate::isLoadedObject($context->currency) ? (int)$context->currency->id : (int) Configuration::get('PS_CURRENCY_DEFAULT');

        if (!$id_address && Validate::isLoadedObject($cur_cart)) {
            $id_address = $cur_cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
        }

        // retrieve address informations
        $address = Address::initialize($id_address, true);
        $id_country = (int)$address->id_country;
        $id_state = (int)$address->id_state;
        $zipcode = $address->postcode;

        if (Tax::excludeTaxeOption()) {
            $usetax = false;
        }

        if ($usetax != false
            && !empty($address->vat_number)
            && $address->id_country != Configuration::get('VATNUMBER_COUNTRY')
            && Configuration::get('VATNUMBER_MANAGEMENT')) {
            $usetax = false;
        }

        if (is_null($id_customer) && Validate::isLoadedObject($context->customer)) {
            $id_customer = $context->customer->id;
        }

        $return = Product::priceCalculation(
            $context->shop->id,
            $id_product,
            $id_product_attribute,
            $id_country,
            $id_state,
            $zipcode,
            $id_currency,
            $id_group,
            $quantity,
            $usetax,
            $decimals,
            $only_reduc,
            $usereduc,
            $with_ecotax,
            $specific_price_output,
            $use_group_reduction,
            $id_customer,
            $use_customer_price,
            $id_cart,
            $cart_quantity,
            $id_customization
        );

        return $return;
    }

}