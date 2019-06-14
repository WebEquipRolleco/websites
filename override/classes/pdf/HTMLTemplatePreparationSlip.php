<?php

class HTMLTemplatePreparationSlipCore extends HTMLTemplate {

	public $context;
	public $order;

	public function __construct(Order $order, $smarty) {

		$this->order = $order;
		$this->context = Context::getContext();
		$this->smarty = $smarty;

		// footer informations
		$this->shop = new Shop((int)$this->order->id_shop);
		$this->display_footer = false;
	}

	/**
	* Returns the template's HTML content
	* @return string HTML content
	**/
	public function getContent() {

		$this->smarty->assign('order', $this->order);
		$this->smarty->assign('employee', $this->context->employee);

		return $this->smarty->fetch($this->getTemplate('preparation-slip'));
	}

	/**
	* Returns the template filename when using bulk rendering
	* @return string filename
	**/
	public function getBulkFilename() {
		return 'bon_de_preparation.pdf';
	}

	/**
	* Returns the template filename
	* @return string filename
	**/
	public function getFilename() {
		return Configuration::get('PS_DELIVERY_PREFIX', Context::getContext()->language->id, null, $this->order->id_shop).sprintf('%06d', $this->order->reference).'.pdf';
	}

}