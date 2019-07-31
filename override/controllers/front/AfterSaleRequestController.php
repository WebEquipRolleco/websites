<?php

class AfterSaleRequestControllerCore extends FrontController {

	/**
    * @see FrontController::initContent()
    **/
	public function initContent() {
		
		parent::initContent();
		$link = new Link();

		if($form = Tools::getValue('form')) {

			// Création du SAV
			$request = new AfterSale();
			$request->id_customer = $this->context->customer->id;
			$request->email = $form['email'];
			$request->id_order = $form['id_order'];
    		$request->ids_detail = implode(AfterSale::DELIMITER, $form['id_detail']);
    		$request->date_add = date('Y-m-d H:i:s');
    		$request->hasBeenUpdated();
    		$request->generateReference();
    		$request->save();

    		// Ajout du message initial
    		$message = new AfterSaleMessage();
    		$message->id_after_sale = $request->id;
			$message->id_customer = $this->context->customer->id;
			$message->message = $form['message'];
			$message->save();

			// Ajout des pièces jointes
			if(isset($_FILES['attachments'])) {
	        	$request->checkDirectory();

	        	for($x=0; $x<count($_FILES['attachments']['name']); $x++) {

		        	$file_name = uniqid().'.'.pathinfo($_FILES['attachments']['name'][$x], PATHINFO_EXTENSION);
		        	$path = $request->getDirectory(true).$file_name;

		        	move_uploaded_file($_FILES['attachments']['tmp_name'][$x], $path);
		        }
        	}

			// Redirection
			Tools::redirect($link->getPageLink('afterSales'));
		}

		

		$data['count'] = 3;
        $data['links'][] = array('url'=>'/', 'title'=>'Accueil');
        $data['links'][] = array('url'=>$link->getPageLink('afterSales'), 'title'=>'Mon SAV');
        $data['links'][] = array('title'=>'Demande de SAV');

		$this->context->smarty->assign('breadcrumb', $data);
		$this->context->smarty->assign('customer', $this->context->customer);
		$this->context->smarty->assign('orders', $this->context->customer->getOrders());
		$this->context->smarty->assign('id_order', Order::getIdByReference(Tools::getValue('order')));
		
        $this->setTemplate('after_sales/request');
	}

}