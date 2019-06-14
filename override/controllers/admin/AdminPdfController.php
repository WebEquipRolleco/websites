<?php

class AdminPdfController extends AdminPdfControllerCore {

	/**
	* Génération du bon de préparation
	**/
	public function processGeneratePreparationSlipPDF() {

        $order = new Order(Tools::getValue('id_order'));
        if (!Validate::isLoadedObject($order))
			die(Tools::displayError('The order cannot be found within your database.'));

        $this->generatePDF($order, PDF::TEMPLATE_PREPARATION_SLIP);
    }

    /**
    * Génération du bon de commande
    **/
    public function processGeneratePurchaseOrderPDF() {

    	$oa = new OA(Tools::getValue('id_oa'));
    	if (!Validate::isLoadedObject($oa))
			die(Tools::displayError('The OA cannot be found within your database.'));

    	$this->generatePDF($oa, PDF::TEMPLATE_PURCHASE_ORDER);
    }

    /**
    * Génération du bon de livraison
    **/
    public function processGenerateDeliverySlipPDF() {

        $oa = new OA(Tools::getValue('id_oa'));
        if (!Validate::isLoadedObject($oa))
			die(Tools::displayError('The OA cannot be found within your database.'));

    	$this->generatePDF($oa, PDF::TEMPLATE_DELIVERY_SLIP);
    }

}