<?php

if (!defined('_PS_VERSION_'))
    exit;


class Webequip_Configuration extends Module {

    const OLD_SAV_TAB = 'AdminParentCustomerThreads';

    const CONFIG_DEFAULT_STATE_SUCCESS = 'DEFAULT_STATE_SUCCESS';
    const CONFIG_DEFAULT_STATE_FAILURE = 'DEFAULT_STATE_FAILURE';

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

        if(!isTabInstalled("AdminSpecificPrices"))
            $check .= $this->installTab('Prix', "AdminSpecificPrices", "AdminCatalog");

        if(!isTabInstalled("AdminOrderStateRules"))
            $check .= $this->installTab("Régles de redirection", 'AdminOrderStateRules', "AdminParentOrders");
        
        if(!isTabInstalled('AdminLatePayments'))
            $check .= $this->installTab("Facture impayées", "AdminLatePayments", "AdminParentOrders");

        if(!isTabInstalled('AdminWaitingOrders'))
            $check .= $this->installTab("Commandes en attente", "AdminWaitingOrders", "AdminParentOrders");

        if(!isTabInstalled('AdminAfterSales'))
            $check .= $this->installTab("SAV", "AdminAfterSales", "AdminParentOrders");

        if(!isTabInstalled("AdminDocuments"))
            $check .= $this->installTab("Documents", "AdminDocuments", 'WEBEQUIP', 'file');

        return $check;
    }

    /**
    * Configuration du module
    **/
    public function getContent() {

        // Gestion des hooks
        if(!$this->isRegisteredInHook("displayAdminProductsPriceStepBottom"))
            $this->registerHook('displayAdminProductsPriceStepBottom');

        if(!$this->isRegisteredInHook("displayAdminProductsPriceStepBottom"))
            $this->registerHook('displayAdminProductsPriceStepBottom');

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

            case 'STATE_RULES':
                $this->installTab("Régles de redirection", 'AdminOrderStateRules', "AdminParentOrders");
            break;

            case 'LATE_PAYMENTS':
                $this->installTab("Facture impayées", "AdminLatePayments", "AdminParentOrders");
            break;

            case 'WAITING_ORDERS':
                $this->installTab("Commandes en attente", "AdminWaitingOrders", "AdminParentOrders");
            break;

            case 'AFTER_SALES':
                $this->installTab("SAV", "AdminAfterSales", "AdminParentOrders");
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

            case 'SPECIFIC_PRICES':
                $this->installTab('Prix', "AdminSpecificPrices", "AdminCatalog");
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
        $tabs[0]['children'][] = array('name'=>"Facture impayées", 'id'=>$this->isTabInstalled("AdminLatePayments"), 'action'=>'LATE_PAYMENTS');
        $tabs[0]['children'][] = array('name'=>"Commandes en attente", 'id'=>$this->isTabInstalled("AdminWaitingOrders"), 'action'=>'WAITING_ORDERS');
        $tabs[0]['children'][] = array('name'=>"SAV", 'id'=>$this->isTabInstalled("AdminAfterSales"), 'action'=>'AFTER_SALES');

        $tabs[1] = array('name'=>"WEB-EQUIP", 'id'=>$this->isTabInstalled('WEBEQUIP'), 'action'=>'WEBEQUIP');
        $tabs[1]['children'][] = array('name'=>'Coordonnées', 'id'=>$this->isTabInstalled('AdminContactInformation'), 'action'=>'CONTACTS');
        $tabs[1]['children'][] = array('name'=>"Objectifs", 'id'=>$this->isTabInstalled("AdminObjectives"), 'action'=>'OBJECTIVES');
        $tabs[1]['children'][] = array('name'=>"Résultats", 'id'=>$this->isTabInstalled("AdminResults"), 'action'=>'RESULTS');
        $tabs[1]['children'][] = array('name'=>"Documents", 'id'=>$this->isTabInstalled("AdminDocuments"), 'action'=>'DOCUMENTS');

        $tabs[2] = array('name'=>'CLIENTS');
        $tabs[2]['children'][] = array('name'=>"Newsletter", 'id'=>$this->isTabInstalled('AdminNewsletter'), 'action'=>'NEWSLETTER');
        $tabs[2]['children'][] = array('name'=>"Type de comptes", 'id'=>$this->isTabInstalled('AdminCustomerTypes'), 'action'=>'ACCOUNT_TYPES');
        $tabs[2]['children'][] = array('name'=>"Etats clients", 'id'=>$this->isTabInstalled('AdminCustomerStates'), 'action'=>'CUSTOMER_STATES');

        $tabs[3] = array('name'=>'CATALOGUE');
        $tabs[3]['children'][] = array('name'=>"Options de commande", 'id'=>$this->isTabInstalled("AdminOrderOptions"), 'action'=>'ORDER_OPTIONS');
        $tabs[3]['children'][] = array('name'=>"Iconographie", 'id'=>$this->isTabInstalled("AdminIconography"), 'action'=>'ICONOGRAPHY');
        $tabs[3]['children'][] = array('name'=>"Prix", 'id'=>$this->isTabInstalled("AdminSpecificPrices"), 'action'=>'SPECIFIC_PRICES');

        // Configuration footer
        $links = array('FOOTER_LINK_PAIEMENT', 'FOOTER_LINK_FAQ', 'MENU_FORCED_FONT_SIZE');
        foreach($links as $name) {

            if(Tools::getIsset($name))
                Configuration::updateValue($name, Tools::getValue($name));

            $this->context->smarty->assign($name, Configuration::get($name));
        }

        // Désinstallation du module SAV
        if(Tools::isSubmit('uninstall_sav'))
            Db::getInstance()->execute("DELETE FROM ps_tab WHERE class_name = '".self::OLD_SAV_TAB."'");

        // Enregistrement des états commandes par défaut
        if(Tools::getIsset(self::CONFIG_DEFAULT_STATE_SUCCESS)) {
            $id_state = Tools::getValue(self::CONFIG_DEFAULT_STATE_SUCCESS);

            $names = array(self::CONFIG_DEFAULT_STATE_SUCCESS, 'DEFAULT_ID_STATE_OK', 'PS_OS_CHEQUE', 'PS_OS_PAYMENT', 'PS_OS_PREPARATION', 'PS_OS_WS_PAYMENT', 'PS_OS_BANKWIRE', 'PS_OS_BANKWIRE_45J', 'PS_OS_PAYPAL', 'PS_OS_BANKWIRE_ADMIN');
            foreach($names as $name)
                Configuration::updateValue($name, $id_state);
        }

        if(Tools::getIsset(self::CONFIG_DEFAULT_STATE_FAILURE)) {
            $id_state = Tools::getValue(self::CONFIG_DEFAULT_STATE_FAILURE);

            $names = array(self::CONFIG_DEFAULT_STATE_FAILURE, 'PS_OS_ERROR');
            foreach($names as $name)
                Configuration::updateValue($name, $id_state);
        }

        $this->context->smarty->assign('tabs', $tabs);
        $this->context->smarty->assign('cms', CMS::getCMSPages(1));
        $this->context->smarty->assign('old_sav_id', $this->isTabInstalled(self::OLD_SAV_TAB));

        $this->context->smarty->assign('states', OrderState::getOrderStates(1));
        $this->context->smarty->assign(self::CONFIG_DEFAULT_STATE_SUCCESS, Configuration::get(self::CONFIG_DEFAULT_STATE_SUCCESS));
        $this->context->smarty->assign(self::CONFIG_DEFAULT_STATE_FAILURE, Configuration::get(self::CONFIG_DEFAULT_STATE_FAILURE));

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

    /**
    * Ajoute la gestion du rollcash dans le produit
    **/
    public function hookDisplayAdminProductsPriceStepBottom($params) { 
        $this->context->smarty->assign('product', new Product($params['id_product']));
        return $this->display(__FILE__, 'product_rollcash.tpl');
    }

    /**
    * Ajoute la gestion du rollcash dans les déclinaisons
    **/
    public function hookDisplayAdminProductsCombinationBottom($params) {
        $this->context->smarty->assign('combination', new Combination($params['id_product_attribute']));
       return $this->display(__FILE__, 'combination_rollcash.tpl');
    }

}