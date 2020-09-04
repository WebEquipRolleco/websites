<?php

class ContactController extends ContactControllerCore {

	public function postProcess() {

		if($form = Tools::getValue('contact')) {

			foreach(array('lastname', 'firstname', 'company', 'number', 'phone', 'email', 'city', 'message') as $name)
				$templateVars["{$name}"] = $form[$name] ?? '-';

			if(Mail::send(1, 'contact', "Contact client", $templateVars, Configuration::get('PS_SHOP_EMAIL'), Configuration::get('PS_SHOP_NAME'), Configuration::get('PS_SHOP_EMAIL'), trim($form['firstname'].' '.$form['lastname'])))
				$this->context->smarty->assign('alert', array('type'=>'success', 'message'=>"Votre message a bien été envoyé."));
			else
				$this->context->smarty->assign('alert', array('type'=>'danger', 'message'=>"Désolé, une erreur est survenue pendant l'envoi de votre message."));
		}

	}
}