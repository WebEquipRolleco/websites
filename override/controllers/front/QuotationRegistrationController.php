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



            Mail::send(1, "quotation_request", "Demande de devis", $tpl_vars, "thierrydu593@gmail.com", "Web-equip", "thierrydu593@gmail.com", "hello"
                , null, null, null, null, null, null);

            $this->context->smarty->assign('validation', true);
            var_dump($tpl_vars);
            die();
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

                    if(!$this->context->cart->id) {

                        $this->context->cart->save();
                        $this->context->cookie->id_cart = $this->context->cart->id;
                        $this->context->cookie->write();
                    }

                    QuotationAssociation::addToCart($quotation->id, $this->context->cart->id);
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