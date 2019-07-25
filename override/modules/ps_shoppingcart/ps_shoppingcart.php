<?php 

	class Ps_ShoppingcartOverride extends Ps_Shoppingcart {

		public function renderWidget($hookName, array $params) {

	        if (Configuration::isCatalogMode())
	            return;

	        $this->smarty->assign('cart', $params['cart']);
	        $this->smarty->assign('cart_url', $this->context->link->getPageLink('cart', null, $this->context->language->id, array('action' => 'show'), false, null, true));
	        $this->smarty->assign('refresh_url', $this->context->link->getModuleLink('ps_shoppingcart', 'ajax', array(), null, null, null, true));

	        return $this->fetch('module:ps_shoppingcart/ps_shoppingcart.tpl');
    	}

	}