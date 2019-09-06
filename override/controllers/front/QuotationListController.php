<?php

class QuotationListControllerCore extends FrontController {

	/**
    * @see FrontController::initContent()
    **/
    public function initContent() {
        parent::initContent();

        $data['count'] = 2;
        $data['links'][] = array('url'=>'/', 'title'=>'Accueil');

        // Téléchargement d'un devis
        if($reference = Tools::getValue('download')) {
            $pdf = new PDF(array('quotation'=>Quotation::findByReference($reference)), PDF::TEMPLATE_QUOTATION, $this->context->smarty);
            die($pdf->render());
        }

        // Ajouter un devis au panier
        if($reference = Tools::getValue('accept')) {

            $quotation = Quotation::findByReference($reference);
            if($quotation->id) {

                if(!$this->context->cart->id){
                    $this->context->cart->save();

                    $this->context->cookie->id_cart = $this->context->cart->id;
                    $this->context->cookie->write();
                }

                QuotationAssociation::addToCart($quotation->id, $this->context->cart->id);
            }

        }

        // Refuser un devis
        if($reference = Tools::getValue('refuse')) {


            $quotation = Quotation::findByReference($reference);
            if($quotation->id) {

                QuotationAssociation::removeFromCart($quotation->id);
                $quotation->status = Quotation::STATUS_REFUSED;
                $quotation->save();
            }
        }
        
        $data['links'][] = array('title'=>'Mes devis');

        $options['id_customer'] = $this->context->customer->id;
        $options['active'] = true;

        $this->context->smarty->assign('breadcrumb', $data);
        $this->context->smarty->assign('quotations', Quotation::find($options));
        $this->setTemplate('customer/quotation-list');
    
    }
}