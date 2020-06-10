<?php

class CheckoutDeliveryStep extends CheckoutDeliveryStepCore {

	public function handleRequest(array $requestParams = array()) {

        if (isset($requestParams['delivery_option'])) {
            $this->getCheckoutSession()->setDeliveryOption(
                $requestParams['delivery_option']
            );
            $this->getCheckoutSession()->setRecyclable(
                isset($requestParams['recyclable']) ? $requestParams['recyclable'] : false
            );
            $this->getCheckoutSession()->setGift(
                isset($requestParams['gift']) ? $requestParams['gift'] : false,
                (isset($requestParams['gift']) && isset($requestParams['gift_message'])) ? $requestParams['gift_message'] : ''
            );
        }

        if(isset($requestParams['internal_reference'])) {
            $this->getCheckoutSession()->getCart()->internal_reference = $requestParams['internal_reference'];
            $this->getCheckoutSession()->getCart()->save();
        }

        if(isset($requestParams['delivery_message']))
            $this->getCheckoutSession()->setMessage($requestParams['delivery_message']);

        if ($this->step_is_reachable && isset($requestParams['confirmDeliveryOption'])) {
            // we're done if
            // - the step was reached (= all previous steps complete)
            // - user has clicked on "continue"
            // - there are delivery options
            // - the is a selected delivery option
            // - the module associated to the delivery option confirms
            $deliveryOptions = $this->getCheckoutSession()->getDeliveryOptions();
            $this->step_is_complete =
                !empty($deliveryOptions) && $this->getCheckoutSession()->getSelectedDeliveryOption() && $this->isModuleComplete($requestParams)
            ;
        }

        $this->setTitle($this->getTranslator()->trans('Commentaire livraison', array(), 'Shop.Theme.Checkout'));

        Hook::exec('actionCarrierProcess', array('cart' => $this->getCheckoutSession()->getCart()));
    }

    public function render(array $extraParams = array()) {

        return $this->renderTemplate(
            $this->getTemplate(),
            $extraParams,
            array(
                'hookDisplayBeforeCarrier' => Hook::exec('displayBeforeCarrier', array('cart' => $this->getCheckoutSession()->getCart())),
                'hookDisplayAfterCarrier' => Hook::exec('displayAfterCarrier', array('cart' => $this->getCheckoutSession()->getCart())),
                'id_address' => $this->getCheckoutSession()->getIdAddressDelivery(),
                'delivery_options' => $this->getCheckoutSession()->getDeliveryOptions(),
                'delivery_option' => $this->getCheckoutSession()->getSelectedDeliveryOption(),
                'recyclable' => $this->getCheckoutSession()->isRecyclable(),
                'recyclablePackAllowed' => $this->isRecyclablePackAllowed(),
                'delivery_message' => $this->getCheckoutSession()->getMessage(),
                'internal_reference' => $this->getCheckoutSession()->getCart()->internal_reference,
                'gift' => array(
                    'allowed' => $this->isGiftAllowed(),
                    'isGift' => $this->getCheckoutSession()->getGift()['isGift'],
                    'label' => $this->getTranslator()->trans(
                        'I would like my order to be gift wrapped %cost%',
                        array('%cost%' => $this->getGiftCostForLabel()),
                        'Shop.Theme.Checkout'
                    ),
                    'message' => $this->getCheckoutSession()->getGift()['message'],
                ),
            )
        );
    }

}