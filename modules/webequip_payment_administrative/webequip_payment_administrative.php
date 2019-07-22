<?php

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_'))
    exit;

class Webequip_payment_administrative extends PaymentModule {

    private $_html = '';
    private $_postErrors = array();

    public function __construct() {

        $this->name = 'webequip_payment_administrative';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->author = 'Web-equip';
        $this->controllers = array('payment', 'validation');

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Paiement par mandat administratif', array(), 'Modules.Checkpayment.Admin');
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

        foreach(array('PS_PAYMENT_ADMINISTRATIVE_TO', 'PS_SHOP_CIC', 'PS_SHOP_IBAN', 'PS_SHOP_BIC', 'DEFAULT_ID_STATE_OK') as $name) {

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

        $to = Configuration::get('PS_PAYMENT_ADMINISTRATIVE_TO');
        $cic = Configuration::get('PS_SHOP_CIC');
        $iban = Configuration::get('PS_SHOP_IBAN');
        $bic = Configuration::get('PS_SHOP_BIC');

        if(!$to or !$cic or !$iban or !$bic)
            return;

        $this->smarty->assign('TO', $to);
        $this->smarty->assign('CIC', $cic);
        $this->smarty->assign('IBAN', $iban);
        $this->smarty->assign('BIC', $bic);

        $option = new PaymentOption();
        $option->setModuleName($this->name);
        $option->setCallToActionText($this->trans('Mandat administratif', array(), 'Modules.Checkpayment.Admin'));
        $option->setAction($this->context->link->getModuleLink($this->name, 'validation', array(), true));
        $option->setAdditionalInformation($this->fetch('module:webequip_payment_administrative/views/templates/front/payment_infos.tpl'));

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
        return $this->fetch('module:webequip_payment_administrative/views/templates/hook/payment_return.tpl');
    }*/

}
