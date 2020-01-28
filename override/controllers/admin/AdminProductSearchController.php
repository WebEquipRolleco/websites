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
    	$this->context->smarty->assign('products', $this->getProducts($search));
    	
    	$tpl = $this->context->smarty->createTemplate(_PS_ROOT_DIR_."/override/controllers/admin/templates/product_search/list.tpl");
        die($tpl->fetch());
    }

    private function getProducts($search) {
    	return Db::getInstance()->executeS("SELECT DISTINCT(p.id_product), ps.reference, pp.product_supplier_reference, pl.name, pas.reference AS combination_reference FROM ps_product p LEFT JOIN ps_product_lang pl ON (p.id_product = pl.id_product AND pl.id_lang = 1) LEFT JOIN ps_product_supplier pp ON (p.id_product = pp.id_product AND pp.product_supplier_reference LIKE '%$search%') LEFT JOIN ps_product_shop ps ON (p.id_product = ps.id_product AND ps.reference LIKE '%$search%') LEFT JOIN ps_product_attribute_shop pas ON (p.id_product = pas.id_product AND pas.reference LIKE '%$search%') WHERE (ps.reference IS NOT NULL or pp.product_supplier_reference IS NOT NULL OR pas.reference IS NOT NULL)");
    }
}