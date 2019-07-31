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

        $this->_select = "a.*, o.reference AS order_reference, c.reference AS edeal, c.company, CONCAT(c.firstname, ' ', c.lastname) AS customer, c.email";
        $this->_join = ' LEFT JOIN '._DB_PREFIX_.'orders o ON (a.id_order = o.id_order)';
        $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'customer c ON (a.id_customer = c.id_customer)';

        $this->fields_list = array(
            'id_after_sale' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'reference' => array(
                'title' => $this->trans('Référence', array(), 'Admin.Global'),
            ),
            'order_reference' => array(
                'title' => $this->trans('Numéro de commande', array(), 'Admin.Global'),
                'align' => 'text-center',
            ),
            'edeal' => array(
                'title' => $this->trans('Numéro E-deal', array(), 'Admin.Global'),
                'align' => 'text-center',
            ),
            'company' => array(
                'title' => $this->trans('Société', array(), 'Admin.Global'),
                'align' => 'text-center',
            ),
            'customer' => array(
                'title' => $this->trans('Client', array(), 'Admin.Global'),
                'align' => 'text-center',
            ),
            'email' => array(
                'title' => $this->trans('E-mail', array(), 'Admin.Global'),
                'align' => 'text-center',
            ),
            'status' => array(
                'title' => $this->trans('Statut', array(), 'Admin.Global'),
                'align' => 'text-center',
                'callback' => 'renderStatuts',
            ),
            'date_add' => array(
                'title' => $this->trans('Création', array(), 'Admin.Global'),
                'align' => 'text-center',
            ),
        );
    }

    public function renderStatuts($value) {

        $sav = new AfterSale();
        $sav->status = $value;

        return "<span class='label label-".$sav->getStatusClass()."'>".$sav->getStatusLabel()."</span>";
    }

    public function renderForm() {

    	$sav = new AfterSale(Tools::getValue('id_after_sale'));
    	if(!$sav->id) {

    		$sav->date_add = date('Y-m-d H:i:s');
    		$sav->hasBeenUpdated();
    	}

    	// Mise à jour des produits
    	if(Tools::getIsset('new_details'))
    		$sav->ids_detail = implode(AfterSale::DELIMITER, Tools::getValue('new_details'));

    	// Changement de statut
    	if($status = Tools::getValue('status') and $sav->status != $status) {
    		$sav->status = Tools::getValue('status');
    		$sav->hasBeenUpdated();
    	}
    	
    	// Mise à jour des informations
    	if(Tools::getValue('id_order')) $sav->id_order = Tools::getValue('id_order');
    	if(Tools::getValue('id_customer')) $sav->id_customer = Tools::getValue('id_customer');
    	if(Tools::getValue('date_add')) $sav->date_add = Tools::getValue('date_add');
    	if(Tools::getIsset('condition')) $sav->condition = Tools::getValue('condition');
    	
    	if(Tools::getIsset('update_configuration')) {
    		if(!$sav->reference) $sav->generateReference();
            $sav->hasBeenUpdated();
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
            $sav->hasBeenUpdated();
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

    public function initToolbar() {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

}