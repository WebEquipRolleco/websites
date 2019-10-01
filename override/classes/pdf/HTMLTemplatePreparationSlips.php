<?php

class HTMLTemplatePreparationSlipsCore extends HTMLTemplate {

	public $context;
	public $orders;

	public function __construct($orders, $smarty) {

		$this->orders = $orders;
		$this->context = Context::getContext();
		$this->smarty = $smarty;

		// footer informations
		$this->shop = $this->context->shop;
		$this->display_footer = false;
	}

	/**
	* Returns the template's HTML content
	* @return string HTML content
	**/
	public function getContent() {

		$this->smarty->assign('orders', $this->orders);
		$this->smarty->assign('employee', $this->context->employee);

		return $this->smarty->fetch($this->getTemplate('preparation-slips'));
	}

	/**
	* Returns the template filename when using bulk rendering
	* @return string filename
	**/
	public function getBulkFilename() {
		return 'bon_de_preparations.pdf';
	}

	/**
	* Returns the template filename
	* @return string filename
	**/
	public function getFilename() {
		return 'bon_de_preparations.pdf';
	}

}