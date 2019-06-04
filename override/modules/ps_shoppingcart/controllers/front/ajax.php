<?php

class Ps_ShoppingcartAjaxModuleFrontControllerOverride extends Ps_ShoppingcartAjaxModuleFrontController {

	/**
    * @see FrontController::initContent()
    **/
    public function initContent() {

        ob_end_clean();
        header('Content-Type: application/json');

        if (Tools::getValue('action') === 'add-to-cart')
        	$data['modal'] = $this->module->renderModal($this->context->cart, null, null);
        $data['preview'] = $this->module->renderWidget(null, array('cart'=>$this->context->cart));

        die(json_encode($data));
    }

}