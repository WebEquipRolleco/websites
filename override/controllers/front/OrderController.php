<?php 

use PrestaShop\PrestaShop\Core\Foundation\Templating\RenderableProxy;

class OrderController extends OrderControllerCore {

	public function initContent() {

        if(Configuration::isCatalogMode())
            Tools::redirect('index.php');

        $this->restorePersistedData($this->checkoutProcess);
        $this->checkoutProcess->handleRequest(Tools::getAllValues());

        // if there is no product in current cart, redirect to cart page
        if($this->context->cart->nbProducts() <= 0) {
        
            $cartLink = $this->context->link->getPageLink('cart');
            Tools::redirect($cartLink);
        }

        // if there is an issue with product quantities, redirect to cart page
        if(is_array($this->context->cart->checkQuantities(true))) {
            
            $cartLink = $this->context->link->getPageLink('cart', null, null, array('action' => 'show'));
            Tools::redirect($cartLink);
        }

        $this->checkoutProcess->setNextStepReachable()->markCurrentStep()->invalidateAllStepsAfterCurrent();
        $this->saveDataToPersist($this->checkoutProcess);

        if(!$this->checkoutProcess->hasErrors())
            if($_SERVER['REQUEST_METHOD'] !== 'GET' && !$this->ajax)
                return $this->redirectWithNotifications($this->checkoutProcess->getCheckoutSession()->getCheckoutURL());

        $data['checkout_process'] = new RenderableProxy($this->checkoutProcess);
        $data['cart'] = $this->cart_presenter->present($this->context->cart);

        $this->context->smarty->assign($data);

        FrontController::initContent();
        $this->setTemplate('checkout/checkout');
    }

}