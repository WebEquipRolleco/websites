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
                'callback' => 'formatDate',
                'type' => 'date',
            ),
            'date_upd' => array(
                'title' => $this->trans('Dernière mise à jour', array(), 'Admin.Global'),
                'align' => 'text-center',
                'callback' => 'formatDate',
                'type' => 'date',
            ),
        );
    }

    public function renderStatuts($value) {

        $sav = new AfterSale();
        $sav->status = $value;

        return "<span class='label label-".$sav->getStatusClass()."'>".$sav->getStatusLabel()."</span>";
    }

    public function formatDate($value) {

        $date = DateTime::createFromFormat('Y-m-d H:i:s', $value);
        return $date->format('d/m/Y');
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
    	if($status = Tools::getValue('new_state') and $sav->status != $status) {
    		$sav->status = Tools::getValue('status');
    		$sav->hasBeenUpdated();

            // Effacer le statut personnalisé
            if(Tools::getValue('eraze')) {
                $sav->condition = null;
                $sav->save();
            }

            // Notification client
            $messages = Tools::getValue('message');
            if($isset($messages[$sav->status]) and $messages[$sav->status]) {

                $data['{firstname}'] = $sav->getCustomer()->firstname;
                $data['{lastname}'] = $sav->getCustomer()->lastname;
                $data['{shop_name}'] = Configuration::get('PS_SHOP_NAME');
                $data['{message}'] = $messages[$sav->status];
                $data['{order_reference}'] = $sav->getOrder()->reference;
                $data['{reference}'] = $sav->reference;

                foreach($sav->getMails() as $mail)
                    Mail::send(1, "sav_change_status", $this->l("Mise à jour de votre SAV : ".$sav->reference), $data, $email, null, Configuration::get('PS_SHOP_EMAIL_SAV_FROM'));
            }
    	}
    	
    	// Mise à jour des informations
    	if(Tools::getValue('id_order')) $sav->id_order = Tools::getValue('id_order');
    	if(Tools::getValue('id_customer')) $sav->id_customer = Tools::getValue('id_customer');
    	if(Tools::getValue('date_add')) $sav->date_add = Tools::getValue('date_add');
        if(Tools::getIsset('condition')) $sav->condition = Tools::getValue('condition');
    	if(Tools::getIsset('email')) $sav->email = Tools::getValue('email');
    	
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

        // Redirection page update
        if($sav->id and Tools::getIsset('addAfter_sale')) {
            
            $link = new Link();
            Tools::redirect($link->getAdminLink('AdminAfterSales')."&updateafter_sale&id_after_sale=".$this->id);
        }

    	$this->context->smarty->assign('sav', $sav);
    	$this->setTemplate("details.tpl");
    }

    public function initToolbar() {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

}