<?php

abstract class HTMLTemplate extends HTMLTemplateCore {

	protected $display_footer = true;

	/**
    * Returns the template's HTML footer
    * @return string HTML footer
    **/
    public function getFooter() {

    	if(!$this->display_footer)
    		return;

        $shop_address = $this->getShopAddress();
        $id_shop = (int)$this->shop->id;

        $this->smarty->assign(array(
            'available_in_your_account' => $this->available_in_your_account,
            'shop_address' => $shop_address,
            'shop_fax' => Configuration::get('PS_SHOP_FAX', null, null, $id_shop),
            'shop_phone' => Configuration::get('PS_SHOP_PHONE', null, null, $id_shop),
            'shop_email' => Configuration::get('PS_SHOP_EMAIL', null, null, $id_shop),
            'free_text' => Configuration::get('PS_INVOICE_FREE_TEXT', (int)Context::getContext()->language->id, null, $id_shop)
        ));

        return $this->smarty->fetch($this->getTemplate('footer'));
    }

}