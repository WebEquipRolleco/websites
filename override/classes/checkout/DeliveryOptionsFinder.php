<?php

class DeliveryOptionsFinder extends DeliveryOptionsFinderCore {

	public function __construct($context, $translator, $objectPresenter, $priceFormatter) {

        $this->context = $context;
        $this->objectPresenter = $objectPresenter;
        $this->translator = $translator;
        $this->priceFormatter = $priceFormatter;
    }

    private function isFreeShipping($cart, array $carrier) {
        
        $free_shipping = false;

        if ($carrier['is_free']) {
            $free_shipping = true;
        } else {
            foreach ($cart->getCartRules() as $rule) {
                if ($rule['free_shipping'] && !$rule['carrier_restriction']) {
                    $free_shipping = true;
                    break;
                }
            }
        }

        return $free_shipping;
    }

	public function getSelectedDeliveryOption() {

		$current = current($this->context->cart->getDeliveryOption(null, false, false));
		if($current) return $current;

        Foreach(Carrier::getCarriers(1, true) as $carrier)
            return $carrier['id_carrier'];
    }

    public function getDeliveryOptions() {

        $delivery_option_list = $this->context->cart->getDeliveryOptionList();
        $include_taxes = !Product::getTaxCalculationMethod((int) $this->context->cart->id_customer) && (int) Configuration::get('PS_TAX');
        $display_taxes_label = (Configuration::get('PS_TAX') && !Configuration::get('AEUC_LABEL_TAX_INC_EXC'));

        $carriers_available = array();

        if (isset($delivery_option_list[$this->context->cart->id_address_delivery])) {
            foreach ($delivery_option_list[$this->context->cart->id_address_delivery] as $id_carriers_list => $carriers_list) {
                foreach ($carriers_list as $carriers) {
                    if (is_array($carriers)) {
                        foreach ($carriers as $carrier) {
                            $carrier = array_merge($carrier, $this->objectPresenter->present($carrier['instance']));
                            $delay = $carrier['delay'][$this->context->language->id];
                            unset($carrier['instance'], $carrier['delay']);
                            $carrier['delay'] = $delay;
                            if ($this->isFreeShipping($this->context->cart, $carriers_list)) {
                                $carrier['price'] = $this->translator->trans(
                                    'Free', array(), 'Shop.Theme.Checkout'
                                );
                            } else {
                                if ($include_taxes) {
                                    $carrier['price'] = $this->priceFormatter->format($carriers_list['total_price_with_tax']);
                                    if ($display_taxes_label) {
                                        $carrier['price'] = $this->translator->trans(
                                            '%price% tax incl.',
                                            array('%price%' => $carrier['price']),
                                            'Shop.Theme.Checkout'
                                        );
                                    }
                                } else {
                                    $carrier['price'] = $this->priceFormatter->format($carriers_list['total_price_without_tax']);
                                    if ($display_taxes_label) {
                                        $carrier['price'] = $this->translator->trans(
                                            '%price% tax excl.',
                                            array('%price%' => $carrier['price']),
                                            'Shop.Theme.Checkout'
                                        );
                                    }
                                }
                            }

                            if (count($carriers) > 1) {
                                $carrier['label'] = $carrier['price'];
                            } else {
                                $carrier['label'] = $carrier['name'].' - '.$carrier['delay'].' - '.$carrier['price'];
                            }

                            // If carrier related to a module, check for additionnal data to display
                            $carrier['extraContent'] = '';
                            if ($carrier['is_module']) {
                                if ($moduleId = Module::getModuleIdByName($carrier['external_module_name'])) {
                                    $carrier['extraContent'] = Hook::exec('displayCarrierExtraContent', array('carrier' => $carrier), $moduleId);
                                }
                            }

                            $carriers_available[$id_carriers_list] = $carrier;
                        }
                    }
                }
            }
        }

        if(empty($carriers_available)) {
            $carriers = Carrier::getCarriers(1, true);
            foreach($carriers as $carrier)
                $carriers_available[$carrier['id_carrier']] = array('id'=>$carrier['id_carrier'], 'logo'=>null, 'name'=>$carrier['name'], 'delay'=>$carrier['delay'], 'price'=>0, 'extraContent'=>null, 'is_module'=>false);
        }
        return $carriers_available;
    }

}