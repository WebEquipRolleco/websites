<?php

if (!defined('_PS_VERSION_'))
    exit;

require_once(dirname(__FILE__)."/../../override/controllers/admin/exports/Export.php");

class Webequip_Configuration extends Module {

    const OLD_SAV_TAB = 'AdminParentCustomerThreads';

    const CONFIG_DEFAULT_STATE_SUCCESS = 'DEFAULT_STATE_SUCCESS';
    const CONFIG_DEFAULT_STATE_FAILURE = 'DEFAULT_STATE_FAILURE';
    const CONFIG_BLBC_HIDDEN_MAIL = "BLBC_HIDDEN_MAIL";
    const CONFIG_BLBC_ORDER_STATE = "BLBC_ORDER_STATE";
    const CONFIG_EXPORT_EXCLUDED_STATES = "EXPORT_EXCLUDED_STATES";
    const CONFIG_AFTER_SALES_ENABLED = "AFTER_SALES_ENABLED";

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

        $check .= $this->registerHook('displayAdminProductsPriceStepBottom');
        $check .= $this->registerHook('displayAdminProductsCombinationBottom');
        $check .= $this->registerHook('displayAdminProductsMainStepLeftColumnBottom');
        $check .= $this->registerHook('displayAdminProductsMainStepRightColumnBottom');
        $check .= $this->registerHook('dashboardZoneOne');

        if(!isTabInstalled("WEBEQUIP"))
            $check .= $this->installTab("Web-équip", "WEBEQUIP", false, 0);

        if(!isTabInstalled("AdminContactInformation"))
            $check .= $this->installTab('Coordonnées', 'AdminContactInformation', "WEBEQUIP", "beenhere");
            
        if(!isTabInstalled("AdminObjectives"))
            $check .= $this->installTab("Objectifs", "AdminObjectives", 'WEBEQUIP', 'equalizer');

        if(!isTabInstalled("AdminReport"))
            $check .= $this->installTab("Rapport", "AdminReport", 'WEBEQUIP', 'list');

        if(!isTabInstalled("AdminResults"))
            $check .= $this->installTab("Résultats", "AdminResults", 'WEBEQUIP', 'grid_on');

        if(!isTabInstalled("AdminImportExport"))
            $check .= $this->installTab("Résultats", "AdminImportExport", 'WEBEQUIP', 'refresh');

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

        if(!isTabInstalled("AdminSku"))
            $check .= $this->installTab("SKU", "AdminSku", 'AdminCatalog');

        if(!isTabInstalled("AdminProductSearch"))
            $check .= $this->installTab("Recherche", "AdminProductSearch", 'AdminCatalog');

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

        if(!isTabInstalled("AdminDocuments"))
            $check .= $this->installTab("Beezup", "AdminBeezup", 'WEBEQUIP', 'bug');

        return $check;
    }

    /**
    * Configuration du module
    **/
    public function getContent() {

        // AJAX
        $this->handleAjax();

        // Gestion des hooks
        if(!$this->isRegisteredInHook("displayAdminProductsPriceStepBottom"))
            $this->registerHook('displayAdminProductsPriceStepBottom');

        if(!$this->isRegisteredInHook("displayAdminProductsCombinationBottom"))
            $this->registerHook('displayAdminProductsCombinationBottom');

        if(!$this->isRegisteredInHook("displayAdminProductsMainStepLeftColumnBottom"))
            $this->registerHook('displayAdminProductsMainStepLeftColumnBottom');

        if(!$this->isRegisteredInHook("displayAdminProductsMainStepRightColumnBottom"))
            $this->registerHook('displayAdminProductsMainStepRightColumnBottom');

        if(!$this->isRegisteredInHook("dashboardZoneOne"))
            $this->registerHook('dashboardZoneOne');

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

            case 'REPORT':
                $this->installTab("Rapport", "AdminReport", 'WEBEQUIP', 'list');
            break;

            case 'RESULTS':
                $this->installTab("Résultats", "AdminResults", 'WEBEQUIP', 'grid_on');
            break;

            case 'IMPORT_EXPORT':
                $this->installTab("Import / Export", "AdminImportExport", 'WEBEQUIP', 'refresh');
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

            case 'SKU':
                $this->installTab("SKU", "AdminSku", 'AdminCatalog');
            break;

            case 'PRODUCT_SEARCH':
                $this->installTab("Recherche", "AdminProductSearch", 'AdminCatalog');
            break;

            case 'DOCUMENTS':
                $this->installTab("Documents", "AdminDocuments", 'WEBEQUIP', 'description');
            break;

            case 'BEEZUP':
                $this->installTab("Beezup", "AdminBeezup", 'WEBEQUIP', 'trending_up');
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
        $tabs[1]['children'][] = array('name'=>"Rapport", 'id'=>$this->isTabInstalled("AdminReport"), 'action'=>'REPORT');
        $tabs[1]['children'][] = array('name'=>"Résultats", 'id'=>$this->isTabInstalled("AdminResults"), 'action'=>'RESULTS');
        $tabs[1]['children'][] = array('name'=>"Documents", 'id'=>$this->isTabInstalled("AdminDocuments"), 'action'=>'DOCUMENTS');
        $tabs[1]['children'][] = array('name'=>"Import / Export", 'id'=>$this->isTabInstalled("AdminImportExport"), 'action'=>'IMPORT_EXPORT');
        $tabs[1]['children'][] = array('name'=>"BEEZUP", 'id'=>$this->isTabInstalled("AdminBeezup"), 'action'=>'BEEZUP');

        $tabs[2] = array('name'=>'CLIENTS');
        $tabs[2]['children'][] = array('name'=>"Newsletter", 'id'=>$this->isTabInstalled('AdminNewsletter'), 'action'=>'NEWSLETTER');
        $tabs[2]['children'][] = array('name'=>"Type de comptes", 'id'=>$this->isTabInstalled('AdminCustomerTypes'), 'action'=>'ACCOUNT_TYPES');
        $tabs[2]['children'][] = array('name'=>"Etats clients", 'id'=>$this->isTabInstalled('AdminCustomerStates'), 'action'=>'CUSTOMER_STATES');

        $tabs[3] = array('name'=>'CATALOGUE');
        $tabs[3]['children'][] = array('name'=>"Options de commande", 'id'=>$this->isTabInstalled("AdminOrderOptions"), 'action'=>'ORDER_OPTIONS');
        $tabs[3]['children'][] = array('name'=>"Iconographie", 'id'=>$this->isTabInstalled("AdminIconography"), 'action'=>'ICONOGRAPHY');
        $tabs[3]['children'][] = array('name'=>"Prix", 'id'=>$this->isTabInstalled("AdminSpecificPrices"), 'action'=>'SPECIFIC_PRICES');
        $tabs[3]['children'][] = array('name'=>"SKU", 'id'=>$this->isTabInstalled("AdminSku"), 'action'=>'SKU');
        $tabs[3]['children'][] = array('name'=>"Recherche", 'id'=>$this->isTabInstalled("AdminProductSearch"), 'action'=>'PRODUCT_SEARCH');
        
        // Configuration footer
        $links = array('FOOTER_LINK_PAIEMENT', 'FOOTER_LINK_FAQ', 'MENU_FORCED_FONT_SIZE', 'MENU_FORCED_NB_ELEMENTS');
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

            foreach(OrderState::getDefaultStateNames() as $name)
                Configuration::updateValue($name, $id_state);
        }

        if(Tools::getIsset(self::CONFIG_DEFAULT_STATE_FAILURE)) {
            $id_state = Tools::getValue(self::CONFIG_DEFAULT_STATE_FAILURE);

            $names = array(self::CONFIG_DEFAULT_STATE_FAILURE, 'PS_OS_ERROR');
            foreach($names as $name)
                Configuration::updateValue($name, $id_state);
        }

        // Enregistrement des états exclus des exports et statistiques
        if(Tools::getIsset(self::CONFIG_EXPORT_EXCLUDED_STATES))
            Configuration::updateValue(self::CONFIG_EXPORT_EXCLUDED_STATES, implode(',', Tools::getValue(self::CONFIG_EXPORT_EXCLUDED_STATES)));

        // Enregistrement de la configuration des BL et BC
        foreach(array(self::CONFIG_BLBC_HIDDEN_MAIL, self::CONFIG_BLBC_ORDER_STATE) as $name)
            if(Tools::getIsset($name))
                Configuration::updateValue($name, Tools::getValue($name));

        // Enregistrement des fonctionnalités SAV
        if(Tools::getIsset(self::CONFIG_AFTER_SALES_ENABLED))
            Configuration::updateValue(self::CONFIG_AFTER_SALES_ENABLED, Tools::getValue(self::CONFIG_AFTER_SALES_ENABLED));
        
        $this->context->smarty->assign('tabs', $tabs);
        $this->context->smarty->assign('cms', CMS::getCMSPages(1));
        $this->context->smarty->assign('old_sav_id', $this->isTabInstalled(self::OLD_SAV_TAB));

        $this->context->smarty->assign('states', OrderState::getOrderStates(1));
        $this->context->smarty->assign(self::CONFIG_DEFAULT_STATE_SUCCESS, Configuration::get(self::CONFIG_DEFAULT_STATE_SUCCESS));
        $this->context->smarty->assign(self::CONFIG_DEFAULT_STATE_FAILURE, Configuration::get(self::CONFIG_DEFAULT_STATE_FAILURE));
        $this->context->smarty->assign(self::CONFIG_BLBC_HIDDEN_MAIL, Configuration::get(self::CONFIG_BLBC_HIDDEN_MAIL));
        $this->context->smarty->assign(self::CONFIG_BLBC_ORDER_STATE, Configuration::get(self::CONFIG_BLBC_ORDER_STATE));
        $this->context->smarty->assign(self::CONFIG_EXPORT_EXCLUDED_STATES, explode(',', Configuration::get(self::CONFIG_EXPORT_EXCLUDED_STATES)));
        $this->context->smarty->assign(self::CONFIG_AFTER_SALES_ENABLED, Configuration::get(self::CONFIG_AFTER_SALES_ENABLED));

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
    * Ajoute la gestion du rollcash et des frais de port dans le produit
    **/
    public function hookDisplayAdminProductsPriceStepBottom($params) { 

        foreach(SpecificPrice::getByProductId($params['id_product']) as $row)
            $prices[] = new SpecificPrice($row['id_specific_price']);

        $this->context->smarty->assign('product', new Product($params['id_product'], true, 1, $this->context->shop->id));
        $this->context->smarty->assign('prices', $prices);

        return $this->display(__FILE__, 'product_prices.tpl');
    }

    /**
    * Ajoute la gestion du rollcash et des frais de port dans les déclinaisons
    **/
    public function hookDisplayAdminProductsCombinationBottom($params) {
        $this->context->smarty->assign('combination', new Combination($params['id_product_attribute']));
       return $this->display(__FILE__, 'combination_prices.tpl');
    }

    /**
    * Ajoute la gestion des commentaires produits
    **/
    public function hookDisplayAdminProductsMainStepLeftColumnBottom($params) {
        $this->context->smarty->assign('product', new Product($params['id_product'], true, 1, $this->context->shop->id));
        return $this->display(__FILE__, 'product_essential.tpl');
    }

    /**
    * Ajoute l'option de destockage des produits
    **/
    public function hookDisplayAdminProductsMainStepRightColumnBottom($params) {
        $this->context->smarty->assign('product', new Product($params['id_product'], true, 1, $this->context->shop->id));
        return $this->display(__FILE__, 'product_destocking.tpl');
    }

    /**
    * Ajoute un rappel des produits sans prix dans le dashboard Admin
    **/
    public function hookDashboardZoneOne($params) {

        $export = new ExportProductsWithoutPrices();
        $this->context->smarty->assign('nb_priceless_products', $export->count());

        $export = new ExportCombinationsWithoutPrices();
        $this->context->smarty->assign('nb_priceless_combinations', $export->count());
        
        return $this->display(__FILE__, 'admin_dashboard_left.tpl');
    }

    /**
    * Gestion AJAX
    **/
    public function handleAjax() {
        if(Tools::getValue('ajax')) {

            // Destockage
            if($id = Tools::getValue('toggle_destocking')) {
                $product = new Product($id, true, 1, $this->context->shop->id);
                $product->destocking = !$product->destocking;
                $product->save();

                die($product->destocking ? "1" : "0");
            }

            // Suppression des prix
            if(Tools::getValue('action') == 'delete_price') {
                if($id = Tools::getValue('id_price')) {
                    $price = new SpecificPrice($id);
                    if($price->id) {
                        $price->delete();
                        die($id);
                    }

                }

                die(0);
            }

            // Sauvegarde de prix
            if(Tools::getValue('action') == 'save_prices') {
                
                $form = array();
                parse_str(Tools::getValue('form'), $form);

                if(is_array($form) and isset($form['prices'])) {
                    foreach($form['prices'] as $id => $row) {

                        $price = new SpecificPrice($id);
                        
                        $price->buying_price = $row['buying_price'];
                        $price->delivery_fees = $row['delivery_fees'];
                        $price->price = $row['price'];
                        $price->comment_1 = $row['comment_1'];
                        $price->comment_2 = $row['comment_2'];

                        $price->save();
                    }
                }
            }

            // Recherche de références (accessoires)
            if(Tools::getValue('action') == 'find_reference') {

                $this->context->smarty->assign('products', Product::searchByReference(Tools::getValue('reference')));
                $this->context->smarty->assign('link', new Link());

                $tpl = $this->context->smarty->createTemplate(_PS_ROOT_DIR_."/modules/webequip_configuration/views/templates/hook/accessory_search.tpl");
                die($tpl->fetch());
            }

            // Ajout d'un accessoire
            if(Tools::getValue('action') == 'add_accessory' and $id_product = Tools::getValue('id_product') and $data = Tools::getValue('data')) {
                $data = explode('_', $data);

                if(!Accessory::exists($id_product, $data[0], $data[1])) {

                    $accessory = new Accessory();
                    $accessory->id_product = $id_product;
                    $accessory->id_product_accessory = $data[0];
                    $accessory->id_combination_accessory = $data[1];
                    $accessory->save();

                }

                $this->loadAccessories($id_product);
            }

            // Chargement de la liste des accessoires
            if(Tools::getValue('action') == 'load_accessories' and $id_product = Tools::getValue('id_product')) {
                $this->loadAccessories($id_product);
            }

            // Suppression d'un accessoire
            if(Tools::getValue('action') == 'remove_accessory' and $id = Tools::getValue('id')) {

                $accessory = new Accessory($id);
                $accessory->delete();

                $this->loadAccessories($accessory->id_product);
            }

            // Chargement de la liste des icones
            if(Tools::getValue('action') == 'load_icons' and $id_product = Tools::getValue('id_product')) {
                $this->loadIcons($id_product);
            }

            // Autoriser une icone
            if(Tools::getValue('action') == 'enable_icon' and $id_icon = Tools::getValue('id_icon') and $id_product = Tools::getValue('id_product')) {
                
                $icon = new ProductIcon($id_icon);
                if($icon->id) $icon->addProduct($id_product);

                $this->loadIcons($id_product);
            }

            // Bloquer une icone
            if(Tools::getValue('action') == 'disable_icon' and $id_icon = Tools::getValue('id_icon') and $id_product = Tools::getValue('id_product')) {
                
                $icon = new ProductIcon($id_icon);
                if($icon->id) $icon->removeProduct($id_product);

                $this->loadIcons($id_product);
            }
        }
    }

    /**
    * Charge le tableau des accessoires d'un produit
    * @param int $id_product
    **/
    private function loadAccessories($id_product) {

        $this->context->smarty->assign('accessories', Accessory::find($id_product, false));
        $this->context->smarty->assign('link', new Link());
        
        $tpl = $this->context->smarty->createTemplate(_PS_ROOT_DIR_."/modules/webequip_configuration/views/templates/hook/accessory_list.tpl");
        die($tpl->fetch());
    }

    /**
    * Charge le table des icones d'un produit
    * @param int $id_product
    **/
    private function loadIcons($id_product) {

        $this->context->smarty->assign('icons', ProductIcon::getList());
        $this->context->smarty->assign('id_product', $id_product);
        $this->context->smarty->assign('id_shop', $this->context->shop->id);
        $this->context->smarty->assign('link', new Link());

        $tpl = $this->context->smarty->createTemplate(_PS_ROOT_DIR_."/modules/webequip_configuration/views/templates/hook/icons_list.tpl");
        die($tpl->fetch());
    }
}