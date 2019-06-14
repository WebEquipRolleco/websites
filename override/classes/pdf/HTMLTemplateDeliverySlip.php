<?php

class HTMLTemplateDeliverySlip extends HTMLTemplateDeliverySlipCore {

	public $oa;
	public $order;

	public function __construct(OA $oa, $smarty)
	{
		$this->oa = $oa;
		$this->order = $oa->getOrder();
		$this->smarty = $smarty;

		// header informations
		$this->date = Tools::displayDate($this->order->invoice_date);
		$this->title = HTMLTemplateDeliverySlip::l('Delivery').' #'.Configuration::get('PS_DELIVERY_PREFIX', Context::getContext()->language->id).sprintf('%06d', $this->order->reference);

		// footer informations
		$this->shop = new Shop((int)$this->order->id_shop);
		$this->header_tpl = "header-custom";
		$this->display_footer = false;

		$this->smarty->assign('header_mail', Configuration::getForOrder('PS_SHOP_EMAIL', $this->order));
		$this->smarty->assign('header_phone', Configuration::getForOrder('PS_SHOP_PHONE', $this->order));
	}

	/**
	 * Returns the template's HTML content
	 * @return string HTML content
	 */
	public function getContent() {

		$this->smarty->assign('oa', $this->oa);
		return $this->smarty->fetch($this->getTemplate('delivery-slip'));
	}

}