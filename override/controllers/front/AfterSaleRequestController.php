<?php

class AfterSaleRequestControllerCore extends FrontController {

	/**
    * @see FrontController::initContent()
    **/
	public function initContent() {
		parent::initContent();

		$data['count'] = 2;
        $data['links'][] = array('url'=>'/', 'title'=>'Accueil');
        $data['links'][] = array('title'=>'Nous contacter');

        $form = Tools::getValue('contact');
        if($form) {

	        $request = new AfterSale();

	        $request->number = $form['number'];
	        $request->firstname = $form['firstname'];
	        $request->lastname = $form['lastname'];
	        $request->company = $form['company'];
	        $request->phone = $form['phone'];
	        $request->email = $form['email'];
	        $request->city = $form['city'];
	        $request->content = $form['content'];
	        $request->date_add = $form['date_add'];

	        if($this->context->customer->id)
	        	$request->id_customer = $this->context->customer->id;
	        
	        $request->save();
	        $this->context->smarty->assign('validation', true);
        }

		$this->context->smarty->assign('breadcrumb', $data);
        $this->setTemplate('customer/after-sale-request');
	}

}