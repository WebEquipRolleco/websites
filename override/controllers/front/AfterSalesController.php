<?php

class AfterSalesControllerCore extends FrontController {

	/**
    * @see FrontController::initContent()
    **/
	public function initContent() {
		parent::initContent();

		$data['count'] = 2;
        $data['links'][] = array('url'=>'/', 'title'=>'Accueil');
        $data['links'][] = array('title'=>'Mes demandes de contact');

		$this->context->smarty->assign('breadcrumb', $data);
		$this->context->smarty->assign('requests', AfterSale::findByCustomer($this->context->customer->id));

        $this->setTemplate('customer/after-sales');
	}

}