<?php

class HTMLTemplateProductCore extends HTMLTemplate {
    
    public $context;
    public $product;
    public $shop;
    public $company;

    /**
     * @param OrderInvoice $order_invoice
     * @param $smarty
     * @throws PrestaShopException
     */
    public function __construct(Product $product, $smarty, $bulk_mode = false) {
        
        $this->product = $product;
        $this->smarty = $smarty;
        $this->context = Context::getContext();
        $this->shop = new Shop($this->context->cart->id_shop);

        $this->company['name'] = Configuration::get('PS_SHOP_NAME');
        $this->company['address_1'] = Configuration::get('PS_SHOP_ADDR1');
        $this->company['address_2'] = Configuration::get('PS_SHOP_ADDR2');
        $this->company['code'] = Configuration::get('PS_SHOP_CODE');
        $this->company['city'] = Configuration::get('PS_SHOP_CITY');
        $this->company['email'] = Configuration::get('PS_SHOP_EMAIL');
        $this->company['phone'] = Configuration::get('PS_SHOP_PHONE');
        $this->company['fax'] = Configuration::get('PS_SHOP_FAX');
        $this->company['details'] = Configuration::get('PS_SHOP_DETAILS');
    }

    /**
    * Returns the template's HTML header
    * @return string HTML header
    **/
    public function getHeader() {

        //$this->assignCommonHeaderData();
        $this->smarty->assign('product', $this->product);
        $this->smarty->assign('style_tab', $this->smarty->fetch($this->getTemplate('style-tab')));
        return $this->smarty->fetch($this->getTemplate('product.header'));
    }

    /**
    * Returns the template's HTML footer
    * @return string HTML footer
    **/
    public function getFooter() {

        //$this->assignCommonHeaderData();
        $this->smarty->assign('company', $this->company);
        $this->smarty->assign('date', date('d/m/Y H:i:s'));
        $this->smarty->assign('style_tab', $this->smarty->fetch($this->getTemplate('style-tab')));
        return $this->smarty->fetch($this->getTemplate('product.footer'));
    }

    public function getPagination() {
        return false;
    }

    /**
    * Returns the template's HTML content
    * @return string HTML content
    **/
    public function getContent() {

        $data['combinations'] = array();
        $data['groups'] = array();

        $attributes_groups = $this->product->getAttributesGroups($this->context->language->id);
        if (is_array($attributes_groups) && $attributes_groups) {

            $combination_prices_set = array();
            foreach ($attributes_groups as $k => $row) {

                if (!isset($data['groups'][$row['id_attribute_group']])) {
                    $data['groups'][$row['id_attribute_group']] = array(
                        'group_name' => $row['group_name'],
                        'name' => $row['public_group_name'],
                        'group_type' => $row['group_type'],
                        'default' => -1,
                    );
                }

                $data['combinations'][$row['id_product_attribute']]['attributes_values'][$row['id_attribute_group']] = $row['attribute_name'];
                $data['combinations'][$row['id_product_attribute']]['attributes'][] = (int) $row['id_attribute'];
                $data['combinations'][$row['id_product_attribute']]['price'] = (float) $row['price'];

                // Call getPriceStatic in order to set $combination_specific_price
                if (!isset($combination_prices_set[(int) $row['id_product_attribute']])) {
                    $combination_specific_price = null;
                    Product::getPriceStatic((int) $this->product->id, false, $row['id_product_attribute'], 6, null, false, true, 1, false, null, null, null, $combination_specific_price);
                    $combination_prices_set[(int) $row['id_product_attribute']] = true;
                    $data['combinations'][$row['id_product_attribute']]['specific_price'] = $combination_specific_price;
                }
                $data['combinations'][$row['id_product_attribute']]['ecotax'] = (float) $row['ecotax'];
                $data['combinations'][$row['id_product_attribute']]['weight'] = (float) $row['weight'];
                $data['combinations'][$row['id_product_attribute']]['quantity'] = (int) $row['quantity'];
                $data['combinations'][$row['id_product_attribute']]['reference'] = $row['reference'];
                $data['combinations'][$row['id_product_attribute']]['unit_impact'] = $row['unit_price_impact'];
                $data['combinations'][$row['id_product_attribute']]['minimal_quantity'] = $row['minimal_quantity'];
                if ($row['available_date'] != '0000-00-00' && Validate::isDate($row['available_date'])) {
                    $data['combinations'][$row['id_product_attribute']]['available_date'] = $row['available_date'];
                    $data['combinations'][$row['id_product_attribute']]['date_formatted'] = Tools::displayDate($row['available_date']);
                } else {
                    $data['combinations'][$row['id_product_attribute']]['available_date'] = $data['combinations'][$row['id_product_attribute']]['date_formatted'] = '';
                }
            }
        }

        $data['link'] = new Link();
        $data['product'] = $this->product;
        $data['company'] = $this->company;
        //$data['style_tab'] = $this->smarty->fetch($this->getTemplate('rolleco.style-tab'));
        $this->smarty->assign($data);

        return $this->smarty->fetch($this->getTemplate('product'));
    }

    /**
    * Returns the template filename when using bulk rendering
    * @return string filename
    **/
    public function getBulkFilename() {
        return $this->product->link_rewrite.'-'.$this->product->id.'.pdf';
    }

    /**
    * Returns the template filename
    * @return string filename
    **/
    public function getFilename() {
        return $this->product->link_rewrite.'-'.$this->product->id.'.pdf';
    }

}
