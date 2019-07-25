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

    public function getCustomer() {

        if(!$this->customer)
            $this->customer = new Customer($this->id_customer);

        return $this->customer;
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

            $lines = QuotationAssociation::getCartLines($this->id);
            foreach($lines as $line)
                $value += $line->getPrice($withTaxes);

        }

        if(in_array($type, array(Cart::BOTH, Cart::BOTH_WITHOUT_SHIPPING)))
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

        $quotations_lines = QuotationAssociation::getCartLines($id);
        self::$_nbProducts[$id] += count($quotations_lines);
        
        return self::$_nbProducts[$id];
    }

}