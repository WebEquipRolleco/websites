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

    	// Message lu
    	if($id = Tools::getValue('read')) {

    		$message = new AfterSaleMessage($id);
    		$message->new = false;
    		$message->save();
    	}
    	
    	// Nouveau message
    	if($content = Tools::getValue('new_message')) {

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