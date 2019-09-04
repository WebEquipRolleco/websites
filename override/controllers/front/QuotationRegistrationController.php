<?php

class QuotationRegistrationControllerCore extends FrontController {

	/**
    ** @see FrontController::initContent()
    **/
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

    /**
    * Gère le lien d'ajout de devis au panier
    **/
    public function postProcess() {

        if($reference = Tools::getValue('accept') and $key = Tools::getValue('key')) {

            $quotation = Quotation::findByReference($reference);
            if($quotation->id and $quotation->secure_key == $key) {
                if($quotation->isValid()) {

                    foreach($quotation->getProducts() as $line)
                        QuotationAssociation::addLine($this->context->cart->id, $line->id);

                    Tools::redirect($this->context->link->getPageLink('cart', null, $this->context->language->id, ['action' => 'show']));
                }
                else
                    die("Ce devis n'est plus valide");
            }
            else
                die("Devis inconnu ou clé de sécurité incorrecte");
        }

        parent::postProcess();
    }

}