<?php
/**
 * Project : everpsblog
 * @author Team Ever
 * @link https://www.team-ever.com
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Everpscustomerconnect extends Module
{
    private $html;
    private $postErrors = array();
    private $postSuccess = array();

    public function __construct()
    {
        $this->name = 'everpscustomerconnect';
        $this->tab = 'administration';
        $this->version = '1.0.3';
        $this->author = 'Team Ever';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Ever PS Customer Connect');
        $this->description = $this->l('Allows you to connect on chosen customer account ');

        $this->confirmUninstall = $this->l('');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        return parent::install()
            && $this->registerHook('displayAdminOrderContentOrder')
            && $this->registerHook('displayAdminOrder')
            && $this->registerHook('displayAdminCustomers');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $this->html = '';
        if (((bool)Tools::isSubmit('submitEverpscustomerconnectModule')) == true) {
            $this->postValidation();

            if (!count($this->postErrors)) {
                $this->postProcess();
            }
        }
        $cookie = $this->postProcess();
        if ($cookie->logged) {
            $this->context->smarty->assign(array(
                'lastname' => $cookie->customer_lastname,
                'firstname' => $cookie->customer_firstname,
                'base_uri' => __PS_BASE_URI__,
            ));
        }

        $this->context->smarty->assign(array(
            'evercustomerimage_dir' => $this->_path.'views/img/',
        ));

        $this->html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/header.tpl');
        $this->html .= $this->renderForm();
        $this->html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/footer.tpl');

        return $this->html;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitEverpscustomerconnectModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        $customers = Customer::getCustomers();
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Customer'),
                        'name' => 'EVERPSCUSTOMERCONNECT_CUST',
                        'desc' => $this->l('Please choose customer'),
                        'required' => true,
                        'options' => array(
                            'query' => $customers,
                            'id' => 'id_customer',
                            'name' => 'email'
                        )
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'EVERPSCUSTOMERCONNECT_CUST' => Configuration::get('EVERPSCUSTOMERCONNECT_CUST', true),
        );
    }

    public function postValidation()
    {
        if (((bool)Tools::isSubmit('submitEverpscustomerconnectModule')) == true) {
            if (!Tools::getValue('EVERPSCUSTOMERCONNECT_CUST')
                || !Validate::isInt(Tools::getValue('EVERPSCUSTOMERCONNECT_CUST'))
            ) {
                $this->posterrors[] = $this->l('error : [Customer] is not valid');
            }
        }
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
        $cookie_lifetime = (int) ( defined('_PS_ADMIN_DIR_') ? Configuration::get('PS_COOKIE_LIFETIME_BO') : Configuration::get('PS_COOKIE_LIFETIME_FO') );
        $cookie_lifetime = time() + ( max($cookie_lifetime, 1) * 3600 );
        $cookie = new Cookie(
            'ps-s'.Context::getContext()->shop->id,
            '',
            $cookie_lifetime
        );
        if ($cookie->logged) {
            $cookie->logout();
        }
        Tools::setCookieLanguage();
        Tools::switchLanguage();
        $customer = new Customer((int)Tools::getValue('EVERPSCUSTOMERCONNECT_CUST'));
        $cookie->id_customer = (int)$customer->id;
        $cookie->customer_lastname = $customer->lastname;
        $cookie->customer_firstname = $customer->firstname;
        $cookie->logged = 1;
        $cookie->passwd = $customer->passwd;
        $cookie->email = $customer->email;
        return $cookie;
    }

    private function everConnect($id_customer)
    {
        $cookie_lifetime = (int) ( defined('_PS_ADMIN_DIR_') ? Configuration::get('PS_COOKIE_LIFETIME_BO') : Configuration::get('PS_COOKIE_LIFETIME_FO') );
        $cookie_lifetime = time() + ( max($cookie_lifetime, 1) * 3600 );
        $cookie = new Cookie(
            'ps-s'.Context::getContext()->shop->id,
            '',
            $cookie_lifetime
        );
        if ($cookie->logged) {
            $cookie->logout();
        }
        Tools::setCookieLanguage();
        Tools::switchLanguage();
        $customer = new Customer((int)$id_customer);
        $cookie->id_customer = (int)$customer->id;
        $cookie->customer_lastname = $customer->lastname;
        $cookie->customer_firstname = $customer->firstname;
        $cookie->logged = 1;
        $cookie->passwd = $customer->passwd;
        $cookie->email = $customer->email;
        return $cookie;
    }

    public function hookDisplayAdminCustomers($params)
    {
        if (isset($params) && $params['id_customer']) {
            $id_customer = (int)$params['id_customer'];
        } else {
            $order = new Order((int)$params['id_order']);
            $id_customer = (int)$order->id_customer;
        }
        if (Tools::isSubmit('submitSuperUser')) {
            $cookie = $this->everConnect($id_customer);
        }
        $customer = new Customer($id_customer);
        if (isset($cookie) && $cookie->logged) {
            $this->context->smarty->assign(array(
                'logged' => true,
            ));
        } else {
            $this->context->smarty->assign(array(
                'logged' => false,
            ));
        }
        $this->context->smarty->assign(array(
            'evercustomerimage_dir' => $this->_path.'views/img/',
            'id_customer' => $id_customer,
            'lastname' => $customer->lastname,
            'firstname' => $customer->firstname,
            'base_uri' => __PS_BASE_URI__,
        ));
        return $this->display(__FILE__, 'views/templates/hook/admin.tpl');
    }

    public function hookDisplayAdminOrder($params)
    {
        $order = new Order((int)$params['id_order']);
        $id_customer = (int)$order->id_customer;
        if (Tools::isSubmit('submitSuperUser')) {
            $cookie = $this->everConnect($id_customer);
        }
        $customer = new Customer($id_customer);
        if (isset($cookie) && $cookie->logged) {
            $this->context->smarty->assign(array(
                'logged' => true,
            ));
        } else {
            $this->context->smarty->assign(array(
                'logged' => false,
            ));
        }
        $this->context->smarty->assign(array(
            'evercustomerimage_dir' => $this->_path.'views/img/',
            'id_customer' => $id_customer,
            'lastname' => $customer->lastname,
            'firstname' => $customer->firstname,
            'base_uri' => __PS_BASE_URI__,
        ));
        return $this->display(__FILE__, 'views/templates/hook/admin.tpl');
    }
}
