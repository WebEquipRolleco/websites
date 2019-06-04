<?php

class QuotationRegistrationControllerCore extends FrontController {

	/**
     * @see FrontController::initContent()
     */
    public function initContent() {

    	parent::initContent();

        $data['count'] = 2;
        $data['links'][] = array('url'=>'/', 'title'=>'Accueil');
        $data['links'][] = array('title'=>'Demande de devis');

        $form = Tools::getValue('quotation');
        if($form) {

            foreach($form as $name => $value)
                $tpl_vars["{$name}"] = $value;

            $tpl_vars['{shop_name}'] = $this->context->shop->name;

            Mail::send(1, "request", "Demande de devis", $tpl_vars, Configuration::get('PS_SHOP_EMAIL'), "Web-equip", $form['email'], trim($form['firstname']." ".$form['lastname']));
            $this->context->smarty->assign('validation', true);
        }

        $this->context->smarty->assign('breadcrumb', $data);

        $this->setTemplate('quotation');
    }
}