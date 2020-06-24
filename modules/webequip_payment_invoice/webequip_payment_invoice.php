<?php

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_'))
    exit;

class Webequip_payment_invoice extends PaymentModule {

    private $_html = '';
    private $_postErrors = array();

    public function __construct() {

        $this->name = 'webequip_payment_invoice';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->author = 'Web-equip';
        $this->controllers = array('payment', 'validation');

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Paiement à 45 jours date de facture', array(), 'Modules.Checkpayment.Admin');
        $this->description = $this->trans('Aucun prérequis de paiement lors du passage de la commande', array(), 'Modules.Checkpayment.Admin');
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
    }

    /**
    * Installation du module
    **/
    public function install() {
        return parent::install() and $this->registerHook('paymentOptions') and $this->registerHook('paymentReturn');
    }


    /**
    * Configuration du module
    **/
    public function getContent() {

        foreach(array('PAYMENT_INVOICE_NB_DAYS', 'PS_SHOP_CIC', 'PS_SHOP_IBAN', 'PS_SHOP_BIC', 'DEFAULT_ID_STATE_OK') as $name) {

            if($value = Tools::getIsset($name))
                Configuration::updateValue($name, Tools::getValue($name)); 

            $this->context->smarty->assign($name, Configuration::get($name));
        }
        
        return $this->display(__FILE__, './views/templates/admin/config.tpl');
    }

    /**
    * Affichage dans les options de paiement 
    * @param array $params
    **/
    public function hookPaymentOptions($params) {

        if (!$this->active)
            return;

        $nb = Configuration::get('PAYMENT_INVOICE_NB_DAYS');
        $cic = Configuration::get('PS_SHOP_CIC');
        $iban = Configuration::get('PS_SHOP_IBAN');
        $bic = Configuration::get('PS_SHOP_BIC');

        if(!$nb or !$cic or !$iban or !$bic)
            return;

        $this->smarty->assign('nb', $nb);
        $this->smarty->assign('CIC', $cic);
        $this->smarty->assign('IBAN', $iban);
        $this->smarty->assign('BIC', $bic);

        $option = new PaymentOption();
        $option->setModuleName($this->name);
        $option->setCallToActionText($this->trans('%nb%J date de facture', array('%nb%'=>$nb), 'Modules.Checkpayment.Admin'));
        $option->setAction($this->context->link->getModuleLink($this->name, 'validation', array(), true));
        $option->setAdditionalInformation($this->fetch('module:webequip_payment_invoice/views/templates/front/payment_infos.tpl'));

        return [$option];
    }

    /**
    * Validation de la commande 
    * @param array $params
    **/
    /*public function hookPaymentReturn($params) {

        if (!$this->active)
            return;

        $this->smarty->assign('order', $params['order']);
        return $this->fetch('module:webequip_payment_invoice/views/templates/hook/payment_return.tpl');
    }*/

}
