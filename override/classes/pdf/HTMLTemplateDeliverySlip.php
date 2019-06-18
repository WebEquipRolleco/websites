<?php

class HTMLTemplateDeliverySlip extends HTMLTemplateDeliverySlipCore {

	public $oa;
	public $order;

	public function __construct(OA $oa, $smarty)
	{
		$this->oa = $oa;
		$this->order = $oa->getOrder();
		$this->smarty = $smarty;

		// footer informations
		$this->shop = new Shop((int)$this->order->id_shop);
		$this->display_footer = false;

		$this->smarty->assign('header_mail', Configuration::getForOrder('PS_SHOP_EMAIL', $this->order));
		$this->smarty->assign('header_phone', Configuration::getForOrder('PS_SHOP_PHONE', $this->order));
	}

	/**
    * Returns the template's HTML header
    * @return string HTML header
    **/
    public function getHeader() {
        
        $this->assignCommonHeaderData();
        return $this->smarty->fetch($this->getTemplate('header'));
    }

	/**
	* Returns the template's HTML content
	* @return string HTML content
	**/
	public function getContent() {

		$this->smarty->assign('oa', $this->oa);
		$this->smarty->assign('order', $this->oa->getOrder());
		$this->smarty->assign('header', null);

		return $this->smarty->fetch($this->getTemplate('delivery-slip'));
	}

}