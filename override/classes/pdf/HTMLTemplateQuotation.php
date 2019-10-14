<?php

class HTMLTemplateQuotationCore extends HTMLTemplate {

	public $quotation;

	public function __construct(Quotation $quotation, $smarty) {

		$this->quotation = $quotation;
		$this->smarty = $smarty;

		// footer informations
		$this->display_footer = false;

		$this->smarty->assign('header_mail', Configuration::get('PS_TEAM_PHONE'));
		$this->smarty->assign('header_phone', Configuration::get('PS_TEAM_EMAIL'));
	}

	/**
    * Returns the template's HTML header
    * @return string HTML header
    **/
    public function getHeader() {

        $this->assignCommonHeaderData();
        $this->smarty->assign('quotation', $this->quotation);

        return $this->smarty->fetch($this->getTemplate('header-quotation'));
    }

	/**
	 * Returns the template's HTML content
	 * @return string HTML content
	 */
	public function getContent() {

		$this->smarty->assign('quotation', $this->quotation);
		$this->smarty->assign('style_tab', $this->smarty->fetch($this->getTemplate('style-tab')));
		
		return $this->smarty->fetch($this->getTemplate('quotation'));
	}

	/**
	 * Returns the template filename when using bulk rendering
	 * @return string filename
	 */
	public function getBulkFilename() {
		return $this->quotation->reference.'.pdf';
	}

	/**
	 * Returns the template filename
	 * @return string filename
	 */
	public function getFilename() {
		return $this->quotation->reference.'.pdf';
	}

}