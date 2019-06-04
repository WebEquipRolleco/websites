<?php

class AdminPaymentPreferencesController extends AdminPaymentPreferencesControllerCore {

	public function initContent() {

		if(Tools::isSubmit('PAYMENT_TIME_LIMIT'))
			Configuration::updateValue('PAYMENT_TIME_LIMIT', Tools::getValue('PAYMENT_TIME_LIMIT'));

		$this->context->smarty->assign('PAYMENT_TIME_LIMIT', Configuration::get('PAYMENT_TIME_LIMIT'));
		
        parent::initContent();
    }

}
