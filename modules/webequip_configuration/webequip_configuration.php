<?php

if (!defined('_PS_VERSION_'))
    exit;

class Webequip_Configuration extends Module {

	public function __construct() {
        $this->name = 'webequip_configuration';
        $this->tab = 'front_office_features';
        $this->version = '2.0.4';
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
        $this->author = 'Web-equip';
        $this->bootstrap = true;

        $this->displayName = "Web-equip Configuration";
        $this->description = "Configuration personnalisée des boutiques";

        parent::__construct();
    }

    public function install() {

        $check = parent::install();

        if(!isTabInstalled("WEBEQUIP"))
            $check .= $this->installTab("Web-équip", "WEBEQUIP", false, 0);

        if(!isTabInstalled("AdminContactInformation"))
            $check .= $this->installTab('Coordonnées', 'AdminContactInformation', "WEBEQUIP", "beenhere");
            
        if(!isTabInstalled("AdminObjectives"))
            $check .= $this->installTab("Objectifs", "AdminObjectives", 'WEBEQUIP', 'equalizer');

        if(!isTabInstalled("AdminResults"))
            $check .= $this->installTab("Résultats", "AdminResults", 'WEBEQUIP', 'grid_on');

        if(!isTabInstalled("AdminData"))
            $check .= $this->installTab('Imports / exports', 'AdminData', "WEBEQUIP", 'transform');

        if(!isTabInstalled("AdminQuotations"))
            $check .= $this->installTab("Devis", "AdminQuotations", "SELL", 'list');

        if(!isTabInstalled("AdminNewsletter"))
            $check .= $this->installTab("Newsletter", "AdminNewsletter", "AdminParentCustomer");

        if(!isTabInstalled("AdminAccountTypes"))
            $check .= $this->installTab("Types", "AdminAccountTypes", "AdminParentCustomer");

        if(!isTabInstalled("AdminCustomerStates"))
            $check .= $this->installTab("Status", "AdminCustomerStates", "AdminParentCustomer");

        if(!isTabInstalled("AdminOrderOptions"))
            $check .= $this->installTab('Options de commande', "AdminOrderOptions", "AdminCatalog");

        if(!isTabInstalled("AdminIconography"))
            $check .= $this->installTab('Iconographie', "AdminIconography", "AdminCatalog");

        if(!isTabInstalled("AdminOrderStateRules"))
            $check .= $this->installTab("Régles de redirection", 'AdminOrderStateRules', "AdminParentOrders");
        
        if(!isTabInstalled("AdminDocuments"))
            $check .= $this->installTab("Documents", "AdminDocuments", 'WEBEQUIP', 'file');

        return $check;
    }

    /**
    * Configuration du module
    **/
    public function getContent() {

        // Installation manuelle des menus
        switch (Tools::getValue('action')) {
            
            case 'WEBEQUIP':
                $this->installTab("Web-équip", "WEBEQUIP");
            break;

            case 'CONTACTS':
                $this->installTab('Coordonnées', 'AdminContactInformation', "WEBEQUIP", "beenhere");
            break;

            case 'OBJECTIVES':
                $this->installTab("Objectifs", "AdminObjectives", 'WEBEQUIP', 'equalizer');
            break;

            case 'RESULTS':
                $this->installTab("Résultats", "AdminResults", 'WEBEQUIP', 'grid_on');
            break;

            case 'DATA':
                $this->installTab('Imports / exports', 'AdminData', "WEBEQUIP", 'transform');
            break;

            case 'STATE_RULES':
                $this->installTab("Régles de redirection", 'AdminOrderStateRules', "AdminParentOrders");
            break;

            case 'QUOTATIONS':
                $this->installTab("Devis", "AdminQuotations", "SELL", 'list');
            break;

            case 'NEWSLETTER':
                $this->installTab("Newsletter", "AdminNewsletter", "AdminParentCustomer");
            break;

            case 'ACCOUNT_TYPES':
                $this->installTab('Types', 'AdminCustomerTypes', 'AdminParentCustomer');
            break;

            case 'CUSTOMER_STATES':
                $this->installTab("Status", "AdminCustomerStates", "AdminParentCustomer");
            break;

            case 'ORDER_OPTIONS':
                $this->installTab('Options de commande', "AdminOrderOptions", "AdminCatalog");
            break;

            case 'ICONOGRAPHY':
                $this->installTab('Iconographie', "AdminIconography", "AdminCatalog");
            break;

            case 'DOCUMENTS':
                $this->installTab("Documents", "AdminDocuments", 'WEBEQUIP', 'description');
            break;
        }

        // Suppression des menus
        if($id = Tools::getValue('remove_tab')) {
            $tab = new Tab($id);
            if($tab->id) $tab->delete();
        }

        // Arborescence des menus
        $tabs[0] = array('name'=>'VENDRE');
        $tabs[0]['children'][] = array('name'=>"Régles de redirection", 'id'=>$this->isTabInstalled("AdminOrderStateRules"), 'action'=>'STATE_RULES');
        $tabs[0]['children'][] = array('name'=>"Devis", 'id'=>$this->isTabInstalled("AdminQuotations"), 'action'=>'QUOTATIONS');

        $tabs[1] = array('name'=>"WEB-EQUIP", 'id'=>$this->isTabInstalled('WEBEQUIP'), 'action'=>'WEBEQUIP');
        $tabs[1]['children'][] = array('name'=>'Coordonnées', 'id'=>$this->isTabInstalled('AdminContactInformation'), 'action'=>'CONTACTS');
        $tabs[1]['children'][] = array('name'=>"Objectifs", 'id'=>$this->isTabInstalled("AdminObjectives"), 'action'=>'OBJECTIVES');
        $tabs[1]['children'][] = array('name'=>"Résultats", 'id'=>$this->isTabInstalled("AdminResults"), 'action'=>'RESULTS');
        $tabs[1]['children'][] = array('name'=>"Imports / exports", 'id'=>$this->isTabInstalled("AdminData"), 'action'=>'DATA');
        $tabs[1]['children'][] = array('name'=>"Documents", 'id'=>$this->isTabInstalled("AdminDocuments"), 'action'=>'DOCUMENTS');

        $tabs[2] = array('name'=>'CLIENTS');
        $tabs[2]['children'][] = array('name'=>"Newsletter", 'id'=>$this->isTabInstalled('AdminNewsletter'), 'action'=>'NEWSLETTER');
        $tabs[2]['children'][] = array('name'=>"Type de comptes", 'id'=>$this->isTabInstalled('AdminCustomerTypes'), 'action'=>'ACCOUNT_TYPES');
        $tabs[2]['children'][] = array('name'=>"Etats clients", 'id'=>$this->isTabInstalled('AdminCustomerStates'), 'action'=>'CUSTOMER_STATES');

        $tabs[3] = array('name'=>'CATALOGUE');
        $tabs[3]['children'][] = array('name'=>"Options de commande", 'id'=>$this->isTabInstalled("AdminOrderOptions"), 'action'=>'ORDER_OPTIONS');
        $tabs[3]['children'][] = array('name'=>"Iconographie", 'id'=>$this->isTabInstalled("AdminIconography"), 'action'=>'ICONOGRAPHY');

        $this->context->smarty->assign('tabs', $tabs);

        return $this->display(__FILE__, 'config.tpl');
    }

    /**
    * Vérifie si un lien est déjà existant dans le menu latéral
    **/
    private function isTabInstalled($class_name) {
        return Db::getInstance()->getValue("SELECT id_tab FROM ps_tab WHERE class_name = '$class_name'");
    }

    /**
    * Crée un lien dans le menu latéral
    **/
    private function installTab($name, $class, $parent = false, $icon = false) {

        $tab = new Tab();
        $tab->active = 1;

        $tab->class_name = $class;
        $tab->name[1] = $name;

        if($icon)
            $tab->icon = $icon;

        if($parent)
            $tab->id_parent = (int) Tab::getIdFromClassName($parent);

        return $tab->add();
    }

}