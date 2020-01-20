<?php

class AdminSkuController extends AdminController {

	/**
	* Activer Bootstrap
	**/
	public function __construct() {
        
        $this->bootstrap = true;
        parent::__construct();
    }

    /**
    * Retourne le nom des variables de configuration
    **/
    public function getConfigNames() {
    	return array("SKU_PRODUCT_PREFIX", "SKU_COMBINATION_PREFIX", "SKU_SEPARATOR");
    }
    /**
	* Récupère la configuration
	**/
	public function initContent() {

		parent::initContent();

		foreach($this->getConfigNames() as $name)
			$this->context->smarty->assign($name, configuration::get($name));
	}

	/**
	* Enregistrer la configuration
	**/
	public function postProcess() {

		foreach($this->getConfigNames() as $name)
			if(Tools::getIsset($name))
				Configuration::updateValue($name, Tools::getValue($name));
	}
	
	/**
	* Gestion AJAX
	**/
	public function displayAjax() {

		switch (Tools::getValue('action')) {
			case 'test_sku':
				die(json_encode($this->testSku(Tools::getValue('sku'))));
			break;
		}
	}

	/**
	* Test d'un SKU
	* @param string $sku
	* @return array
	**/
	private function testSku($sku) {
		return null;
	}
}