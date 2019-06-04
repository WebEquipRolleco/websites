<?php

if (!defined('_PS_VERSION_'))
    exit;

class Webequip_Categories extends Module {

	public function __construct() {
        $this->name = 'webequip_categories';
        $this->tab = 'front_office_features';
        $this->version = '2.0.4';
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
        $this->author = 'Web-equip';
        $this->bootstrap = true;

        $this->displayName = "Web-equip Catégories";
        $this->description = "Gestion page catégories (catégories enfants, navigation latérale)";

        parent::__construct();
    }

    public function install() {
        return (parent::install() && $this->registerHook('displayProductListBefore') && $this->registerHook('displayLeftColumn'));
    }

    public function hookDisplayProductListBefore($params) {

    	if(Tools::getIsset('id_category')) {

    		$categories = array();
    		foreach(Category::getChildren(Tools::getValue('id_category'), $this->context->language->id) as $child) {
    			$categories[] = new Category($child['id_category'], $this->context->language->id);
    		}

    		$this->context->smarty->assign('id_lang', $this->context->language->id);
    		$this->context->smarty->assign('categories', $categories);
    		return $this->display(__FILE__, 'content.tpl');
    	}
    }

    public function hookDisplayLeftColumn($params) {

    	//$top = Category::getTopCategory($this->context->language->id);
    	if(Tools::getIsset('id_category')) {

    		$id_lang = $this->context->language->id;
    		$current = new Category(Tools::getValue('id_category'), $this->context->language->id);

    		$display = array();
    		foreach($current->getParentsCategories($this->context->language->id) as $row) {
    			$display[] = $row['id_category'];
    		}

    		$root = Category::getRootCategory($id_lang);
    		foreach(Category::getChildren($root->id, $this->context->language->id) as $category) {
    			$tree = $this->generateTree($category, 1, 27, $display, $current->id);
    			if($tree)
    				$categories[] = $tree;	
    		}

    		

    		$this->context->smarty->assign('categories', $categories);
    		return $this->display(__FILE__, 'nav.tpl');
    	}
    }

    private function generateTree($category, $level, $truncate, $parents, $id_current) {

    	$parent = in_array($category['id_category'], $parents);
    	if(!$parent and $level == 2)
    		return false;

    	$entity = new Category($category['id_category'], $this->context->language->id);

		$data = $category;
		$data['level'] = $level;
		$data['truncate'] = $truncate;
		$data['current'] = ($entity->id == $id_current);
		$data['link'] = $entity->getLink();
		$data['children'] = array();

		if(!$parent)
			return $data;

		foreach(Category::getChildren($category['id_category'], $this->context->language->id) as $child){
			$tree = $this->generateTree($child, $level+1, $truncate-3, $parents, $id_current);
			if($tree)
				$data['children'][] = $tree;
		}

		return $data;
	}

}

