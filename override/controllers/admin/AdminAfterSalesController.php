<?php

class AdminAfterSalesControllerCore extends AdminController {

    private $email_from;
    private $email_supplier_from;

	public function __construct() {
        
        $this->bootstrap = true;
        $this->table = 'after_sale';
        $this->className = 'AfterSale';

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        parent::__construct();

        $this->email_from = Configuration::get('PS_SHOP_EMAIL_SAV_FROM');
        $this->email_supplier_from = Configuration::get('PS_SHOP_EMAIL_SAV_SUPPLIER_FROM');

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
                'filter_key' => 'a.reference'
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
                'search' => false
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

        // Envoi message au fournisseur
        if(Tools::isSubmit('send_contact_supplier')) {
            
            $to = Tools::getValue('email_supplier');
            $id_supplier = Tools::getValue('id_supplier');
            $content = Tools::getValue('message');
            $attachments = array();

            // Enregistrement du message
            $message = new AfterSaleMessage();
            $message->id_after_sale = $sav->id;
            $message->id_employee = $this->context->employee->id;
            $message->id_supplier = $id_supplier;
            $message->message = $content;
            $message->display = false;
            $message->new = false;
            $message->date_add = date('Y-m-d H:i:s');
            $message->save();

            // Envoi du mail
            $data['{shop_name}'] = Configuration::get('PS_SHOP_NAME');
            $data['{message}'] = $message->message;

            if(Tools::getIsset('attachments'))
            foreach(Tools::getValue('attachments') as $file_name) {
                if($path = $sav->getDirectory(true).$file_name and is_file($path)) {

                    $attachment['content'] = file_get_contents($path);
                    $attachment['name'] = $file_name;
                    $attachment['mime'] = mime_content_type($path);

                    $attachments[] = $attachment;
                }
            }
            
            Mail::send(1, "sav_contact_supplier", $this->l("Demande concernant un SAV"), $data, $to, null, $this->email_supplier_from, null, $attachments);

            // Mise à jour du SAV
            $sav->hasBeenUpdated();
            $sav->save();
        }

    	// Mise à jour des produits
    	if(Tools::getIsset('new_details')) {
    		$sav->ids_detail = implode(AfterSale::DELIMITER, Tools::getValue('new_details'));
            $sav->save();
        }

    	// Changement de statut
    	if($status = Tools::getValue('new_state') and $sav->status != $status) {
    		
            $sav->status = $status;
            $sav->condition = null;

            // Notification client
            $messages = Tools::getValue('message');
            if(isset($messages[$sav->status]) and $messages[$sav->status]) {

                $data['{firstname}'] = $sav->getCustomer()->firstname;
                $data['{lastname}'] = $sav->getCustomer()->lastname;
                $data['{shop_name}'] = Configuration::get('PS_SHOP_NAME');
                $data['{message}'] = $messages[$sav->status];
                $data['{order_reference}'] = $sav->getOrder()->reference;
                $data['{reference}'] = $sav->reference;

                foreach($sav->getMails() as $email)
                    Mail::send(1, "sav_change_status", $this->l("Mise à jour de votre SAV : ".$sav->reference), $data, $email, null, $this->email_from);
            }

            $sav->hasBeenUpdated();
            $sav->save();

            // Enregistrer dans l'historique
            $sav->addHistory($sav->getStatusLabel(true), $this->context->employee->id);
    	}
    	
    	// Mise à jour des informations
    	if(Tools::getValue('id_order')) $sav->id_order = Tools::getValue('id_order');
    	if(Tools::getValue('id_customer')) $sav->id_customer = Tools::getValue('id_customer');
    	
        if(Tools::getIsset('date_add')) {
            $sav->date_add = Tools::getValue('date_add');
            $sav->save();
        }

        if(Tools::getIsset('condition')) {
            $sav->condition = Tools::getValue('condition');
            $sav->save();
        }

    	if(Tools::getIsset('email')) {
            $sav->email = Tools::getValue('email');
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

            if($message->display) {

                $data['{firstname}'] = $sav->getCustomer()->firstname;
                $data['{lastname}'] = $sav->getCustomer()->lastname;
                $data['{shop_name}'] = Configuration::get('PS_SHOP_NAME');
                $data['{message}'] = $message->message;
                $data['{order_reference}'] = $sav->getOrder()->reference;
                $data['{reference}'] = $sav->reference;

                foreach($sav->getMails() as $email)
                    Mail::send(1, "sav_message_to_customer", $this->l("Nouveau message pour votre SAV : ".$sav->reference), $data, $email, null, $this->email_from); 
            }
    	}

        // Redirection page update
        if($sav->id and Tools::getIsset('addafter_sale')) {
            
            if(!$sav->reference) {
                $sav->generateReference();
                $sav->save();
            }

            $link = new Link();
            Tools::redirect($link->getAdminLink('AdminAfterSales')."&updateafter_sale&id_after_sale=".$sav->id);
        }

    	$this->context->smarty->assign('sav', $sav);
        $this->context->smarty->assign('email_supplier_from', $this->email_supplier_from);
    	$this->setTemplate("details.tpl");
    }

    public function initToolbar() {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

}