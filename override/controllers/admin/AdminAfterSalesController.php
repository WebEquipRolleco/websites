<?php

class AdminAfterSalesControllerCore extends AdminController {

	public function __construct() {
        
        $this->bootstrap = true;
        $this->table = 'after_sale';
        $this->className = 'AfterSale';

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        parent::__construct();

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Notifications.Info'),
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Info'),
                'icon' => 'icon-trash'
            )
        );

        $this->fields_list = array(
            'id_after_sale' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'reference' => array(
                'title' => $this->trans('Name', array(), 'Admin.Global'),
            ),
        );
    }

    public function renderForm() {

    	$sav = new AfterSale(Tools::getValue('id_after_sale'));
    	if(!$sav->id) {

    		$sav->date_add = date('Y-m-d 00:00:00');
    		$sav->date_upd = date('Y-m-d 00:00:00');
    	}

    	// Mise à jour des produits
    	if(Tools::getIsset('new_details'))
    		$sav->ids_detail = implode(AfterSale::DELIMITER, Tools::getValue('new_details'));

    	// Changement de statut
    	if($status = Tools::getValue('status') and $sav->status != $status) {
    		$sav->status = Tools::getValue('status');
    		$sav->date_upd = date('Y-m-d 00:00:00');
    	}
    	
    	// Mise à jour des informations
    	if(Tools::getValue('id_order')) $sav->id_order = Tools::getValue('id_order');
    	if(Tools::getValue('id_customer')) $sav->id_customer = Tools::getValue('id_customer');
    	if(Tools::getValue('date_add')) $sav->date_add = Tools::getValue('date_add');
    	if(Tools::getIsset('condition')) $sav->condition = Tools::getValue('condition');
    	
    	if(Tools::getIsset('update_configuration')) {
    		if(!$sav->reference) $sav->generateReference();
    		$sav->save();
    	}

    	// Message lu
    	if($id = Tools::getValue('read')) {

    		$message = new AfterSaleMessage($id);
    		$message->new = false;
    		$message->save();
    	}
    	
    	// Nouveau message
    	if($content = Tools::getValue('new_message')) {
    		
    		if(!$sav->reference) $sav->generateReference();
    		$sav->save();

    		$message = new AfterSaleMessage();
    		$message->id_after_sale = $sav->id;
			$message->id_employee = $this->context->employee->id;
			$message->message = $content;
			$message->display = Tools::getValue('display');
		    $message->date_add = date('Y-m-d H:i:s');
		    $message->save();
    	}

    	$this->context->smarty->assign('sav', $sav);
    	$this->setTemplate("details.tpl");
    }

}