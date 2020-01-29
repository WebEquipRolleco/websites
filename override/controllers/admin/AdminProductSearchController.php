<?php 

class AdminProductSearchController extends AdminController {

	/**
	* Activer Bootstrap
	**/
	public function __construct() {
        
        $this->bootstrap = true;
        parent::__construct();
    }

    /**
    * Gestion AJAX
    **/
    public function displayAjax() {
    	$search = Tools::getValue('reference');

    	$this->context->smarty->assign('search', $search);
    	$this->context->smarty->assign('products', Product::searchByReference($search));
    	
    	$tpl = $this->context->smarty->createTemplate(_PS_ROOT_DIR_."/override/controllers/admin/templates/product_search/list.tpl");
        die($tpl->fetch());
    }

}