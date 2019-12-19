<?php

use PrestaShop\PrestaShop\Adapter\AddressFactory;
use PrestaShop\PrestaShop\Adapter\Cache\CacheAdapter;
use PrestaShop\PrestaShop\Adapter\Customer\CustomerDataProvider;
use PrestaShop\PrestaShop\Adapter\Group\GroupDataProvider;
use PrestaShop\PrestaShop\Adapter\Product\PriceCalculator;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Adapter\Database;
use PrestaShop\PrestaShop\Core\Cart\Calculator;
use PrestaShop\PrestaShop\Core\Cart\CartRow;
use PrestaShop\PrestaShop\Core\Cart\CartRuleData;

class Cart extends CartCore {

    /** Variables temporaires **/
    private $customer;
    private $address_invoice;
    private $address_delivery;

    /**
    * OVERRIDE : Correction bug avec les lignes sans produits
    * Update Product quantity
    *
    * @param int    $quantity             Quantity to add (or substract)
    * @param int    $id_product           Product ID
    * @param int    $id_product_attribute Attribute ID if needed
    * @param string $operator             Indicate if quantity must be increased or decreased
    * @return bool Whether the quantity has been succesfully updated
    **/
    public function updateQty($quantity, $id_product, $id_product_attribute = null, $id_customization = false, $operator = 'up', $id_address_delivery = 0, Shop $shop = null, $auto_add_cart_rule = true, $skipAvailabilityCheckOutOfStock = false) {
        if($id_product)
            return parent::updateQty($quantity, $id_product, $id_product_attribute, $id_customization, $operator, $id_address_delivery, $shop, $auto_add_cart_rule, $skipAvailabilityCheckOutOfStock);
        return true;
    }

    public function getCustomer() {

        if(!$this->customer)
            $this->customer = new Customer($this->id_customer);

        return $this->customer;
    }

    public function getAddressInvoice() {

        if(!$this->address_invoice)
            $this->address_invoice = new Address($this->id_address_invoice);

        return $this->address_invoice;
    }

    public function getAddressDelivery() {

        if(!$this->address_delivery)
            $this->address_delivery = new Address($this->id_address_delivery);

        return $this->address_delivery;
    }

    public function allowOption($id_option) {

        foreach(QuotationAssociation::find($this->id) as $quotation) {
            if(!in_array($id_option, $quotation->getOptions()))
                return false;
        }

        return true;
    }

    private function newCalculator($products, $cartRules, $id_carrier)
    {
        $calculator = new Calculator($this, $id_carrier);

        /** @var PriceCalculator $priceCalculator */
        $priceCalculator = ServiceLocator::get(PriceCalculator::class);

        // set cart rows (products)
        $useEcotax = $this->configuration->get('PS_USE_ECOTAX');
        $precision = $this->configuration->get('_PS_PRICE_COMPUTE_PRECISION_');
        $configRoundType = $this->configuration->get('PS_ROUND_TYPE');
        $roundTypes = [
            Order::ROUND_TOTAL => CartRow::ROUND_MODE_TOTAL,
            Order::ROUND_LINE  => CartRow::ROUND_MODE_LINE,
            Order::ROUND_ITEM  => CartRow::ROUND_MODE_ITEM,
        ];
        if (isset($roundTypes[$configRoundType])) {
            $roundType = $roundTypes[$configRoundType];
        } else {
            $roundType = CartRow::ROUND_MODE_ITEM;
        }

        foreach ($products as $product) {
            $cartRow = new CartRow(
                $product,
                $priceCalculator,
                new AddressFactory,
                new CustomerDataProvider,
                new CacheAdapter,
                new GroupDataProvider,
                new Database,
                $useEcotax,
                $precision,
                $roundType
            );
            $calculator->addCartRow($cartRow);
        }

        // set cart rules
        foreach ($cartRules as $cartRule) {
            $calculator->addCartRule(new CartRuleData($cartRule));
        }

        return $calculator;
    }

    public function getOrderTotal($withTaxes = true, $type = Cart::BOTH, $products = null, $id_carrier = null, $use_cache = false) {

        if((int) $id_carrier <= 0)
            $id_carrier = null;

        // deprecated type
        if($type == Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING)
            $type = Cart::ONLY_PRODUCTS;

        // check type
        $type = (int)$type;
        $allowedTypes = array(Cart::ONLY_PRODUCTS, Cart::ONLY_DISCOUNTS, Cart::BOTH, Cart::BOTH_WITHOUT_SHIPPING, Cart::ONLY_SHIPPING, Cart::ONLY_WRAPPING, Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING,
        );

        if(!in_array($type, $allowedTypes))
            throw new \Exception('Invalid calculation type: ' . $type);

        // EARLY RETURNS

        // if cart rules are not used
        if($type == Cart::ONLY_DISCOUNTS && !CartRule::isFeatureActive())
            return 0;

        // no shipping cost if is a cart with only virtuals products
        $virtual = $this->isVirtualCart();
        if($virtual && $type == Cart::ONLY_SHIPPING) 
            return 0;

        if($virtual && $type == Cart::BOTH)
            $type = Cart::BOTH_WITHOUT_SHIPPING;

        // filter products
        if(is_null($products))
            $products = $this->getProducts();

        if($type == Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING) {
            foreach($products as $key => $product) {
                if($product['is_virtual'])
                    unset($products[$key]);
            }
            $type = Cart::ONLY_PRODUCTS;
        }

        if(Tax::excludeTaxeOption())
            $withTaxes = false;

        // CART CALCULATION
        $cartRules = array();
        if(in_array($type, [Cart::BOTH, Cart::ONLY_DISCOUNTS]))
            $cartRules = $this->getCartRules();

        $calculator = $this->newCalculator($products, $cartRules, $id_carrier);
        $computePrecision = $this->configuration->get('_PS_PRICE_COMPUTE_PRECISION_');
        switch ($type) {
            case Cart::ONLY_SHIPPING:
                $calculator->calculateRows();
                $calculator->calculateFees($computePrecision);
                $amount = $calculator->getFees()->getInitialShippingFees();
                break;
            case Cart::ONLY_WRAPPING:
                $calculator->calculateRows();
                $calculator->calculateFees($computePrecision);
                $amount = $calculator->getFees()->getInitialWrappingFees();
                break;
            case Cart::BOTH:
                $calculator->processCalculation($computePrecision);
                $amount = $calculator->getTotal();
                break;
            case Cart::BOTH_WITHOUT_SHIPPING:
            case Cart::ONLY_PRODUCTS:
                $calculator->calculateRows();
                $amount = $calculator->getRowTotal();
                break;
            case Cart::ONLY_DISCOUNTS:
                $calculator->processCalculation($computePrecision);
                $amount = $calculator->getDiscountTotal();
                break;
            default:
                throw new \Exception('unknown cart calculation type : ' . $type);
        }

        // TAXES ?

        $value = $withTaxes ? $amount->getTaxIncluded() : $amount->getTaxExcluded();

        if(in_array($type, array(Cart::BOTH, Cart::BOTH_WITHOUT_SHIPPING, Cart::ONLY_PRODUCTS))) {

            foreach(QuotationAssociation::find($this->id) as $quotation)
                foreach($quotation->getProducts() as $line)
                    $value += $line->getPrice($withTaxes);

        }

        if(!$products and in_array($type, array(Cart::BOTH, Cart::BOTH_WITHOUT_SHIPPING, Cart::ONLY_PRODUCTS)))
            $value += OrderOptionCart::getCartTotal($this->id);

        // ROUND AND RETURN
        $compute_precision = $this->configuration->get('_PS_PRICE_COMPUTE_PRECISION_');
        return Tools::ps_round($value, $compute_precision);
    }

    /**
    * OVERRIDE : prendre en compte les devis
    **/
    public static function getNbProducts($id) {

        // Must be strictly compared to NULL, or else an empty cart will bypass the cache and add dozens of queries
        if (isset(self::$_nbProducts[$id]) && self::$_nbProducts[$id] !== null) {
            return self::$_nbProducts[$id];
        }

        self::$_nbProducts[$id] = (int)Db::getInstance()->getValue(
            'SELECT SUM(`quantity`)
            FROM `'._DB_PREFIX_.'cart_product`
            WHERE `id_cart` = '.(int)$id
        );

        self::$_nbProducts[$id] += QuotationAssociation::countProducts($id);
        
        return self::$_nbProducts[$id];
    }

    /**
    * Retourne le montant de la taxe écologique 
    * @param Cart $cart
    * @return float
    **/
    public static function getEcoTax($cart = null) {

        if(!$cart)
            $cart = Context::getContext()->cart;

        $value = (float)Db::getInstance()->getValue("SELECT SUM(l.eco_tax) FROM ps_quotation_line l, ps_quotation_association a WHERE a.id_quotation = l.id_quotation AND a.id_cart = ".$cart->id);

        foreach($cart->getProducts() as $product)
            $value += $product['ecotax'] * $product['quantity'];

        return $value;
    }

    /**
    * Are all products of the Cart in stock?
    * OVERRIDE : wtf ? n'ajoute pas les options de stock
    *
    * @param bool $ignore_virtual Ignore virtual products
    * @param bool $exclusive (DEPRECATED) If true, the validation is exclusive : it must be present product in stock and out of stock
    * @since 1.5.0
    *
    * @return bool False if not all products in the cart are in stock
    */
    public function isAllProductsInStock($ignoreVirtual = false, $exclusive = false)
    {
        if (func_num_args() > 1) {
            @trigger_error(
                '$exclusive parameter is deprecated since version 1.7.3.2 and will be removed in the next major version.',
                E_USER_DEPRECATED
            );
        }
        $productOutOfStock = 0;
        $productInStock = 0;

        foreach ($this->getProducts() as $product) {
            if ($ignoreVirtual && $product['is_virtual']) {
                continue;
            }
            $idProductAttribute = !empty($product['id_product_attribute']) ? $product['id_product_attribute'] : null;
            $availableOutOfStock = Product::isAvailableWhenOutOfStock($product['out_of_stock']);
            $productQuantity = Product::getQuantity(
                $product['id_product'],
                $idProductAttribute,
                null,
                $this,
                $product['id_customization']
            );

            if (!$exclusive
                && ($productQuantity < 0 && !$availableOutOfStock)
            ) {
                return false;
            } else if ($exclusive) {
                if ($productQuantity <= 0) {
                    $productOutOfStock++;
                } else {
                    $productInStock++;
                }

                if ($productInStock > 0 && $productOutOfStock > 0) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
    * Return cart products
    * OVERRIDE : ajout commentaire_1, commentaire_2, rollcash
    *
    * @param bool $refresh
    * @param bool $id_product
    * @param int  $id_country
    * @param bool $fullInfos
    * @return array Products
    **/
    public function getProducts($refresh = false, $id_product = false, $id_country = null, $fullInfos = true) {

        if (!$this->id) {
            return array();
        }
        // Product cache must be strictly compared to NULL, or else an empty cart will add dozens of queries
        if ($this->_products !== null && !$refresh) {
            // Return product row with specified ID if it exists
            if (is_int($id_product)) {
                foreach ($this->_products as $product) {
                    if ($product['id_product'] == $id_product) {
                        return array($product);
                    }
                }
                return array();
            }
            return $this->_products;
        }

        // Build query
        $sql = new DbQuery();

        // Build SELECT
        $sql->select('p.`comment_1`, p.`comment_2`, p.`rollcash`, cp.`id_product_attribute`, cp.`id_product`, cp.`quantity` AS cart_quantity, cp.id_shop, cp.`id_customization`, pl.`name`, p.`is_virtual`, pl.`description_short`, pl.`available_now`, pl.`available_later`, product_shop.`id_category_default`, p.`id_supplier`, p.`id_manufacturer`, m.`name` AS manufacturer_name, product_shop.`on_sale`, product_shop.`ecotax`, product_shop.`additional_shipping_cost`, product_shop.`available_for_order`, product_shop.`show_price`, product_shop.`price`, product_shop.`active`, product_shop.`unity`, product_shop.`unit_price_ratio`, stock.`quantity` AS quantity_available, p.`width`, p.`height`, p.`depth`, stock.`out_of_stock`, p.`weight`, p.`available_date`, p.`date_add`, p.`date_upd`, IFNULL(stock.quantity, 0) as quantity, pl.`link_rewrite`, cl.`link_rewrite` AS category, CONCAT(LPAD(cp.`id_product`, 10, 0), LPAD(IFNULL(cp.`id_product_attribute`, 0), 10, 0), IFNULL(cp.`id_address_delivery`, 0), IFNULL(cp.`id_customization`, 0)) AS unique_id, cp.id_address_delivery, product_shop.advanced_stock_management, ps.product_supplier_reference supplier_reference');

        // Build FROM
        $sql->from('cart_product', 'cp');

        // Build JOIN
        $sql->leftJoin('product', 'p', 'p.`id_product` = cp.`id_product`');
        $sql->innerJoin('product_shop', 'product_shop', '(product_shop.`id_shop` = cp.`id_shop` AND product_shop.`id_product` = p.`id_product`)');
        $sql->leftJoin(
            'product_lang',
            'pl',
            'p.`id_product` = pl.`id_product`
            AND pl.`id_lang` = '.(int)$this->id_lang.Shop::addSqlRestrictionOnLang('pl', 'cp.id_shop')
        );

        $sql->leftJoin(
            'category_lang',
            'cl',
            'product_shop.`id_category_default` = cl.`id_category`
            AND cl.`id_lang` = '.(int)$this->id_lang.Shop::addSqlRestrictionOnLang('cl', 'cp.id_shop')
        );

        $sql->leftJoin('product_supplier', 'ps', 'ps.`id_product` = cp.`id_product` AND ps.`id_product_attribute` = cp.`id_product_attribute` AND ps.`id_supplier` = p.`id_supplier`');
        $sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');

        // @todo test if everything is ok, then refactorise call of this method
        $sql->join(Product::sqlStock('cp', 'cp'));

        // Build WHERE clauses
        $sql->where('cp.`id_cart` = '.(int)$this->id);
        if ($id_product) {
            $sql->where('cp.`id_product` = '.(int)$id_product);
        }
        $sql->where('p.`id_product` IS NOT NULL');

        // Build ORDER BY
        $sql->orderBy('cp.`date_add`, cp.`id_product`, cp.`id_product_attribute` ASC');

        if (Customization::isFeatureActive()) {
            $sql->select('cu.`id_customization`, cu.`quantity` AS customization_quantity');
            $sql->leftJoin(
                'customization',
                'cu',
                'p.`id_product` = cu.`id_product` AND cp.`id_product_attribute` = cu.`id_product_attribute` AND cp.`id_customization` = cu.`id_customization` AND cu.`id_cart` = '.(int)$this->id
            );
            $sql->groupBy('cp.`id_product_attribute`, cp.`id_product`, cp.`id_shop`, cp.`id_customization`');
        } else {
            $sql->select('NULL AS customization_quantity, NULL AS id_customization');
        }

        if (Combination::isFeatureActive()) {
            $sql->select('
                product_attribute_shop.`price` AS price_attribute, product_attribute_shop.`ecotax` AS ecotax_attr, product_attribute_shop.`rollcash` AS rollcash_attr,
                IF (IFNULL(pa.`reference`, \'\') = \'\', p.`reference`, pa.`reference`) AS reference,
                (p.`weight`+ pa.`weight`) weight_attribute,
                IF (IFNULL(pa.`ean13`, \'\') = \'\', p.`ean13`, pa.`ean13`) AS ean13,
                IF (IFNULL(pa.`isbn`, \'\') = \'\', p.`isbn`, pa.`isbn`) AS isbn,
                IF (IFNULL(pa.`upc`, \'\') = \'\', p.`upc`, pa.`upc`) AS upc,
                IFNULL(product_attribute_shop.`minimal_quantity`, product_shop.`minimal_quantity`) as minimal_quantity,
                IF(product_attribute_shop.wholesale_price > 0,  product_attribute_shop.wholesale_price, product_shop.`wholesale_price`) wholesale_price
            ');

            $sql->leftJoin('product_attribute', 'pa', 'pa.`id_product_attribute` = cp.`id_product_attribute`');
            $sql->leftJoin('product_attribute_shop', 'product_attribute_shop', '(product_attribute_shop.`id_shop` = cp.`id_shop` AND product_attribute_shop.`id_product_attribute` = pa.`id_product_attribute`)');
        } else {
            $sql->select(
                'p.`reference` AS reference, p.`ean13`, p.`isbn`,
                p.`upc` AS upc, product_shop.`minimal_quantity` AS minimal_quantity, product_shop.`wholesale_price` wholesale_price'
            );
        }

        $sql->select('image_shop.`id_image` id_image, il.`legend`');
        $sql->leftJoin('image_shop', 'image_shop', 'image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$this->id_shop);
        $sql->leftJoin('image_lang', 'il', 'il.`id_image` = image_shop.`id_image` AND il.`id_lang` = '.(int)$this->id_lang);

        $result = Db::getInstance()->executeS($sql);

        // Reset the cache before the following return, or else an empty cart will add dozens of queries
        $products_ids = array();
        $pa_ids = array();
        if ($result) {
            foreach ($result as $key => $row) {
                $products_ids[] = $row['id_product'];
                $pa_ids[] = $row['id_product_attribute'];
                $specific_price = SpecificPrice::getSpecificPrice($row['id_product'], $this->id_shop, $this->id_currency, $id_country, $this->id_shop_group, $row['cart_quantity'], $row['id_product_attribute'], $this->id_customer, $this->id);
                if ($specific_price) {
                    $reduction_type_row = array('reduction_type' => $specific_price['reduction_type']);
                } else {
                    $reduction_type_row = array('reduction_type' => 0);
                }

                $result[$key] = array_merge($row, $reduction_type_row);
            }
        }
        // Thus you can avoid one query per product, because there will be only one query for all the products of the cart
        Product::cacheProductsFeatures($products_ids);
        Cart::cacheSomeAttributesLists($pa_ids, $this->id_lang);

        $this->_products = array();
        if (empty($result)) {
            return array();
        }

        if ($fullInfos) {
            $ecotax_rate = (float)Tax::getProductEcotaxRate($this->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
            $apply_eco_tax = Product::$_taxCalculationMethod == PS_TAX_INC && (int)Configuration::get('PS_TAX');
            $cart_shop_context = Context::getContext()->cloneContext();

            $gifts = $this->getCartRules(CartRule::FILTER_ACTION_GIFT);
            $givenAwayProductsIds = array();

            if ($this->shouldSplitGiftProductsQuantity && count($gifts) > 0) {
                foreach ($gifts as $gift) {
                    foreach ($result as $rowIndex => $row) {
                        if (!array_key_exists('is_gift', $result[$rowIndex])) {
                            $result[$rowIndex]['is_gift'] = false;
                        }

                        if (
                            $row['id_product'] == $gift['gift_product'] &&
                            $row['id_product_attribute'] == $gift['gift_product_attribute']
                        ) {
                            $row['is_gift'] = true;
                            $result[$rowIndex] = $row;
                        }
                    }

                    $index = $gift['gift_product'] . '-' . $gift['gift_product_attribute'];
                    if (!array_key_exists($index, $givenAwayProductsIds)) {
                        $givenAwayProductsIds[$index] = 1;
                    } else {
                        $givenAwayProductsIds[$index]++;
                    }
                }
            }

            foreach ($result as &$row) {

                if (!array_key_exists('is_gift', $row)) {
                    $row['is_gift'] = false;
                }

                if (!array_key_exists('allow_oosp', $row)) {
                    $row['allow_oosp'] = true;
                }

                try {
                    $additionalRow = Product::getProductProperties((int)$this->id_lang, $row);
                    $row['reduction'] = $additionalRow['reduction'];
                    $row['price_without_reduction'] = $additionalRow['price_without_reduction'];
                    $row['specific_prices'] = $additionalRow['specific_prices'];
                    unset($additionalRow);
                }
                catch(Exception $e) { }

                $givenAwayQuantity = 0;
                $giftIndex = $row['id_product'] . '-' . $row['id_product_attribute'];
                if ($row['is_gift'] && array_key_exists($giftIndex, $givenAwayProductsIds)) {
                    $givenAwayQuantity = $givenAwayProductsIds[$giftIndex];
                }

                if (!$row['is_gift'] || (int)$row['cart_quantity'] === $givenAwayQuantity) {
                    $row = $this->applyProductCalculations($row, $cart_shop_context);
                } else {
                    // Separate products given away from those manually added to cart
                    $this->_products[] = $this->applyProductCalculations($row, $cart_shop_context, $givenAwayQuantity);
                    unset($row['is_gift']);
                    $row = $this->applyProductCalculations(
                        $row,
                        $cart_shop_context,
                        $row['cart_quantity'] - $givenAwayQuantity
                    );
                }

                $this->_products[] = $row;
            }
        } else {
            $this->_products = $result;
        }

        return $this->_products;
    }

    /**
    * OVERRIDE : ajout rollcash
    *
    * @param $row
    * @param $shopContext
    * @param $productQuantity
    * @return mixed
    **/
    protected function applyProductCalculations($row, $shopContext, $productQuantity = null) {

        if (is_null($productQuantity)) {
            $productQuantity = (int)$row['cart_quantity'];
        }

        if (isset($row['ecotax_attr']) && $row['ecotax_attr'] > 0) {
            $row['ecotax'] = (float)$row['ecotax_attr'];
        }

        if(isset($row['rollcash_attr']) && $row['rollcash_attr'] > 0)
            $row['rollcash'] = (float)$row['rollcash_attr'];

        $row['stock_quantity'] = (int)$row['quantity'];
        // for compatibility with 1.2 themes
        $row['quantity'] = $productQuantity;

        // get the customization weight impact
        $customization_weight = Customization::getCustomizationWeight($row['id_customization']);

        if (isset($row['id_product_attribute']) && (int)$row['id_product_attribute'] && isset($row['weight_attribute'])) {
            $row['weight_attribute'] += $customization_weight;
            $row['weight'] = (float)$row['weight_attribute'];
        } else {
            $row['weight'] += $customization_weight;
        }

        if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
            $address_id = (int)$this->id_address_invoice;
        } else {
            $address_id = (int)$row['id_address_delivery'];
        }
        if (!Address::addressExists($address_id)) {
            $address_id = null;
        }

        if ($shopContext->shop->id != $row['id_shop']) {
            $shopContext->shop = new Shop((int)$row['id_shop']);
        }

        $address = Address::initialize($address_id, true);
        $id_tax_rules_group = Product::getIdTaxRulesGroupByIdProduct((int)$row['id_product'], $shopContext);
        $tax_calculator = TaxManagerFactory::getManager($address, $id_tax_rules_group)->getTaxCalculator();

        $specific_price_output = null;

        $row['price_without_reduction'] = Product::getPriceStatic(
            (int)$row['id_product'],
            true,
            isset($row['id_product_attribute']) ? (int)$row['id_product_attribute'] : null,
            6,
            null,
            false,
            false,
            $productQuantity,
            false,
            (int)$this->id_customer ? (int)$this->id_customer : null,
            (int)$this->id,
            $address_id,
            $specific_price_output,
            true,
            true,
            $shopContext,
            true,
            $row['id_customization']
        );

        $row['price_with_reduction'] = Product::getPriceStatic(
            (int)$row['id_product'],
            true,
            isset($row['id_product_attribute']) ? (int)$row['id_product_attribute'] : null,
            6,
            null,
            false,
            true,
            $productQuantity,
            false,
            (int)$this->id_customer ? (int)$this->id_customer : null,
            (int)$this->id,
            $address_id,
            $specific_price_output,
            true,
            true,
            $shopContext,
            true,
            $row['id_customization']
        );

        $row['price'] = $row['price_with_reduction_without_tax'] = Product::getPriceStatic(
            (int)$row['id_product'],
            false,
            isset($row['id_product_attribute']) ? (int)$row['id_product_attribute'] : null,
            6,
            null,
            false,
            true,
            $productQuantity,
            false,
            (int)$this->id_customer ? (int)$this->id_customer : null,
            (int)$this->id,
            $address_id,
            $specific_price_output,
            true,
            true,
            $shopContext,
            true,
            $row['id_customization']
        );

        switch (Configuration::get('PS_ROUND_TYPE')) {
            case Order::ROUND_TOTAL:
                $row['total'] = $row['price_with_reduction_without_tax'] * $productQuantity;
                $row['total_wt'] = $row['price_with_reduction'] * $productQuantity;
                break;
            case Order::ROUND_LINE:
                $row['total'] = Tools::ps_round(
                    $row['price_with_reduction_without_tax'] * $productQuantity,
                    _PS_PRICE_COMPUTE_PRECISION_
                );
                $row['total_wt'] = Tools::ps_round(
                    $row['price_with_reduction'] * $productQuantity,
                    _PS_PRICE_COMPUTE_PRECISION_
                );
                break;

            case Order::ROUND_ITEM:
            default:
                $row['total'] = Tools::ps_round(
                        $row['price_with_reduction_without_tax'],
                        _PS_PRICE_COMPUTE_PRECISION_
                    ) * $productQuantity;
                $row['total_wt'] = Tools::ps_round(
                        $row['price_with_reduction'],
                        _PS_PRICE_COMPUTE_PRECISION_
                    ) * $productQuantity;
                break;
        }

        $row['price_wt'] = $row['price_with_reduction'];
        $row['description_short'] = Tools::nl2br($row['description_short']);

        // check if a image associated with the attribute exists
        if ($row['id_product_attribute']) {
            $row2 = Image::getBestImageAttribute($row['id_shop'], $this->id_lang, $row['id_product'], $row['id_product_attribute']);
            if ($row2) {
                $row = array_merge($row, $row2);
            }
        }

        $row['reduction_applies'] = ($specific_price_output && (float)$specific_price_output['reduction']);
        $row['quantity_discount_applies'] = ($specific_price_output && $productQuantity >= (int)$specific_price_output['from_quantity']);
        $row['id_image'] = Product::defineProductImage($row, $this->id_lang);
        $row['allow_oosp'] = Product::isAvailableWhenOutOfStock($row['out_of_stock']);
        $row['features'] = Product::getFeaturesStatic((int)$row['id_product']);

        if (array_key_exists($row['id_product_attribute'] . '-' . $this->id_lang, self::$_attributesLists)) {
            $row = array_merge($row, self::$_attributesLists[$row['id_product_attribute'] . '-' . $this->id_lang]);
        }

        return Product::getTaxesInformations($row, $shopContext);
    }

}