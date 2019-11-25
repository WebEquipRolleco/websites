<?php

class CustomerPersister extends CustomerPersisterCore {


	/**
	* OVERRIDE : forcer la boutique du mail (mauvais mail reçu pendant les tests)
	**/
	private function sendConfirmationMail(Customer $customer) {

        if ($customer->is_guest || !Configuration::get('PS_CUSTOMER_CREATION_EMAIL'))
            return true;

        $data['{firstname}'] = $customer->firstname;
        $data['{lastname}'] = $customer->lastname;
        $data['{email}'] = $customer->email;

        return Mail::send(
            $this->context->language->id,
            'account',
            $this->translator->trans('Welcome!', array(), 'Emails.Subject'),
            $data,
            $customer->email,
            $customer->firstname.' '.$customer->lastname,
            Configuration::get('PS_SHOP_EMAIL', 1, null, $this->context->shop->id),
            Configuration::get('PS_SHOP_NAME', 1, null, $this->context->shop->id),
            null,
            null,
            _PS_MAIL_DIR_,
            false,
            $this->context->shop->id
        );
    }

}