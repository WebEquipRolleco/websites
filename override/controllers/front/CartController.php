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

        // Convertion Rollcash
        if(Tools::getIsset('use_rollcash')) {
            $customer = Context::getContext()->cart->getCustomer();

            $rule = new CartRule();
            $rule->name[1] = "Rollcash";
            $rule->id_customer = $customer->id;
            $rule->partial_use = 0;
            $rule->code = uniqid();
            $rule->reduction_tax = false;
            $rule->reduction_amount = $customer->rollcash;
            $rule->highlight = true;
            $rule->date_from = date('Y-m-d');
            $rule->date_to = (date('Y')+1).date('-m-d');
            $rule->save();

            $customer->rollcash = 0;
            $customer->save();
        }

    	parent::initContent();
    }

}