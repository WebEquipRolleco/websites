<?php

class AfterSalesControllerCore extends FrontController {

	/**
    * @see FrontController::initContent()
    **/
	public function initContent() {
		parent::initContent();

		if(Tools::getIsset('sav'))
			$this->renderDetails();
		else
			$this->renderList();	
	}

	/**
	* Affiche la liste des SAV
	**/
	public function renderList() {

		$data['count'] = 2;
        $data['links'][] = array('url'=>'/', 'title'=>'Accueil');
        $data['links'][] = array('title'=>'Mon SAV');

		$this->context->smarty->assign('breadcrumb', $data);
		$this->context->smarty->assign('requests', AfterSale::findByCustomer($this->context->customer->id));

        $this->setTemplate('after_sales/list');
	}

	/**
	* Affiche le détails d'un SAV
	**/
	public function renderDetails() {

		$link = new Link();
		$sav = AfterSale::findByReference(Tools::getValue('sav'));

		$data['count'] = 3;
        $data['links'][] = array('url'=>'/', 'title'=>'Accueil');
        $data['links'][] = array('url'=>$link->getPageLink('afterSales'), 'title'=>'Mon SAV');
        $data['links'][] = array('title'=>'SAV N° '.$sav->reference);

        // Nouveau message
        if($content = Tools::getValue('new_message')) {

        	$message = new AfterSaleMessage();
        	$message->id_after_sale = $sav->id;
			$message->id_customer = $this->context->customer->id;
			$message->message = $content;
    		$message->date_add = date('Y-m-d H:i:s');
    		$message->save();
        }

        // Nouvelle image
        if(isset($_FILES['new_file'])) {
        	$sav->checkDirectory();

        	$file_name = uniqid().'.'.pathinfo($_FILES['new_file']['name'], PATHINFO_EXTENSION);
        	$path = $sav->getDirectory(true).$file_name;

        	move_uploaded_file($_FILES['new_file']['tmp_name'], $path);
        }

        // Suppression image
        if($name = Tools::getValue('remove'))
        	@unlink($sav->getDirectory(true).$name);
        
		$this->context->smarty->assign('breadcrumb', $data);
		$this->context->smarty->assign('sav', $sav);

		$this->setTemplate('after_sales/details');
	}
}