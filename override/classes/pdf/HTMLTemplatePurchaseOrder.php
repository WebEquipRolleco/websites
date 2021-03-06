<?php

class HTMLTemplatePurchaseOrderCore extends HTMLTemplate
{
	public $oa;
	public $order;

	public function __construct(OA $oa, $smarty) {
		
		$this->oa = $oa;
		$this->order = $oa->getOrder();
		$this->smarty = $smarty;

		// footer informations
		$this->display_footer = false;

		$this->smarty->assign('header_mail', Configuration::getForOrder('PS_TEAM_PHONE', $this->order));
		$this->smarty->assign('header_phone', Configuration::getForOrder('PS_TEAM_EMAIL', $this->order));
	}

	/**
    * Returns the template's HTML header
    * @return string HTML header
    **/
    public function getHeader() {
        
        $this->assignCommonHeaderData();
        return $this->smarty->fetch($this->getTemplate('header.purchase-order'));
    }

	/**
	* Returns the template's HTML content
	* @return string HTML content
	**/
	public function getContent() {

		$this->smarty->assign('oa', $this->oa);
		$this->smarty->assign('order', $this->order);
		return $this->smarty->fetch($this->getTemplate('purchase-order'));
	}

	/**
	* Returns the template filename when using bulk rendering
	* @return string filename
	**/
	public function getBulkFilename() {
		return 'bon_de_commande.pdf';
	}

	/**
	* Returns the template filename
	* @return string filename
	**/
	public function getFilename() {
		return 'BC '.$this->order->reference.' '.$this->oa->getSupplier()->name.".pdf";
	}
}

