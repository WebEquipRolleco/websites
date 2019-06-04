<?php 

class CartController extends CartControllerCore {

	/**
    * @see FrontController::initContent()
    **/
    public function initContent() {

    	// Ajout option
    	if($id = Tools::getValue('add_option')) {
    		$cart_id = Context::getContext()->cart->id;
    		
    		if(!OrderOptionCart::hasAssociation($cart_id, $id)) {
    		
    			$option = new OrderOptionCart();
    			$option->id_option = $id;
    			$option->id_cart = $cart_id;
    			$option->save();
    		}
    	}

    	// Suppression option
    	if($id = Tools::getValue('remove_option')) { 
    		if($id = OrderOptionCart::hasAssociation(Context::getContext()->cart->id, $id)) {
    			$option = new OrderOptionCart($id);
    			if($option->id) $option->delete();
    		}
    	}

    	parent::initContent();
    }

}