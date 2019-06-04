<?php

class QuotationDetailControllerCore extends FrontController {

	/**
    * @see FrontController::initContent()
    **/
    public function Account() {
        parent::initContent();

        $link = new Link();

        $data['count'] = 3;
        $data['links'][] = array('url'=>'/', 'title'=>'Accueil');
        $data['links'][] = array('url'=>$link->getPageLink('QuotationList'), 'title'=>'Mes devis');
        $data['links'][] = array('title'=>$reference);

        // Ajouter un devis au panier
        $reference = Tools::getValue('accept');
        if($reference) {

            $quotation = Quotation::findByReference($reference);
            if($quotation->id) {

                foreach($quotation->getProducts() as $line)
                    QuotationAssociation::addLine($this->context->cart->id, $line->id);
            }

        }
        // Refuser un devis
        $reference = Tools::getValue('refuse');
        if($reference) {

            $quotation = Quotation::findByReference($reference);
            if($quotation->id) {

                $quotation->status = Quotation::STATUS_REFUSED;
                $quotation->save();
            }
        }

        $reference = Tools::getValue('reference');

        

        $this->context->smarty->assign('breadcrumb', $data);
        $this->context->smarty->assign('quotation', Quotation::findByReference($reference));
        $this->setTemplate('account/quotation-detail');
    }

    public function cronTask() {

        // Envoi des e-mails de rappel
        $options = array();
        $options['date_recall'] = date('Y-m-d');
        $options['states'] = array(Quotation::STATUS_WAITING);

        $quotations = Quotation::find($options);
        foreach($quotations as $quotation) {

            $tpl_vars = array();
            $employee = $quotation->getEmployee();

            foreach($quotations->getEmails() as $email)
                Mail::send(1, "quotation_recall", "Rappel de devis", $tpl_vars, $email, $employee->firstname." ".$employee->lastname, Configuration::get('PS_SHOP_EMAIL'), Configuration::get('PS_SHOP_NAME'), null, null, __DIR__."/mails/");
        }

        // Changer le statut des devis expirés
        $options = array();
        $options['expired'] = true;
        $options['states'] = array(Quotation::STATUS_WAITING);

        $quotations = Quotation::find($options);
        foreach($quotations as $quotation) {

            $quotation->status = Quotation::STATUS_OVER;
            $quotation->save();
        }
        
    }
}