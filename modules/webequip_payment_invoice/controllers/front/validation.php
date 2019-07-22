<?php

class Webequip_payment_invoiceValidationModuleFrontController extends ModuleFrontController {

    public function postProcess() {

        $cart = $this->context->cart;

        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
            Tools::redirect('index.php?controller=order&step=1');

        // Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'webequip_payment_invoice') {
                $authorized = true;
                break;
            }
        }

        if (!$authorized)
            die($this->trans('This payment method is not available.', array(), 'Modules.Checkpayment.Shop'));

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer))
            Tools::redirect('index.php?controller=order&step=1');

        $id_order_state = Configuration::get('DEFAULT_ID_STATE_OK');
        if(!$id_order_state)
            Tools::redirect('index.php?controller=order&step=1');

        $this->module->validateOrder($cart->id, $id_order_state, $cart->getOrderTotal(true, Cart::BOTH), $this->module->displayName);
        Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int)$cart->id.'&id_module='.(int)$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
    }
}
