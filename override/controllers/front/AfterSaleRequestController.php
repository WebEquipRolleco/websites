<?php

class AfterSaleRequestControllerCore extends FrontController {

	private $page_link; 

	/**
    * @see FrontController::initContent()
    **/
	public function initContent() {
		
		parent::initContent();
		$this->page_link = new Link();
		
		if($form = Tools::getValue('form')) {
			$this->createAfterSale($form);
		}

		$data['count'] = 3;
        $data['links'][] = array('url'=>'/', 'title'=>'Accueil');
        $data['links'][] = array('url'=>$this->page_link->getPageLink('afterSales'), 'title'=>'Mon SAV');
        $data['links'][] = array('title'=>'Demande de SAV');

		$this->context->smarty->assign('breadcrumb', $data);
		$this->context->smarty->assign('customer', $this->context->customer);
		$this->context->smarty->assign('orders', $this->context->customer->getOrders());
		$this->context->smarty->assign('id_order', Order::getIdByReference(Tools::getValue('order')));
		
        $this->setTemplate('after_sales/request');
	}

	/**
	* Crée une demande de SAV
	* @param array $form
	**/
	private function createAfterSale($form) {

		// Création du SAV
		$request = new AfterSale();
		$request->id_customer = $this->context->customer->id;
		$request->email = $form['email'];
		$request->id_order = $form['id_order'];
    	$request->ids_detail = implode(AfterSale::DELIMITER, $form['id_detail']);
    	$request->notice_on_delivery = isset($form['notice_on_delivery']);
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

        // Notification client
        $from_name = Configuration::get('PS_SHOP_NAME');
        $from_mail = Configuration::get('PS_SHOP_EMAIL_SAV_FROM');
        $notification_mail = Configuration::get('PS_SHOP_EMAIL_SAV_TO');

        $data['{firstname}'] = $request->getCustomer()->firstname;
        $data['{lastname}'] = $request->getCustomer()->lastname;
        $data['{order_reference}'] = $request->getOrder()->reference;
        $data['{reference}'] = $request->reference;
        $data['{shop_name}'] = $from_name;
        $data['{message}'] = $message->message;

        foreach($request->getMails() as $email)
        	Mail::send(1, "sav_confirmation", $this->trans("Votre demande de SAV", array(), 'Shop.Notifications.Success'), $data, $email, null, $from_mail);

        // Notification équipe
        Mail::send(1, "sav_notification", $this->trans("Nouvelle demande de SAV", array(), 'Shop.Notifications.Success'), $data, $notification_mail);

		// Redirection
		Tools::redirect($this->page_link->getPageLink('afterSales'));
	}

}