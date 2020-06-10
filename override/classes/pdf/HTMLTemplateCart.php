<?php

class HTMLTemplateCartCore extends HTMLTemplate {

	public $cart;

	public function __construct(Cart $cart, $smarty) {

		$this->cart = $cart;
		$this->smarty = $smarty;

		$this->smarty->assign('header_mail', Configuration::get('PS_TEAM_PHONE'));
		$this->smarty->assign('header_phone', Configuration::get('PS_TEAM_EMAIL'));
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
    * Returns the template's HTML footer
    * @return string HTML footer
    **/
    public function getFooter() {

    	$this->smarty->assign('shop', $this->cart->getShop());

        return $this->smarty->fetch($this->getTemplate('footer'));
    }

	/**
	* Returns the template's HTML content
	* @return string HTML content
	**/
	public function getContent() {

		$this->smarty->assign('cart', $this->cart);
		$this->smarty->assign('style_tab', $this->smarty->fetch($this->getTemplate('style-tab')));
		
		return $this->smarty->fetch($this->getTemplate('cart'));
	}

	/**
	* Returns the template filename when using bulk rendering
	* @return string filename
	**/
	public function getBulkFilename() {
		return 'mon-panier'.iconv('UTF-8', 'ASCII//TRANSLIT', $this->cart->getShop()->name).'.pdf';
	}

	/**
	* Returns the template filename
	* @return string filename
	**/
	public function getFilename() {
		return 'mon-panier-'.iconv('UTF-8', 'ASCII//TRANSLIT', $this->cart->getShop()->name).'.pdf';
	}

}