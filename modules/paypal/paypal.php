<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2019 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;
use PaypalPPBTlib\Extensions\ProcessLogger\ProcessLoggerHandler;

if (!defined('_PS_VERSION_')) {
    exit;
}
include_once(_PS_MODULE_DIR_.'paypal/vendor/autoload.php');
use PaypalPPBTlib\Module\PaymentModule;
use PaypalPPBTlib\Extensions\ProcessLogger\ProcessLoggerExtension;

include_once(_PS_MODULE_DIR_.'paypal/sdk/BraintreeSiSdk.php');

include_once 'classes/AbstractMethodPaypal.php';
include_once 'classes/PaypalCapture.php';
include_once 'classes/PaypalOrder.php';
include_once 'classes/PaypalCustomer.php';
include_once 'classes/PaypalVaulting.php';
include_once 'classes/PaypalLog.php';

const BT_CARD_PAYMENT = 'card-braintree';
const BT_PAYPAL_PAYMENT = 'paypal-braintree';
// Method Alias :
// EC = express checkout
// ECS = express checkout sortcut
// BT = Braintree
// PPP = PayPal Plus

class PayPal extends PaymentModule
{
    public static $dev = true;
    public $express_checkout;
    public $message;
    public $amount_paid_paypal;
    public $module_link;
    public $errors;
    public $bt_countries = array("FR", "GB", "IT", "ES", "US");
    /** @var array matrix of state iso codes between paypal and prestashop */
    public static $state_iso_code_matrix = array(
        'MX' => array(
            'AGS' => 'AGS',
            'BCN' => 'BC',
            'BCS' => 'BCS',
            'CAM' => 'CAMP',
            'CHP' => 'CHIS',
            'CHH' => 'CHIH',
            'COA' => 'COAH',
            'COL' => 'COL',
            'DIF' => 'DF',
            'DUR' => 'DGO',
            'GUA' => 'GTO',
            'GRO' => 'GRO',
            'HID' => 'HGO',
            'JAL' => 'JAL',
            'MEX' => 'MEX',
            'MIC' => 'MICH',
            'MOR' => 'MOR',
            'NAY' => 'NAY',
            'NLE' => 'NL',
            'OAX' => 'OAX',
            'PUE' => 'PUE',
            'QUE' => 'QRO',
            'ROO' => 'Q ROO',
            'SLP' => 'SLP',
            'SIN' => 'SIN',
            'SON' => 'SON',
            'TAB' => 'TAB',
            'TAM' => 'TAMPS',
            'TLA' => 'TLAX',
            'VER' => 'VER',
            'YUC' => 'YUC',
            'ZAC' => 'ZAC',
        ),
        'JP' => array(
            'Aichi' => 'Aichi-KEN',
            'Akita' => 'Akita-KEN',
            'Aomori' => 'Aomori-KEN',
            'Chiba' => 'Chiba-KEN',
            'Ehime' => 'Ehime-KEN',
            'Fukui' => 'Fukui-KEN',
            'Fukuoka' => 'Fukuoka-KEN',
            'Fukushima' => 'Fukushima-KEN',
            'Gifu' => 'Gifu-KEN',
            'Gunma' => 'Gunma-KEN',
            'Hiroshima' => 'Hiroshima-KEN',
            'Hokkaido' => 'Hokkaido-KEN',
            'Hyogo' => 'Hyogo-KEN',
            'Ibaraki' => 'Ibaraki-KEN',
            'Ishikawa' => 'Ishikawa-KEN',
            'Iwate' => 'Iwate-KEN',
            'Kagawa' => 'Kagawa-KEN',
            'Kagoshima' => 'Kagoshima-KEN',
            'Kanagawa' => 'Kanagawa-KEN',
            'Kochi' => 'Kochi-KEN',
            'Kumamoto' => 'Kumamoto-KEN',
            'Kyoto' => 'Kyoto-KEN',
            'Mie' => 'Mie-KEN',
            'Miyagi' => 'Miyagi-KEN',
            'Miyazaki' => 'Miyazaki-KEN',
            'Nagano' => 'Nagano-KEN',
            'Nagasaki' => 'Nagasaki-KEN',
            'Nara' => 'Nara-KEN',
            'Niigata' => 'Niigata-KEN',
            'Oita' => 'Oita-KEN',
            'Okayama' => 'Okayama-KEN',
            'Okinawa' => 'Okinawa-KEN',
            'Osaka' => 'Osaka-KEN',
            'Saga' => 'Saga-KEN',
            'Saitama' => 'Saitama-KEN',
            'Shiga' => 'Shiga-KEN',
            'Shimane' => 'Shimane-KEN',
            'Shizuoka' => 'Shizuoka-KEN',
            'Tochigi' => 'Tochigi-KEN',
            'Tokushima' => 'Tokushima-KEN',
            'Tokyo' => 'Tokyo-KEN',
            'Tottori' => 'Tottori-KEN',
            'Toyama' => 'Toyama-KEN',
            'Wakayama' => 'Wakayama-KEN',
            'Yamagata' => 'Yamagata-KEN',
            'Yamaguchi' => 'Yamaguchi-KEN',
            'Yamanashi' => 'Yamanashi-KEN'
        )
    );

    /**
    * List of objectModel used in this Module
    * @var array
    */
    public $objectModels = array(
        'PaypalCapture',
        'PaypalOrder',
        'PaypalVaulting',
        'PaypalCustomer'
    );

    /**
     * List of ppbtlib extentions
     */
    public $extensions = array(
        PaypalPPBTlib\Extensions\ProcessLogger\ProcessLoggerExtension::class,
    );

    /**
     * List of hooks used in this Module
     */
    public $hooks = array(
         'paymentOptions',
         'paymentReturn',
         'displayOrderConfirmation',
         'displayAdminOrder',
         'actionOrderStatusPostUpdate',
         'actionOrderStatusUpdate',
         'header',
         'actionObjectCurrencyAddAfter',
         'displayBackOfficeHeader',
         'displayFooterProduct',
         'actionBeforeCartUpdateQty',
         'displayReassurance',
         'displayInvoiceLegalFreeText',
         'actionAdminControllerSetMedia',
         'displayMyAccountBlock',
         'displayCustomerAccount',
         'displayShoppingCartFooter',
         'actionOrderSlipAdd',
         'displayAdminOrderTabOrder',
         'displayAdminOrderContentOrder',
         'displayAdminCartsView'
     );

    /**
     * List of admin tabs used in this Module
     */
    public $moduleAdminControllers = array(
             array(
             'name' => array(
                 'en' => 'PayPal & Braintree Official',
                 'fr' => 'PayPal et Braintree Officiel'
             ),
             'class_name' => 'AdminParentPaypalConfiguration',
             'parent_class_name' => 'SELL',
             'visible' => true,
             'icon' => 'payment'
         ),
         array(
             'name' => array(
                 'en' => 'Configuration',
                 'fr' => 'Configuration'
             ),
             'class_name' => 'AdminPaypalConfiguration',
             'parent_class_name' => 'AdminParentPaypalConfiguration',
             'visible' => true,
         ),
         array(
             'name' => array(
                 'en' => 'Report',
                 'fr' => 'Rapport'
             ),
             'class_name' => 'AdminPaypalStats',
             'parent_class_name' => 'AdminParentPaypalConfiguration',
             'visible' => true,
         ),
     );

    public function __construct()
    {
        $this->name = 'paypal';
        $this->tab = 'payments_gateways';
        $this->version = '4.5.0';
        $this->author = 'PrestaShop';
        $this->display = 'view';
        $this->module_key = '336225a5988ad434b782f2d868d7bfcd';
        $this->is_eu_compatible = 1;
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->controllers = array('payment', 'validation');
        $this->bootstrap = true;

        $this->currencies = true;
        $this->currencies_mode = 'radio';

        parent::__construct();

        $this->displayName = $this->l('PayPal');
        $this->description = $this->l('Allow your customers to pay with PayPal - the safest, quickest and easiest way to pay online.');
        $this->confirmUninstall = $this->l('Are you sure you want to delete your details?');
        $this->express_checkout = $this->l('PayPal Express Checkout ');
        $this->module_link = $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;

        $this->errors = '';
    }

    public function install()
    {
        // Install default
        if (!parent::install()) {
            return false;
        }

        // Registration order status
        if (!$this->installOrderState()) {
            return false;
        }
        $this->checkPaypalStats();
        if (!Configuration::updateValue('PAYPAL_MERCHANT_ID_SANDBOX', '')
            || !Configuration::updateValue('PAYPAL_MERCHANT_ID_LIVE', '')
            || !Configuration::updateValue('PAYPAL_USERNAME_SANDBOX', '')
            || !Configuration::updateValue('PAYPAL_PSWD_SANDBOX', '')
            || !Configuration::updateValue('PAYPAL_SIGNATURE_SANDBOX', '')
            || !Configuration::updateValue('PAYPAL_SANDBOX_ACCESS', 0)
            || !Configuration::updateValue('PAYPAL_USERNAME_LIVE', '')
            || !Configuration::updateValue('PAYPAL_PSWD_LIVE', '')
            || !Configuration::updateValue('PAYPAL_SIGNATURE_LIVE', '')
            || !Configuration::updateValue('PAYPAL_LIVE_ACCESS', 0)
            || !Configuration::updateValue('PAYPAL_SANDBOX', 0)
            || !Configuration::updateValue('PAYPAL_API_INTENT', 'sale')
            || !Configuration::updateValue('PAYPAL_API_ADVANTAGES', 1)
            || !Configuration::updateValue('PAYPAL_API_CARD', 0)
            || !Configuration::updateValue('PAYPAL_METHOD', '')
            || !Configuration::updateValue('PAYPAL_EXPRESS_CHECKOUT_SHORTCUT', 0)
            || !Configuration::updateValue('PAYPAL_EXPRESS_CHECKOUT_SHORTCUT_CART', 1)
            || !Configuration::updateValue('PAYPAL_CRON_TIME', date('Y-m-d H:m:s'))
            || !Configuration::updateValue('PAYPAL_BY_BRAINTREE', 0)
            || !Configuration::updateValue('PAYPAL_EXPRESS_CHECKOUT_IN_CONTEXT', 0)
            || !Configuration::updateValue('PAYPAL_VAULTING', 0)
            || !Configuration::updateValue('PAYPAL_REQUIREMENTS', 0)
        ) {
            return false;
        }

        return true;
    }

    /**
     * Set default currency restriction to "customer currency"
     * @return bool
     */
    public function updateRadioCurrencyRestrictionsForModule()
    {
        $shops = Shop::getShops(true, null, true);
        foreach ($shops as $s) {
            if (!Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'module_currency` SET `id_currency` = -1
                WHERE `id_shop` = "'.(int)$s.'" AND `id_module` = '.(int)$this->id)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Create order state
     * @return boolean
     */
    public function installOrderState()
    {
        if (!Configuration::get('PAYPAL_OS_WAITING')
            || !Validate::isLoadedObject(new OrderState(Configuration::get('PAYPAL_OS_WAITING')))) {
            $order_state = new OrderState();
            $order_state->name = array();
            foreach (Language::getLanguages() as $language) {
                if (Tools::strtolower($language['iso_code']) == 'fr') {
                    $order_state->name[$language['id_lang']] = 'En attente de paiement PayPal';
                } else {
                    $order_state->name[$language['id_lang']] = 'Awaiting for PayPal payment';
                }
            }
            $order_state->send_email = false;
            $order_state->color = '#4169E1';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            if ($order_state->add()) {
                $source = _PS_MODULE_DIR_.'paypal/views/img/os_paypal.png';
                $destination = _PS_ROOT_DIR_.'/img/os/'.(int) $order_state->id.'.gif';
                copy($source, $destination);
            }
            Configuration::updateValue('PAYPAL_OS_WAITING', (int) $order_state->id);
        }
        if (!Configuration::get('PAYPAL_BRAINTREE_OS_AWAITING')
            || !Validate::isLoadedObject(new OrderState(Configuration::get('PAYPAL_BRAINTREE_OS_AWAITING')))) {
            $order_state = new OrderState();
            $order_state->name = array();
            foreach (Language::getLanguages() as $language) {
                if (Tools::strtolower($language['iso_code']) == 'fr') {
                    $order_state->name[$language['id_lang']] = 'En attente de paiement Braintree';
                } else {
                    $order_state->name[$language['id_lang']] = 'Awaiting for Braintree payment';
                }
            }
            $order_state->send_email = false;
            $order_state->color = '#4169E1';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            if ($order_state->add()) {
                $source = _PS_MODULE_DIR_.'paypal/views/img/os_braintree.png';
                $destination = _PS_ROOT_DIR_.'/img/os/'.(int) $order_state->id.'.gif';
                copy($source, $destination);
            }
            Configuration::updateValue('PAYPAL_BRAINTREE_OS_AWAITING', (int) $order_state->id);
        }
        if (!Configuration::get('PAYPAL_BRAINTREE_OS_AWAITING_VALIDATION')
            || !Validate::isLoadedObject(new OrderState(Configuration::get('PAYPAL_BRAINTREE_OS_AWAITING_VALIDATION')))) {
            $order_state = new OrderState();
            $order_state->name = array();
            foreach (Language::getLanguages() as $language) {
                if (Tools::strtolower($language['iso_code']) == 'fr') {
                    $order_state->name[$language['id_lang']] = 'En attente de validation Braintree';
                } else {
                    $order_state->name[$language['id_lang']] = 'Awaiting for Braintree validation';
                }
            }
            $order_state->send_email = false;
            $order_state->color = '#4169E1';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            if ($order_state->add()) {
                $source = _PS_MODULE_DIR_.'paypal/views/img/os_braintree.png';
                $destination = _PS_ROOT_DIR_.'/img/os/'.(int) $order_state->id.'.gif';
                copy($source, $destination);
            }
            Configuration::updateValue('PAYPAL_BRAINTREE_OS_AWAITING_VALIDATION', (int) $order_state->id);
        }
        return true;
    }


    public function uninstall()
    {
        // Uninstall default
        if (!parent::uninstall()) {
            return false;
        }
        return true;
    }

    public function getUrl()
    {
        if (Configuration::get('PAYPAL_SANDBOX')) {
            return 'https://www.sandbox.paypal.com/';
        } else {
            return 'https://www.paypal.com/';
        }
    }

    /**
     * Get url for BT
     * @return string
     */
    public function getUrlBt()
    {
        if (Configuration::get('PAYPAL_SANDBOX')) {
            return 'https://sandbox.pp-ps-auth.com/';
        } else {
            return 'https://pp-ps-auth.com/';
        }
    }

    public function hookDisplayShoppingCartFooter()
    {
        if ('cart' !== $this->context->controller->php_self
            || (Configuration::get('PAYPAL_METHOD') != 'EC' && Configuration::get('PAYPAL_METHOD') != 'PPP')
            || !Configuration::get('PAYPAL_EXPRESS_CHECKOUT_SHORTCUT_CART')) {
            return false;
        }
        $method = AbstractMethodPaypal::load(Configuration::get('PAYPAL_METHOD'));
        return $method->renderExpressCheckoutShortCut($this->context, Configuration::get('PAYPAL_METHOD'), 'cart');
    }

    /**
     * Check requirement before method activation
     */
    private function _checkRequirements($ajax = false)
    {
        $requirements = '';
        if (!Configuration::get('PS_COUNTRY_DEFAULT')) {
            $link = $this->context->link->getAdminLink('AdminLocalization', true);
            if ($ajax && strpos($this->context->link->getAdminLink('AdminLocalization', true), '/') == 0) {
                $link = Tools::substr($this->context->link->getAdminLink('AdminLocalization', true), 1);
            }
            $requirements .= $this->displayError($this->l('To activate a payment solution, please select your default country on the following page:').
            '<a target="_blank" href="'.$link.'"> '.$this->l('Localization').'</a>');
        }
        if ($tls_check = $this->_checkTLSVersion()) {
            $requirements .= $this->displayError($this->l('Tls verification failed.').' '.$tls_check);
        }
        return $requirements;
    }

    /**
     * Check TLS version 1.2 compability : CURL request to server
     */
    private function _checkTLSVersion()
    {
        $error = '';
        $paypal = Module::getInstanceByName('paypal');
        if (defined('CURL_SSLVERSION_TLSv1_2')) {
            $tls_server = $this->context->link->getModuleLink('paypal', 'tlscurltestserver');
            $curl = curl_init($tls_server);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
            $response = curl_exec($curl);
            if ($response != 'ok') {
                $curl_info = curl_getinfo($curl);
                if ($curl_info['http_code'] == 401) {
                    $error = $paypal->l('401 Unauthorized. Please note that the TLS verification can not be done if you have an htaccess password protection enabled on your web site.');
                } else {
                    $error = curl_error($curl);
                }
            }
        } else {
            if (version_compare(curl_version()['version'], '7.34.0', '<')) {
                $error = $paypal->l(' You are using an old version of cURL. Please update your cURL extension to version 7.34.0 or higher.');
            } else {
                $error = $paypal->l('TLS version is not compatible');
            }
        }
        return $error;
    }

    /**
     * Ajax request to check requirements
     */
    public function ajaxProcessCheckRequirements()
    {
        $validation = $this->_checkRequirements(true);

        die(json_encode($validation));
    }

    public function getContent()
    {
        $requirements = '';
        if (!Configuration::get('PAYPAL_REQUIREMENTS')) {
            $requirements = $this->_checkRequirements();
            Configuration::updateValue('PAYPAL_REQUIREMENTS', 1);
        }
        $this->_postProcess();
        $country_default = Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT'));

        $lang = $this->context->country->iso_code;
        $img_esc = $this->_path."/views/img/ECShortcut/".Tools::strtolower($lang)."/checkout.png";
        if (!file_exists(_PS_ROOT_DIR_.$img_esc)) {
            $img_esc = "/modules/paypal/views/img/ECShortcut/us/checkout.png";
        }

        $this->context->smarty->assign(array(
            'path' => $this->_path,
            'active_products' => $this->express_checkout,
            'return_url' => $this->module_link,
            'country' => Country::getNameById($this->context->language->id, $this->context->country->id),
            'localization' => $this->context->link->getAdminLink('AdminLocalization', true),
            'preference' => $this->context->link->getAdminLink('AdminPreferences', true),
            'paypal_card' => Configuration::get('PAYPAL_API_CARD'),
            'iso_code' => $lang,
            'img_checkout' => $img_esc,
            'PAYPAL_SANDBOX_CLIENTID' => Configuration::get('PAYPAL_SANDBOX_CLIENTID'),
            'PAYPAL_SANDBOX_SECRET' => Configuration::get('PAYPAL_SANDBOX_SECRET'),
            'PAYPAL_LIVE_CLIENTID' => Configuration::get('PAYPAL_LIVE_CLIENTID'),
            'PAYPAL_LIVE_SECRET' => Configuration::get('PAYPAL_LIVE_SECRET'),
            'ssl_active' => Configuration::get('PS_SSL_ENABLED'),
            'country_iso' => $this->context->country->iso_code,
            'mode' => Configuration::get('PAYPAL_SANDBOX')  ? 'SANDBOX' : 'LIVE',
            'AdminPaypalProcessLogger_link' => $this->context->link->getAdminLink('AdminPaypalProcessLogger'),
        ));


        if (getenv('PLATEFORM') != 'PSREADY' && in_array($country_default, $this->bt_countries)) {
            $this->context->smarty->assign(array(
                'braintree_available' => true,
            ));
        } elseif ($country_default == "DE") {
            $this->context->smarty->assign(array(
                'ppp_available' => true,
            ));
        }

        if (Configuration::get('PAYPAL_METHOD') == 'BT') {
            $hint = $this->l('Set up a test environment in your Braintree account (only if you are a developer)');
        } else {
            $hint = $this->l('Set up a test environment in your PayPal account (only if you are a developer)');
        }

        $fields_form = array();
        $inputs = array(
            array(
                'type' => 'switch',
                'label' => $this->l('Activate sandbox'),
                'name' => 'paypal_sandbox',
                'is_bool' => true,
                'hint' => $hint,
                'values' => array(
                    array(
                        'id' => 'paypal_sandbox_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ),
                    array(
                        'id' => 'paypal_sandbox_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    )
                ),
            ),
        );
        $fields_value = array(
            'paypal_sandbox' => Configuration::get('PAYPAL_SANDBOX'),
        );

        $method_name = Configuration::get('PAYPAL_METHOD');
        $config = '';
        if ($method_name) {
            $method = AbstractMethodPaypal::load($method_name);

            $config = $method->getConfig($this);
            $inputs = array_merge($inputs, $config['inputs']);
            $fields_value = array_merge($fields_value, $config['fields_value']);
        }

        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('MODULE SETTINGS'),
                'icon' => 'icon-cogs',
            ),
            'input' => $inputs,
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right button',
            ),
        );
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = 'main_form';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;
        $helper->submit_action = 'paypal_config';
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->tpl_vars = array(
            'fields_value' => $fields_value,
            'id_language' => $this->context->language->id,
            'back_url' => $this->module_link.'#paypal_params'
        );
        $form = $helper->generateForm($fields_form);


        if ($this->errors) {
            $this->message .= $this->errors;
        } elseif (Configuration::get('PAYPAL_METHOD') && Configuration::get('PAYPAL_SANDBOX') == 1) {
            if (Configuration::get('PAYPAL_METHOD') == 'BT') {
                $this->message .= $this->display(__FILE__, 'views/templates/admin/_partials/messages/warningSandboxBraintree.tpl');
            } else {
                $this->message .= $this->display(__FILE__, 'views/templates/admin/_partials/messages/warningSandboxPayPal.tpl');
            }
        } elseif (Configuration::get('PAYPAL_METHOD') && Configuration::get('PAYPAL_SANDBOX') == 0) {
            if (Configuration::get('PAYPAL_METHOD') == 'BT') {
                $this->message .= $this->displayConfirmation($this->l('Your Braintree account is properly connected, you can now receive payments'));
            } else {
                $this->message .= $this->displayConfirmation($this->l('Your PayPal account is properly connected, you can now receive payments'));
            }
        }
        $this->context->controller->addCSS($this->_path.'views/css/paypal-bo.css', 'all');

        $result = $this->message;

        $result .= $this->display(__FILE__, 'views/templates/admin/configuration.tpl').$form;
        if (isset($config['short_cut'])) {
            $result .= $config['short_cut'];
        }
        if (isset($config['form'])) {
            $result .= $config['form'];
        }

        return $requirements.$result;
    }

    private function _postProcess()
    {
        if (Tools::isSubmit('paypal_config')) {
            Configuration::updateValue('PAYPAL_SANDBOX', Tools::getValue('paypal_sandbox'));
        }

        if (Tools::getValue('method')) {
            $method_name = Tools::getValue('method');
        } elseif (Tools::getValue('active_method')) {
            $method_name = Tools::getValue('active_method');
        } else {
            $method_name = Configuration::get('PAYPAL_METHOD');
        }

        if ($method_name) {
            $method = AbstractMethodPaypal::load($method_name);
            $method->setConfig(Tools::getAllValues());
        }
        $this->checkPaypalStats();
    }
    /**
     * Get url for BT onboarding
     * @return string
     */
    public function getBtConnectUrl()
    {
        $redirect_link = $this->module_link.'&active_method='.Tools::getValue('method');
        $connect_params = array(
            'user_country' => $this->context->country->iso_code,
            'user_email' => Configuration::get('PS_SHOP_EMAIL'),
            'business_name' => Configuration::get('PS_SHOP_NAME'),
            'redirect_url' => str_replace("http://", "https://", $redirect_link),
        );
        $sdk = new BraintreeSDK(Configuration::get('PAYPAL_SANDBOX'));
        return $sdk->getUrlConnect($connect_params);
    }

    public function hookPaymentOptions($params)
    {
        $is_virtual = 0;
        foreach ($params['cart']->getProducts() as $key => $product) {
            if ($product['is_virtual']) {
                $is_virtual = 1;
                break;
            }
        }

        $method_active = Configuration::get('PAYPAL_METHOD');
        $payments_options = array();
        $mode = Configuration::get('PAYPAL_SANDBOX') ? 'SANDBOX' : 'LIVE';

        switch ($method_active) {
            case 'EC':
                if (!Configuration::get('PAYPAL_SANDBOX') && (Configuration::get('PAYPAL_USERNAME_LIVE') && Configuration::get('PAYPAL_PSWD_LIVE') && Configuration::get('PAYPAL_PSWD_LIVE'))
                    || (Configuration::get('PAYPAL_SANDBOX') && (Configuration::get('PAYPAL_USERNAME_SANDBOX') && Configuration::get('PAYPAL_PSWD_SANDBOX') && Configuration::get('PAYPAL_SIGNATURE_SANDBOX')))) {
                    $payment_options = new PaymentOption();
                    $action_text = $this->l('Pay with Paypal');
                    $payment_options->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/views/img/paypal_sm.png'));
                    $payment_options->setModuleName($this->name);
                    if (Configuration::get('PAYPAL_API_ADVANTAGES')) {
                        $action_text .= ' | '.$this->l('It\'s easy, simple and secure');
                    }
                    $this->context->smarty->assign(array(
                        'path' => $this->_path,
                    ));
                    $payment_options->setCallToActionText($action_text);
                    if (Configuration::get('PAYPAL_EXPRESS_CHECKOUT_IN_CONTEXT')) {
                        $payment_options->setAction('javascript:ECInContext()');
                    } else {
                        $payment_options->setAction($this->context->link->getModuleLink($this->name, 'ecInit', array('credit_card'=>'0'), true));
                    }
                    if (!$is_virtual) {
                        $payment_options->setAdditionalInformation($this->context->smarty->fetch('module:paypal/views/templates/front/payment_infos.tpl'));
                    }
                    $payments_options[] = $payment_options;

                    if (Configuration::get('PAYPAL_API_CARD')) {
                        $payment_options = new PaymentOption();
                        $action_text = $this->l('Pay with debit or credit card');
                        $payment_options->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/views/img/logo_card.png'));
                        $payment_options->setCallToActionText($action_text);
                        $payment_options->setModuleName($this->name);
                        $payment_options->setAction($this->context->link->getModuleLink($this->name, 'ecInit', array('credit_card'=>'1'), true));
                        $payment_options->setAdditionalInformation($this->context->smarty->fetch('module:paypal/views/templates/front/payment_infos_card.tpl'));
                        $payments_options[] = $payment_options;
                    }
                    if ((Configuration::get('PAYPAL_EXPRESS_CHECKOUT_SHORTCUT') || Configuration::get('PAYPAL_EXPRESS_CHECKOUT_SHORTCUT_CART')) && isset($this->context->cookie->paypal_ecs)) {
                        $payment_options = new PaymentOption();
                        $action_text = $this->l('Pay with paypal express checkout');
                        $payment_options->setCallToActionText($action_text);
                        $payment_options->setModuleName('express_checkout_schortcut');
                        $payment_options->setAction($this->context->link->getModuleLink($this->name, 'ecValidation', array('short_cut'=>'1'), true));
                        $this->context->smarty->assign(array(
                            'paypal_account_email' => $this->context->cookie->paypal_ecs_email,
                        ));
                        $payment_options->setAdditionalInformation($this->context->smarty->fetch('module:paypal/views/templates/front/payment_sc.tpl'));
                        $payments_options[] = $payment_options;
                    }
                }
                break;
            case 'BT':
                $merchant_accounts = Tools::jsonDecode(Configuration::get('PAYPAL_'.$mode.'_BRAINTREE_ACCOUNT_ID'));
                $curr = context::getContext()->currency->iso_code;
                if (!isset($merchant_accounts->$curr)) {
                    return $payments_options;
                }
                if (Configuration::get('PAYPAL_BRAINTREE_ENABLED')) {
                    if (Configuration::get('PAYPAL_BY_BRAINTREE')) {
                        $embeddedOption = new PaymentOption();
                        $action_text = $this->l('Pay with paypal');
                        if (Configuration::get('PAYPAL_API_ADVANTAGES')) {
                            $action_text .= ' | '.$this->l('It\'s easy, simple and secure');
                        }
                        $embeddedOption->setCallToActionText($action_text)
                            ->setForm($this->generateFormPaypalBt());
                        $embeddedOption->setModuleName('braintree');
                        $payments_options[] = $embeddedOption;
                    }

                    $embeddedOption = new PaymentOption();
                    $embeddedOption->setCallToActionText($this->l('Pay with card'))
                        ->setAdditionalInformation($this->generateFormBt())
                        ->setAction('javascript:BTSubmitPayment();')
                        ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/views/img/mini-cards.png'));
                    $embeddedOption->setModuleName('braintree');

                    $payments_options[] = $embeddedOption;
                }
                break;
            case 'PPP':
                if (Configuration::get('PAYPAL_PLUS_ENABLED') && $this->assignInfoPaypalPlus()) {
                    $payment_options = new PaymentOption();
                    $action_text = $this->l('Pay with PayPal Plus');
                    if (Configuration::get('PAYPAL_API_ADVANTAGES')) {
                        $action_text .= ' | '.$this->l('It\'s easy, simple and secure');
                    }
                    $payment_options->setCallToActionText($action_text);
                    $payment_options->setModuleName('paypal_plus');
                    $payment_options->setAction('javascript:doPatchPPP();');
                    try {
                        $payment_options->setAdditionalInformation($this->context->smarty->fetch('module:paypal/views/templates/front/payment_ppp.tpl'));
                    } catch (Exception $e) {
                        die($e);
                    }
                    $payments_options[] = $payment_options;
                    if ((Configuration::get('PAYPAL_EXPRESS_CHECKOUT_SHORTCUT') || Configuration::get('PAYPAL_EXPRESS_CHECKOUT_SHORTCUT_CART')) && isset($this->context->cookie->paypal_pSc)) {
                        $payment_options = new PaymentOption();
                        $action_text = $this->l('Pay with paypal plus shortcut');
                        $payment_options->setCallToActionText($action_text);
                        $payment_options->setModuleName('paypal_plus_schortcut');
                        $payment_options->setAction($this->context->link->getModuleLink($this->name, 'pppValidation', array('short_cut'=>'1'), true));
                        $this->context->smarty->assign(array(
                            'paypal_account_email' => $this->context->cookie->paypal_pSc_email,
                        ));
                        $payment_options->setAdditionalInformation($this->context->smarty->fetch('module:paypal/views/templates/front/payment_sc.tpl'));
                        $payments_options[] = $payment_options;
                    }
                }

                break;
        }

        return $payments_options;
    }

    public function hookHeader()
    {
        if (Tools::getValue('controller') == "order") {
            $active = false;
            $modules = Hook::getHookModuleExecList('paymentOptions');
            if (empty($modules)) {
                return;
            }
            foreach ($modules as $module) {
                if ($module['module'] == 'paypal') {
                    $active = true;
                }
            }
            if (!$active) {
                return;
            }

            if (Configuration::get('PAYPAL_METHOD') == 'BT') {
                if (Configuration::get('PAYPAL_BRAINTREE_ENABLED')) {
                    $this->context->controller->addJqueryPlugin('fancybox');
                    $this->context->controller->registerJavascript($this->name . '-braintreegateway-client', 'https://js.braintreegateway.com/web/3.24.0/js/client.min.js', array('server' => 'remote'));
                    $this->context->controller->registerJavascript($this->name . '-braintreegateway-hosted', 'https://js.braintreegateway.com/web/3.24.0/js/hosted-fields.min.js', array('server' => 'remote'));
                    $this->context->controller->registerJavascript($this->name . '-braintreegateway-data', 'https://js.braintreegateway.com/web/3.24.0/js/data-collector.min.js', array('server' => 'remote'));
                    $this->context->controller->registerJavascript($this->name . '-braintreegateway-3ds', 'https://js.braintreegateway.com/web/3.24.0/js/three-d-secure.min.js', array('server' => 'remote'));
                    $this->context->controller->registerStylesheet($this->name . '-braintreecss', 'modules/' . $this->name . '/views/css/braintree.css');
                    $this->context->controller->registerJavascript($this->name . '-braintreejs', 'modules/' . $this->name . '/views/js/payment_bt.js');
                }
                if (Configuration::get('PAYPAL_BY_BRAINTREE')) {
                    $this->context->controller->registerJavascript($this->name . '-pp-braintree-checkout', 'https://www.paypalobjects.com/api/checkout.js', array('server' => 'remote'));
                    $this->context->controller->registerJavascript($this->name . '-pp-braintree-checkout-min', 'https://js.braintreegateway.com/web/3.24.0/js/paypal-checkout.min.js', array('server' => 'remote'));
                    $this->context->controller->registerJavascript($this->name . '-pp-braintreejs', 'modules/' . $this->name . '/views/js/payment_pbt.js');
                }
            }
            if ((Configuration::get('PAYPAL_EXPRESS_CHECKOUT_SHORTCUT') || Configuration::get('PAYPAL_EXPRESS_CHECKOUT_SHORTCUT_CART')) && (isset($this->context->cookie->paypal_ecs) || isset($this->context->cookie->paypal_pSc))) {
                $this->context->controller->registerJavascript($this->name . '-paypal-ec-sc', 'modules/' . $this->name . '/views/js/shortcut_payment.js');
            }
            if (Configuration::get('PAYPAL_METHOD') == 'EC' && Configuration::get('PAYPAL_EXPRESS_CHECKOUT_IN_CONTEXT')) {
                $environment = (Configuration::get('PAYPAL_SANDBOX')?'sandbox':'live');
                Media::addJsDef(array(
                    'environment' => $environment,
                    'merchant_id' => Configuration::get('PAYPAL_MERCHANT_ID_'.Tools::strtoupper($environment)),
                    'url_token'   => $this->context->link->getModuleLink($this->name, 'ecInit', array('credit_card'=>'0','getToken'=>1), true),
                ));
                $this->context->controller->registerJavascript($this->name . '-paypal-checkout', 'https://www.paypalobjects.com/api/checkout.js', array('server' => 'remote'));
                $this->context->controller->registerJavascript($this->name . '-paypal-checkout-in-context', 'modules/' . $this->name . '/views/js/ec_in_context.js');
            }
            if (Configuration::get('PAYPAL_METHOD') == 'PPP' && Configuration::get('PAYPAL_PLUS_ENABLED')) {
                $this->context->controller->registerJavascript($this->name . '-plus-minjs', 'https://www.paypalobjects.com/webstatic/ppplus/ppplus.min.js', array('server' => 'remote'));
                $this->context->controller->registerJavascript($this->name . '-plus-payment-js', 'modules/' . $this->name . '/views/js/payment_ppp.js');
                $this->context->controller->addJqueryPlugin('fancybox');
            }
        }
        if ((Tools::getValue('controller') == "product" && Configuration::get('PAYPAL_EXPRESS_CHECKOUT_SHORTCUT'))
        || (Tools::getValue('controller') == "cart" && Configuration::get('PAYPAL_EXPRESS_CHECKOUT_SHORTCUT_CART'))) {
            if (Configuration::get('PAYPAL_EXPRESS_CHECKOUT_IN_CONTEXT') && Configuration::get('PAYPAL_METHOD') == 'EC') {
                $environment = (Configuration::get('PAYPAL_SANDBOX')?'sandbox':'live');
                Media::addJsDef(array(
                    'ec_sc_in_context' => 1,
                    'ec_sc_environment' => $environment,
                    'merchant_id' => Configuration::get('PAYPAL_MERCHANT_ID_'.Tools::strtoupper($environment)),
                    'ec_sc_action_url'   => $this->context->link->getModuleLink($this->name, 'ScInit', array('credit_card'=>'0','getToken'=>1), true),
                ));
            }
            Media::addJsDef(array(
                'sc_init_url'   => $this->context->link->getModuleLink($this->name, 'ScInit', array(), true),
            ));
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (Configuration::get('PAYPAL_METHOD') == 'BT') {
            $diff_cron_time = date_diff(date_create('now'), date_create(Configuration::get('PAYPAL_CRON_TIME')));
            if ($diff_cron_time->d > 0 || $diff_cron_time->h > 4 || true) {
                Configuration::updateValue('PAYPAL_CRON_TIME', date('Y-m-d H:i:s'));
                $bt_orders = PaypalOrder::getPaypalBtOrdersIds();
                if ($bt_orders) {
                    $method = AbstractMethodPaypal::load('BT');
                    $transactions = $method->searchTransactions($bt_orders);
                    foreach ($transactions as $transaction) {
                        $paypal_order_id = PaypalOrder::getIdOrderByTransactionId($transaction->id);
                        $paypal_order = PaypalOrder::loadByOrderId($paypal_order_id);
                        $ps_order = new Order($paypal_order_id);
                        $paid_state  = Configuration::get('PS_OS_PAYMENT');
                        $ps_order_details = OrderDetail::getList($paypal_order_id);
                        foreach ($ps_order_details as $order_detail) {
                            // Switch to back order if needed
                            $product_stock = StockAvailable::getQuantityAvailableByProduct($order_detail['product_id'], $order_detail['product_attribute_id']);
                            if (Configuration::get('PS_STOCK_MANAGEMENT') && $product_stock <= 0) {
                                $paid_state  = Configuration::get('PS_OS_OUTOFSTOCK_PAID');
                            }
                        }
                        switch ($transaction->status) {
                            case 'declined':
                                if ($paypal_order->payment_status != "declined") {
                                    $paypal_order->payment_status = $transaction->status;
                                    $paypal_order->update();
                                    $ps_order->setCurrentState(Configuration::get('PS_OS_ERROR'));
                                }
                                break;
                            case 'settled':
                                if ($paypal_order->payment_status != "settled") {
                                    $paypal_order->payment_status = $transaction->status;
                                    $paypal_order->update();
                                    $ps_order->setCurrentState($paid_state);
                                    $this->setTransactionId($ps_order, $transaction->id);
                                }
                                break;
                            case 'settling': // waiting
                                // do nothing and check later one more time
                                break;
                            case 'submit_for_settlement': //waiting
                                // do nothing and check later one more time
                                break;
                            default:
                                // do nothing and check later one more time
                                break;
                        }
                    }
                }
            }
        }
    }

    /**
     * Get url for BT onboarding
     * @param object $ps_order PS order object
     * @param string $transaction_id payment transaction ID
     */
    public function setTransactionId($ps_order, $transaction_id)
    {
        Db::getInstance()->update('order_payment', array(
            'transaction_id' => pSQL($transaction_id),
        ), 'order_reference = "'.pSQL($ps_order->reference).'"');
    }

    public function hookActionObjectCurrencyAddAfter($params)
    {
        if (Configuration::get('PAYPAL_METHOD') == 'BT') {
            $mode = Configuration::get('PAYPAL_SANDBOX') ? 'SANDBOX' : 'LIVE';
            $merchant_accounts = (array)Tools::jsonDecode(Configuration::get('PAYPAL_' . $mode . '_BRAINTREE_ACCOUNT_ID'));
            $method_bt = AbstractMethodPaypal::load('BT');
            $merchant_account = $method_bt->createForCurrency($params['object']->iso_code);

            if ($merchant_account) {
                $merchant_accounts[$params['object']->iso_code] = $merchant_account[$params['object']->iso_code];
                Configuration::updateValue('PAYPAL_' . $mode . '_BRAINTREE_ACCOUNT_ID', Tools::jsonEncode($merchant_accounts));
            }
        }
    }

    /**
     * Assign form data for Paypal Plus payment option
     * @return boolean
     */
    protected function assignInfoPaypalPlus()
    {
        $ppplus = AbstractMethodPaypal::load('PPP');
        try {
            $approval_url = $ppplus->init();
            $this->context->cookie->__set('paypal_plus_payment', $ppplus->paymentId);
        } catch (Exception $e) {
            return false;
        }
        $address_invoice = new Address($this->context->cart->id_address_invoice);
        $country_invoice = new Country($address_invoice->id_country);

        $this->context->smarty->assign(array(
            'pppSubmitUrl'=> $this->context->link->getModuleLink('paypal', 'pppValidation', array(), true),
            'approval_url_ppp'=> $approval_url,
            'baseDir' => $this->context->link->getBaseLink($this->context->shop->id, true),
            'path' => $this->_path,
            'mode' => Configuration::get('PAYPAL_SANDBOX')  ? 'sandbox' : 'live',
            'ppp_language_iso_code' => $this->context->language->iso_code,
            'ppp_country_iso_code' => $country_invoice->iso_code,
            'ajax_patch_url' => $this->context->link->getModuleLink('paypal', 'pppPatch', array(), true),
        ));
        return true;
    }

    /**
     * Display form for BT paypal payment option
     * @return string
     */
    protected function generateFormPaypalBt()
    {
        $amount = $this->context->cart->getOrderTotal();

        $braintree = AbstractMethodPaypal::load('BT');
        $clientToken = $braintree->init();

        if (isset($clientToken['error_code'])) {
            $this->context->smarty->assign(array(
                'init_error'=> $this->l('Error Braintree initialization ').$clientToken['error_code'].' : '.$clientToken['error_msg'],
            ));
        }

        $this->context->smarty->assign(array(
            'braintreeToken'=> $clientToken,
            'braintreeSubmitUrl'=> $this->context->link->getModuleLink('paypal', 'btValidation', array(), true),
            'braintreeAmount'=> $amount,
            'baseDir' => $this->context->link->getBaseLink($this->context->shop->id, true),
            'path' => $this->_path,
            'mode' => $braintree->mode == 'SANDBOX' ? Tools::strtolower($braintree->mode) : 'production',
            'bt_method' => BT_PAYPAL_PAYMENT,
            'active_vaulting'=> Configuration::get('PAYPAL_VAULTING'),
            'currency' => $this->context->currency->iso_code,
        ));

        if (Configuration::get('PAYPAL_VAULTING')) {
            $payment_methods = PaypalVaulting::getCustomerMethods($this->context->customer->id, BT_PAYPAL_PAYMENT);
            $this->context->smarty->assign(array(
                'payment_methods' => $payment_methods,
            ));
        }

        return $this->context->smarty->fetch('module:paypal/views/templates/front/payment_pb.tpl');
    }

    /**
     * Display form for BT cards payment option
     * @return string
     */
    protected function generateFormBt()
    {
        $amount = $this->context->cart->getOrderTotal();
        $braintree = AbstractMethodPaypal::load('BT');

        $clientToken = $braintree->init();

        if (isset($clientToken['error_code'])) {
            $this->context->smarty->assign(array(
                'init_error'=> $this->l('Error Braintree initialization ').$clientToken['error_code'].' : '.$clientToken['error_msg'],
            ));
        }
        $check3DS = 0;
        $required_3ds_amount = Tools::convertPrice(Configuration::get('PAYPAL_3D_SECURE_AMOUNT'), Currency::getCurrencyInstance((int)$this->context->currency->id));
        if (Configuration::get('PAYPAL_USE_3D_SECURE') && $amount > $required_3ds_amount) {
            $check3DS = 1;
        }

        if (Configuration::get('PAYPAL_VAULTING')) {
            $payment_methods = PaypalVaulting::getCustomerMethods($this->context->customer->id, BT_CARD_PAYMENT);
            if (Configuration::get('PAYPAL_USE_3D_SECURE') && $amount > $required_3ds_amount) {
                foreach ($payment_methods as $key => $method) {
                    $nonce = $braintree->createMethodNonce($method['token']);
                    $payment_methods[$key]['nonce'] = $nonce;
                }
            }

            $this->context->smarty->assign(array(
                'active_vaulting'=> true,
                'payment_methods' => $payment_methods,
            ));
        }
        $this->context->smarty->assign(array(
            'error_msg'=> Tools::getValue('bt_error_msg'),
            'braintreeToken'=> $clientToken,
            'braintreeSubmitUrl'=> $this->context->link->getModuleLink('paypal', 'btValidation', array(), true),
            'braintreeAmount'=> $amount,
            'check3Dsecure'=> $check3DS,
            'baseDir' => $this->context->link->getBaseLink($this->context->shop->id, true),
            'method_bt' => BT_CARD_PAYMENT,
        ));
        return $this->context->smarty->fetch('module:paypal/views/templates/front/payment_bt.tpl');
    }

    public function hookPaymentReturn($params)
    {
    }

    public function hookDisplayOrderConfirmation($params)
    {
        $paypal_order = PaypalOrder::loadByOrderId($params['order']->id);
        if (!Validate::isLoadedObject($paypal_order)) {
            return;
        }

        $this->context->smarty->assign(array(
            'transaction_id' => $paypal_order->id_transaction,
            'method' => $paypal_order->method,
        ));
        if ($paypal_order->method == 'PPP' && $paypal_order->payment_tool == 'PAY_UPON_INVOICE') {
            $method = AbstractMethodPaypal::load('PPP');
            try {
                $this->context->smarty->assign('ppp_information', $method->getInstructionInfo($paypal_order->id_payment));
            } catch (Exception $e) {
                $this->context->smarty->assign('error_msg', $this->l('We are not able to verify if payment was successful. Please check if you have received confirmation from PayPal.'));
            }
        }
        $this->context->controller->registerJavascript($this->name.'-order_confirmation_js', $this->_path.'/views/js/order_confirmation.js');
        return $this->context->smarty->fetch('module:paypal/views/templates/hook/order_confirmation.tpl');
    }


    public function hookDisplayReassurance()
    {
        if ('product' !== $this->context->controller->php_self || !Configuration::get('PAYPAL_EXPRESS_CHECKOUT_SHORTCUT') || (Configuration::get('PAYPAL_METHOD') != 'EC' && Configuration::get('PAYPAL_METHOD') != 'PPP')) {
            return false;
        }
        $method = AbstractMethodPaypal::load(Configuration::get('PAYPAL_METHOD'));
        return $method->renderExpressCheckoutShortCut($this->context, Configuration::get('PAYPAL_METHOD'), 'product');
    }

    /**
     * Check if we need convert currency
     * @return boolean|integer currency id
     */
    public function needConvert()
    {
        $currency_mode = Currency::getPaymentCurrenciesSpecial($this->id);
        $mode_id = $currency_mode['id_currency'];
        if ($mode_id == -2) {
            return (int)Configuration::get('PS_CURRENCY_DEFAULT');
        } elseif ($mode_id == -1) {
            return false;
        } elseif ($mode_id != $this->context->currency->id) {
            return (int)$mode_id;
        } else {
            return false;
        }
    }

    /**
     * Get payment currency iso code
     * @return string currency iso code
     */
    public function getPaymentCurrencyIso()
    {
        if ($id_currency = $this->needConvert()) {
            $currency = new Currency((int)$id_currency);
        } else {
            $currency = Context::getContext()->currency;
        }
        return $currency->iso_code;
    }

    public function validateOrder($id_cart, $id_order_state, $amount_paid, $payment_method = 'Unknown', $message = null, $transaction = array(), $currency_special = null, $dont_touch_amount = false, $secure_key = false, Shop $shop = null)
    {
        if ($this->needConvert()) {
            $amount_paid_curr = Tools::ps_round(Tools::convertPrice($amount_paid, new Currency($currency_special), true), 2);
        } else {
            $amount_paid_curr = Tools::ps_round($amount_paid, 2);
        }
        $amount_paid = Tools::ps_round($amount_paid, 2);

        $cart = new Cart((int) $id_cart);
        $total_ps = (float)$cart->getOrderTotal(true, Cart::BOTH);
        if ($amount_paid_curr > $total_ps+0.10 || $amount_paid_curr < $total_ps-0.10) {
            $total_ps = $amount_paid_curr;
        }

        try {
            parent::validateOrder(
                (int) $id_cart,
                (int) $id_order_state,
                (float) $total_ps,
                $payment_method,
                $message,
                array('transaction_id' => isset($transaction['transaction_id']) ? $transaction['transaction_id'] : ''),
                $currency_special,
                $dont_touch_amount,
                $secure_key,
                $shop
            );
        } catch (Exception $e) {
            ProcessLoggerHandler::openLogger();
            ProcessLoggerHandler::logError(
                'Order validation error : ' . $e->getMessage(),
                isset($transaction['transaction_id']) ? $transaction['transaction_id'] : null,
                null,
                (int)$id_cart,
                $this->context->shop->id,
                isset($transaction['payment_tool']) && $transaction['payment_tool'] ? $transaction['payment_tool'] : 'PayPal',
                (int)Configuration::get('PAYPAL_SANDBOX'),
                isset($transaction['date_transaction']) ? $transaction['date_transaction'] : null
            );
            ProcessLoggerHandler::closeLogger();
            $msg = $this->l('Order validation error : ').$e->getMessage().'. ';
            if (isset($transaction['transaction_id']) && $id_order_state != Configuration::get('PS_OS_ERROR')) {
                $msg .= $this->l('Attention, your payment is made. Please, contact customer support. Your transaction ID is  : ').$transaction['transaction_id'];
            }
            Tools::redirect(Context::getContext()->link->getModuleLink('paypal', 'error', array('error_msg' => $msg, 'no_retry' => true)));
        }
        ProcessLoggerHandler::openLogger();
        ProcessLoggerHandler::logInfo(
            'Payment successful',
            isset($transaction['transaction_id']) ? $transaction['transaction_id'] : null,
            $this->currentOrder,
            (int)$id_cart,
            $this->context->shop->id,
            isset($transaction['payment_tool']) && $transaction['payment_tool'] ? $transaction['payment_tool'] : 'PayPal',
            (int)Configuration::get('PAYPAL_SANDBOX'),
            isset($transaction['date_transaction']) ? $transaction['date_transaction'] : null
        );
        ProcessLoggerHandler::closeLogger();

        if (Tools::version_compare(_PS_VERSION_, '1.7.1.0', '>')) {
            $order = Order::getByCartId($id_cart);
        } else {
            $id_order = Order::getOrderByCartId($id_cart);
            $order = new Order($id_order);
        }

        if (isset($amount_paid_curr) && $amount_paid_curr != 0 && $order->total_paid != $amount_paid_curr && $this->isOneOrder($order->reference)) {
            $order->total_paid = $amount_paid_curr;
            $order->total_paid_real = $amount_paid_curr;
            $order->total_paid_tax_incl = $amount_paid_curr;
            $order->update();

            $sql = 'UPDATE `'._DB_PREFIX_.'order_payment`
		    SET `amount` = '.(float)$amount_paid_curr.'
		    WHERE  `order_reference` = "'.pSQL($order->reference).'"';
            Db::getInstance()->execute($sql);
        }

        //if there isn't a method, then we don't create PaypalOrder and PaypalCapture
        if (isset($transaction['method']) && $transaction['method']) {
            $paypal_order = new PaypalOrder();
            $paypal_order->id_order = $this->currentOrder;
            $paypal_order->id_cart = $id_cart;
            $paypal_order->id_transaction = $transaction['transaction_id'];
            $paypal_order->id_payment = $transaction['id_payment'];
            $paypal_order->payment_method = $transaction['payment_method'];
            $paypal_order->currency = $transaction['currency'];
            $paypal_order->total_paid = (float) $amount_paid;
            $paypal_order->payment_status = $transaction['payment_status'];
            $paypal_order->total_prestashop = (float) $total_ps;
            $paypal_order->method = $transaction['method'];
            $paypal_order->payment_tool = isset($transaction['payment_tool']) ? $transaction['payment_tool'] : 'PayPal';
            $paypal_order->sandbox = (int) Configuration::get('PAYPAL_SANDBOX');
            $paypal_order->save();

            if ($transaction['capture']) {
                $paypal_capture = new PaypalCapture();
                $paypal_capture->id_paypal_order = $paypal_order->id;
                $paypal_capture->save();
            }
        }
    }

    public function hookActionAdminControllerSetMedia()
    {
        if (Tools::getValue('controller') == "AdminOrders" && Tools::getValue('id_order')) {
            $paypal_order = PaypalOrder::loadByOrderId(Tools::getValue('id_order'));
            if (Validate::isLoadedObject($paypal_order)) {
                $method = $paypal_order->method == 'BT' ? $this->l('Refund Braintree') : $this->l('Refund PayPal');
                Media::addJsDefL('chb_paypal_refund', $method);
                $this->context->controller->addJS($this->_path . '/views/js/bo_order.js');
            }
        }
    }


    public function hookDisplayAdminOrder($params)
    {
        $id_order = $params['id_order'];
        $order = new Order((int)$id_order);
        $paypal_msg = '';
        $paypal_order = PaypalOrder::loadByOrderId($id_order);
        $paypal_capture = PaypalCapture::loadByOrderPayPalId($paypal_order->id);

        if (!Validate::isLoadedObject($paypal_order)) {
            return false;
        }
        if ($paypal_order->sandbox) {
            $this->context->controller->warnings[] = $this->l('[SANDBOX] Please pay attention that payment for this order was made via PayPal Sandbox mode.');
        }
        if (Tools::getValue('not_payed_capture')) {
            $paypal_msg .= $this->displayWarning(
                '<p class="paypal-warning">'.$this->l('You couldn\'t refund order, it\'s not payed yet.').'</p>'
            );
        }
        if (Tools::getValue('error_refund')) {
            $paypal_msg .= $this->displayWarning(
                '<p class="paypal-warning">'.$this->l('We have unexpected problem during refund operation. For more details please see the "PayPal" tab in the order details.').'</p>'
            );
        }
        if (Tools::getValue('cancel_failed')) {
            $paypal_msg .= $this->displayWarning(
                '<p class="paypal-warning">'.$this->l('We have unexpected problem during cancel operation. For more details please see the "PayPal" tab in the order details.').'</p>'
            );
        }
        if ($order->current_state == Configuration::get('PS_OS_REFUND') &&  $paypal_order->payment_status == 'Refunded') {
            if ($paypal_order->method == 'BT') {
                $msg = $this->l('Your order is fully refunded by Braintree.');
            } else {
                $msg = $this->l('Your order is fully refunded by PayPal.');
            }
            $paypal_msg .= $this->displayWarning(
                '<p class="paypal-warning">'.$msg.'</p>'
            );
        }

        if ($order->getCurrentOrderState()->paid == 1 && Validate::isLoadedObject($paypal_capture) && $paypal_capture->id_capture) {
            if ($paypal_order->method == 'BT') {
                $msg = $this->l('Your order is fully captured by Braintree.');
            } else {
                $msg = $this->l('Your order is fully captured by PayPal.');
            }
            $paypal_msg .= $this->displayWarning(
                '<p class="paypal-warning">'.$msg.'</p>'
            );
        }
        if (Tools::getValue('error_capture')) {
            $paypal_msg .= $this->displayWarning(
                '<p class="paypal-warning">'.$this->l('We have unexpected problem during capture operation. See massages for more details').'</p>'
            );
        }

        if ($paypal_order->total_paid != $paypal_order->total_prestashop) {
            $preferences = $this->context->link->getAdminLink('AdminPreferences', true);
            $paypal_msg .= $this->displayWarning('<p class="paypal-warning">'.$this->l('Product pricing has been modified as your rounding settings aren\'t compliant with PayPal.').' '.
                $this->l('To avoid automatic rounding to customer for PayPal payments, please update your rounding settings.').' '.
                '<a target="_blank" href="'.$preferences.'">'.$this->l('Reed more.').'</a></p>');
        }

        return $paypal_msg.$this->display(__FILE__, 'views/templates/hook/paypal_order.tpl');
    }

    public function hookActionBeforeCartUpdateQty($params)
    {
        if (isset($this->context->cookie->paypal_ecs) || isset($this->context->cookie->paypal_ecs_payerid)) {
            //unset cookie of payment init if it's no more same cart
            Context::getContext()->cookie->__unset('paypal_ecs');
            Context::getContext()->cookie->__unset('paypal_ecs_payerid');
            Context::getContext()->cookie->__unset('paypal_ecs_email');
        }
        if (isset($this->context->cookie->paypal_pSc) || isset($this->context->cookie->paypal_pSc_payerid)) {
            //unset cookie of payment init if it's no more same cart
            Context::getContext()->cookie->__unset('paypal_pSc');
            Context::getContext()->cookie->__unset('paypal_pSc_payerid');
            Context::getContext()->cookie->__unset('paypal_pSc_email');
        }
    }

    public function hookActionOrderSlipAdd($params)
    {
        if (Tools::isSubmit('doPartialRefundPaypal')) {
            $paypal_order = PaypalOrder::loadByOrderId($params['order']->id);
            if (!Validate::isLoadedObject($paypal_order)) {
                return false;
            }
            $method = AbstractMethodPaypal::load($paypal_order->method);
            $message = '';
            $ex_detailed_message = '';
            $capture = PaypalCapture::loadByOrderPayPalId($paypal_order->id);
            if (Validate::isLoadedObject($capture) && !$capture->id_capture) {
                ProcessLoggerHandler::openLogger();
                ProcessLoggerHandler::logError(
                    $this->l('You couldn\'t refund order, it\'s not payed yet.'),
                    null,
                    $paypal_order->id_order,
                    $paypal_order->id_cart,
                    $this->context->shop->id,
                    $paypal_order->payment_tool,
                    $paypal_order->sandbox
                );
                ProcessLoggerHandler::closeLogger();
                return true;
            }
            $status = '';
            if ($paypal_order->method == "BT") {
                $status = $method->getTransactionStatus($paypal_order->id_transaction);
            }

            if ($paypal_order->method == "BT" && $status == "submitted_for_settlement") {
                ProcessLoggerHandler::openLogger();
                ProcessLoggerHandler::logError(
                    $this->l('You couldn\'t refund order, it\'s not payed yet.'),
                    null,
                    $paypal_order->id_order,
                    $paypal_order->id_cart,
                    $this->context->shop->id,
                    $paypal_order->payment_tool,
                    $paypal_order->sandbox
                );
                ProcessLoggerHandler::closeLogger();
                return true;
            } else {
                try {
                    $refund_response = $method->partialRefund($params);
                } catch (PayPal\Exception\PPConnectionException $e) {
                    $ex_detailed_message = $this->l('Error connecting to ') . $e->getUrl();
                } catch (PayPal\Exception\PPMissingCredentialException $e) {
                    $ex_detailed_message = $e->errorMessage();
                } catch (PayPal\Exception\PPConfigurationException $e) {
                    $ex_detailed_message = $this->l('Invalid configuration. Please check your configuration file');
                } catch (PayPal\Exception\PayPalConnectionException $e) {
                    $decoded_message = Tools::jsonDecode($e->getData());
                    $ex_detailed_message = $decoded_message->message;
                } catch (PayPal\Exception\PayPalInvalidCredentialException $e) {
                    $ex_detailed_message = $e->errorMessage();
                } catch (PayPal\Exception\PayPalMissingCredentialException $e) {
                    $ex_detailed_message = $this->l('Invalid configuration. Please check your configuration file');
                } catch (Exception $e) {
                    $ex_detailed_message = $e->errorMessage();
                }
            }

            if (isset($refund_response) && isset($refund_response['success']) && $refund_response['success']) {
                if (Validate::isLoadedObject($capture) && $capture->id_capture) {
                    $capture->result = 'refunded';
                    $capture->save();
                }
                $paypal_order->payment_status = 'refunded';
                $paypal_order->save();
                foreach ($refund_response as $key => $msg) {
                    $message .= $key." : ".$msg.";\r";
                }
                ProcessLoggerHandler::openLogger();
                ProcessLoggerHandler::logInfo(
                    $message,
                    isset($refund_response['refund_id']) ? $refund_response['refund_id'] : null,
                    $paypal_order->id_order,
                    $paypal_order->id_cart,
                    $this->context->shop->id,
                    $paypal_order->payment_tool,
                    $paypal_order->sandbox
                );
                ProcessLoggerHandler::closeLogger();
            } elseif (isset($refund_response) && empty($refund_response) == false) {
                foreach ($refund_response as $key => $msg) {
                    $message .= $key." : ".$msg.";\r";
                }
                ProcessLoggerHandler::openLogger();
                ProcessLoggerHandler::logError(
                    $message,
                    null,
                    $paypal_order->id_order,
                    $paypal_order->id_cart,
                    $this->context->shop->id,
                    $paypal_order->payment_tool,
                    $paypal_order->sandbox
                );
                ProcessLoggerHandler::closeLogger();
            }
            if ($ex_detailed_message) {
                ProcessLoggerHandler::openLogger();
                ProcessLoggerHandler::logError(
                    $ex_detailed_message,
                    null,
                    $paypal_order->id_order,
                    $paypal_order->id_cart,
                    $this->context->shop->id,
                    $paypal_order->payment_tool,
                    $paypal_order->sandbox
                );
                ProcessLoggerHandler::closeLogger();
            }
        }
    }

    public function hookActionOrderStatusPostUpdate(&$params)
    {
        if ($params['newOrderStatus']->paid == 1) {
            $capture = PaypalCapture::getByOrderId($params['id_order']);
            $ps_order = new Order($params['id_order']);
            if ($capture['id_capture']) {
                $this->setTransactionId($ps_order, $capture['id_capture']);
            }
        }
    }


    public function hookActionOrderStatusUpdate(&$params)
    {
        /**@var $orderPayPal PaypalOrder*/
        $orderPayPal = PaypalOrder::loadByOrderId($params['id_order']);
        if (!Validate::isLoadedObject($orderPayPal)) {
            return false;
        }
        $method = AbstractMethodPaypal::load($orderPayPal->method);
        $message = '';
        $ex_detailed_message = '';
        if ($params['newOrderStatus']->id == Configuration::get('PS_OS_CANCELED')) {
            if ($orderPayPal->method == "PPP" || $orderPayPal->payment_status == "refunded") {
                return;
            }
            $paypalCapture = PaypalCapture::loadByOrderPayPalId($orderPayPal->id);
            if ($orderPayPal->method == "EC" && $orderPayPal->payment_status != "refunded" && ((!Validate::isLoadedObject($paypalCapture))
            || (Validate::isLoadedObject($paypalCapture) && $paypalCapture->id_capture))) {
                ProcessLoggerHandler::openLogger();
                ProcessLoggerHandler::logError(
                    $this->l('You canceled the order that hadn\'t been refunded yet'),
                    null,
                    $orderPayPal->id_order,
                    $orderPayPal->id_cart,
                    $this->context->shop->id,
                    $orderPayPal->payment_tool,
                    $orderPayPal->sandbox
                );
                ProcessLoggerHandler::closeLogger();
                return;
            }

            try {
                $response_void = $method->void($orderPayPal);
            } catch (PayPal\Exception\PPConnectionException $e) {
                $ex_detailed_message = $this->l('Error connecting to ') . $e->getUrl();
            } catch (PayPal\Exception\PPMissingCredentialException $e) {
                $ex_detailed_message = $e->errorMessage();
            } catch (PayPal\Exception\PPConfigurationException $e) {
                $ex_detailed_message = $this->l('Invalid configuration. Please check your configuration file');
            }
            if (isset($response_void) && isset($response_void['success']) && $response_void['success']) {
                $paypalCapture->result = 'voided';
                $paypalCapture->save();
                $orderPayPal->payment_status = 'voided';
                $orderPayPal->save();
                foreach ($response_void as $key => $msg) {
                    $message .= $key." : ".$msg.";\r";
                }
                ProcessLoggerHandler::openLogger();
                ProcessLoggerHandler::logInfo(
                    $message,
                    isset($response_void['transaction_id']) ? $response_void['transaction_id'] : null,
                    $orderPayPal->id_order,
                    $orderPayPal->id_cart,
                    $this->context->shop->id,
                    $orderPayPal->payment_tool,
                    $orderPayPal->sandbox,
                    $response_void['date_transaction']
                );
                ProcessLoggerHandler::closeLogger();
            } elseif (isset($response_void) && empty($response_void) == false) {
                foreach ($response_void as $key => $msg) {
                    $message .= $key." : ".$msg.";\r";
                }
                ProcessLoggerHandler::openLogger();
                ProcessLoggerHandler::logError(
                    $message,
                    null,
                    $orderPayPal->id_order,
                    $orderPayPal->id_cart,
                    $this->context->shop->id,
                    null,
                    $orderPayPal->sandbox
                );
                ProcessLoggerHandler::closeLogger();
                Tools::redirect($_SERVER['HTTP_REFERER'].'&cancel_failed=1');
            }

            if ($ex_detailed_message) {
                ProcessLoggerHandler::openLogger();
                ProcessLoggerHandler::logError(
                    $ex_detailed_message,
                    null,
                    $orderPayPal->id_order,
                    $orderPayPal->id_cart,
                    $this->context->shop->id,
                    $orderPayPal->payment_tool,
                    $orderPayPal->sandbox
                );
                ProcessLoggerHandler::closeLogger();
            }
        }

        if ($params['newOrderStatus']->id == Configuration::get('PS_OS_REFUND')) {
            $capture = PaypalCapture::loadByOrderPayPalId($orderPayPal->id);
            if (Validate::isLoadedObject($capture) && !$capture->id_capture) {
                ProcessLoggerHandler::openLogger();
                ProcessLoggerHandler::logError(
                    $this->l('You couldn\'t refund order, it\'s not payed yet.'),
                    null,
                    $orderPayPal->id_order,
                    $orderPayPal->id_cart,
                    $this->context->shop->id,
                    $orderPayPal->payment_tool,
                    $orderPayPal->sandbox
                );
                ProcessLoggerHandler::closeLogger();
                Tools::redirect($_SERVER['HTTP_REFERER'].'&not_payed_capture=1');
            }
            $status = '';
            if ($orderPayPal->method == "BT") {
                $status = $method->getTransactionStatus($orderPayPal);
            }

            if ($orderPayPal->method == "BT" && $status == "submitted_for_settlement") {
                try {
                    $refund_response = $method->void($orderPayPal);
                } catch (PayPal\Exception\PPConnectionException $e) {
                    $ex_detailed_message = $this->l('Error connecting to ') . $e->getUrl();
                } catch (PayPal\Exception\PPMissingCredentialException $e) {
                    $ex_detailed_message = $e->errorMessage();
                } catch (PayPal\Exception\PPConfigurationException $e) {
                    $ex_detailed_message = $this->l('Invalid configuration. Please check your configuration file');
                }
                if (isset($refund_response) && isset($refund_response['success']) && $refund_response['success']) {
                    $capture->result = 'voided';
                    $orderPayPal->payment_status = 'voided';
                    foreach ($refund_response as $key => $msg) {
                        $message .= $key." : ".$msg.";\r";
                    }
                    ProcessLoggerHandler::openLogger();
                    ProcessLoggerHandler::logInfo(
                        $message,
                        isset($refund_response['transaction_id']) ? $refund_response['transaction_id'] : null,
                        $orderPayPal->id_order,
                        $orderPayPal->id_cart,
                        $this->context->shop->id,
                        $orderPayPal->payment_tool,
                        $orderPayPal->sandbox,
                        $response_void['date_transaction']
                    );
                    ProcessLoggerHandler::closeLogger();
                }
            } else {
                try {
                    $refund_response = $method->refund($orderPayPal);
                } catch (PayPal\Exception\PPConnectionException $e) {
                    $ex_detailed_message = $this->l('Error connecting to ') . $e->getUrl();
                } catch (PayPal\Exception\PPMissingCredentialException $e) {
                    $ex_detailed_message = $e->errorMessage();
                } catch (PayPal\Exception\PPConfigurationException $e) {
                    $ex_detailed_message = $this->l('Invalid configuration. Please check your configuration file');
                } catch (PayPal\Exception\PayPalConnectionException $e) {
                    $decoded_message = Tools::jsonDecode($e->getData());
                    $ex_detailed_message = $decoded_message->message;
                } catch (PayPal\Exception\PayPalInvalidCredentialException $e) {
                    $ex_detailed_message = $e->errorMessage();
                } catch (PayPal\Exception\PayPalMissingCredentialException $e) {
                    $ex_detailed_message = $this->l('Invalid configuration. Please check your configuration file');
                } catch (Exception $e) {
                    $ex_detailed_message = $e->errorMessage();
                }

                if (isset($refund_response) && isset($refund_response['success']) && $refund_response['success']) {
                    $capture->result = 'refunded';
                    $orderPayPal->payment_status = 'refunded';
                    foreach ($refund_response as $key => $msg) {
                        $message .= $key." : ".$msg.";\r";
                    }
                    ProcessLoggerHandler::openLogger();
                    ProcessLoggerHandler::logInfo(
                        $message,
                        isset($refund_response['refund_id']) ? $refund_response['refund_id'] : null,
                        $orderPayPal->id_order,
                        $orderPayPal->id_cart,
                        $this->context->shop->id,
                        $orderPayPal->payment_tool,
                        $orderPayPal->sandbox,
                        $refund_response['date_transaction']
                    );
                    ProcessLoggerHandler::closeLogger();
                }
            }

            if (isset($refund_response) && isset($refund_response['success']) && $refund_response['success']) {
                $capture->save();
                $orderPayPal->save();
            }

            if ($ex_detailed_message) {
                ProcessLoggerHandler::openLogger();
                ProcessLoggerHandler::logError(
                    $ex_detailed_message,
                    null,
                    $orderPayPal->id_order,
                    $orderPayPal->id_cart,
                    $this->context->shop->id,
                    $orderPayPal->payment_tool,
                    $orderPayPal->sandbox
                );
                ProcessLoggerHandler::closeLogger();
            }

            if (isset($refund_response) && !isset($refund_response['already_refunded']) && !isset($refund_response['success'])) {
                foreach ($refund_response as $key => $msg) {
                    $message .= $key." : ".$msg.";\r";
                }
                ProcessLoggerHandler::openLogger();
                ProcessLoggerHandler::logError(
                    $message,
                    null,
                    $orderPayPal->id_order,
                    $orderPayPal->id_cart,
                    $this->context->shop->id,
                    $orderPayPal->payment_tool,
                    $orderPayPal->sandbox
                );
                ProcessLoggerHandler::closeLogger();
                Tools::redirect($_SERVER['HTTP_REFERER'].'&error_refund=1');
            }
        }

        if ($params['newOrderStatus']->paid == 1) {
            $capture = PaypalCapture::loadByOrderPayPalId($orderPayPal->id);
            if (!Validate::isLoadedObject($capture)) {
                return false;
            }

            try {
                $capture_response = $method->confirmCapture($orderPayPal);
            } catch (PayPal\Exception\PPConnectionException $e) {
                $ex_detailed_message = $this->l('Error connecting to ') . $e->getUrl();
            } catch (PayPal\Exception\PPMissingCredentialException $e) {
                $ex_detailed_message = $e->errorMessage();
            } catch (PayPal\Exception\PPConfigurationException $e) {
                $ex_detailed_message = $this->l('Invalid configuration. Please check your configuration file');
            }

            if (isset($capture_response['success'])) {
                $orderPayPal->payment_status = $capture_response['status'];
                $orderPayPal->save();
            }
            if ($ex_detailed_message) {
                ProcessLoggerHandler::openLogger();
                ProcessLoggerHandler::logError(
                    $ex_detailed_message,
                    null,
                    $orderPayPal->id_order,
                    $orderPayPal->id_cart,
                    $this->context->shop->id,
                    $orderPayPal->payment_tool,
                    $orderPayPal->sandbox
                );
                ProcessLoggerHandler::closeLogger();
            } elseif (isset($capture_response) && isset($capture_response['success']) && $capture_response['success']) {
                foreach ($capture_response as $key => $msg) {
                    $message .= $key." : ".$msg.";\r";
                }
                ProcessLoggerHandler::openLogger();
                ProcessLoggerHandler::logInfo(
                    $message,
                    isset($capture_response['authorization_id']) ? $capture_response['authorization_id'] : null,
                    $orderPayPal->id_order,
                    $orderPayPal->id_cart,
                    $this->context->shop->id,
                    $orderPayPal->payment_tool,
                    $orderPayPal->sandbox,
                    isset($capture_response['date_transaction']) ? $capture_response['date_transaction'] : null
                );
                ProcessLoggerHandler::closeLogger();
            }

            if (!isset($capture_response['already_captured']) && !isset($capture_response['success'])) {
                foreach ($capture_response as $key => $msg) {
                    $message .= $key." : ".$msg.";\r";
                }
                ProcessLoggerHandler::openLogger();
                ProcessLoggerHandler::logError(
                    $message,
                    null,
                    $orderPayPal->id_order,
                    $orderPayPal->id_cart,
                    $this->context->shop->id,
                    $orderPayPal->payment_tool,
                    $orderPayPal->sandbox
                );
                ProcessLoggerHandler::closeLogger();
                Tools::redirect($_SERVER['HTTP_REFERER'].'&error_capture=1');
            }
        }
    }

    /**
     * Get URL for EC onboarding
     * @return string
     */
    public function getPartnerInfo()
    {
        $return_url = $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&active_method='.Tools::getValue('method');
        if ($this->context->country->iso_code == "CN") {
            $country = "C2";
        } else {
            $country = $this->context->country->iso_code;
        }

        $partner_info = array(
            'email'         => $this->context->employee->email,
            'language'      => $this->context->language->iso_code.'_'.Tools::strtoupper($this->context->country->iso_code),
            'shop_url'      => Tools::getShopDomainSsl(true),
            'address1'      => Configuration::get('PS_SHOP_ADDR1', null, null, null, ''),
            'address2'      => Configuration::get('PS_SHOP_ADDR2', null, null, null, ''),
            'city'          => Configuration::get('PS_SHOP_CITY', null, null, null, ''),
            'country_code'  => Tools::strtoupper($country),
            'postal_code'   => Configuration::get('PS_SHOP_CODE', null, null, null, ''),
            'state'         => Configuration::get('PS_SHOP_STATE_ID', null, null, null, ''),
            'return_url'    => str_replace("http://", "https://", $return_url),
            'first_name'    => $this->context->employee->firstname,
            'last_name'     => $this->context->employee->lastname,
            'shop_name'     => Configuration::get('PS_SHOP_NAME', null, null, null, ''),
            'ref_merchant'  => 'PrestaShop_'.(getenv('PLATEFORM') == 'PSREADY' ? 'Ready':''),
            'ps_version'    => _PS_VERSION_,
            'pp_version'    => $this->version,
            'sandbox'       => Configuration::get('PAYPAL_SANDBOX') ? "true" : '',
        );

        $response = "https://partners-subscribe.prestashop.com/paypal/request.php?".http_build_query($partner_info, '', '&');

        return $response;
    }

    public function hookDisplayInvoiceLegalFreeText($params)
    {
        $paypal_order = PaypalOrder::loadByOrderId($params['order']->id);
        if (!Validate::isLoadedObject($paypal_order) || $paypal_order->method != 'PPP'
            || $paypal_order->payment_tool != 'PAY_UPON_INVOICE') {
            return;
        }

        $method = AbstractMethodPaypal::load('PPP');
        $information = $method->getInstructionInfo($paypal_order->id_payment);
        $tab = $this->l('The bank name').' : '.$information->recipient_banking_instruction->bank_name.'; 
        '.$this->l('Account holder name').' : '.$information->recipient_banking_instruction->account_holder_name.'; 
        '.$this->l('IBAN').' : '.$information->recipient_banking_instruction->international_bank_account_number.'; 
        '.$this->l('BIC').' : '.$information->recipient_banking_instruction->bank_identifier_code.'; 
        '.$this->l('Amount due / currency').' : '.$information->amount->value.' '.$information->amount->currency.';
        '.$this->l('Payment due date').' : '.$information->payment_due_date.'; 
        '.$this->l('Reference').' : '.$information->reference_number.'.';
        return $tab;
    }

    /**
     * Get decimal correspondent to payment currency
     * @return integer Number of decimal
     */
    public static function getDecimal()
    {
        $paypal = Module::getInstanceByName('paypal');
        $currency_wt_decimal = array('HUF', 'JPY', 'TWD');
        if (in_array($paypal->getPaymentCurrencyIso(), $currency_wt_decimal) ||
            (int)Configuration::get('PS_PRICE_DISPLAY_PRECISION') == 0) {
            return (int)0;
        } else {
            return (int)2;
        }
    }

    public function hookDisplayCustomerAccount()
    {
        if (Configuration::get('PAYPAL_METHOD') == 'BT' && Configuration::get('PAYPAL_VAULTING')) {
            return $this->display(__FILE__, 'my-account.tpl');
        }
    }

    public function hookDisplayMyAccountBlock()
    {
        if (Configuration::get('PAYPAL_METHOD') == 'BT' && Configuration::get('PAYPAL_VAULTING')) {
            return $this->display(__FILE__, 'my-account-footer.tpl');
        }
    }

    /**
     * Get State ID
     * @param $ship_addr_state string state code from PayPal
     * @param $ship_addr_country string delivery country iso code from PayPal
     * @return int id state
     */
    public static function getIdStateByPaypalCode($ship_addr_state, $ship_addr_country)
    {
        $id_state = 0;
        $id_country = Country::getByIso($ship_addr_country);
        if (Country::containsStates($id_country)) {
            if (isset(PayPal::$state_iso_code_matrix[$ship_addr_country])) {
                $matrix = PayPal::$state_iso_code_matrix[$ship_addr_country];
                $ship_addr_state = array_search(Tools::strtolower($ship_addr_state), array_map('strtolower', $matrix));
            }
            if ($id_state = (int)State::getIdByIso(Tools::strtoupper($ship_addr_state), $id_country)) {
                $id_state = $id_state;
            } elseif ($id_state = State::getIdByName(pSQL(trim($ship_addr_state)))) {
                $state = new State((int)$id_state);
                if ($state->id_country == $id_country) {
                    $id_state= $state->id;
                }
            }
        }
        return $id_state;
    }

    /**
     * Get delivery state code in paypal format
     * @param $address Address object
     * @return string state code
     */
    public static function getPaypalStateCode($address)
    {
        $ship_addr_state = '';
        if ($address->id_state) {
            $country = new Country((int) $address->id_country);
            $state = new State((int) $address->id_state);
            if (isset(PayPal::$state_iso_code_matrix[$country->iso_code]) &&
                empty(PayPal::$state_iso_code_matrix[$country->iso_code]) == false)
            {
                $matrix = PayPal::$state_iso_code_matrix[$country->iso_code];
                $ship_addr_state = $matrix[$state->iso_code] ? $matrix[$state->iso_code] : $matrix[$state->name];
            } else {
                $ship_addr_state = $state->iso_code;
            }
        }
        return $ship_addr_state;
    }

    public function hookDisplayAdminOrderTabOrder($params)
    {
        if ($result = $this->handleExtensionsHook(__FUNCTION__, $params)) {
            if (!is_null($result)) {
                return $result;
            }
        }
    }

    public function hookDisplayAdminOrderContentOrder($params)
    {
        $params['class_logger'] = 'PaypalLog';
        if ($result = $this->handleExtensionsHook(__FUNCTION__, $params)) {
            if (!is_null($result)) {
                return $result;
            }
        }
    }

    public function hookDisplayAdminCartsView($params)
    {
        $params['class_logger'] = 'PaypalLog';
        if ($result = $this->handleExtensionsHook(__FUNCTION__, $params)) {
            if (!is_null($result)) {
                return $result;
            }
        }
    }

    public function checkPaypalStats()
    {
        $tab = Tab::getInstanceFromClassName('AdminPaypalStats');
        if (Validate::isLoadedObject($tab)) {
            if ($tab->active && (bool)Configuration::get('PAYPAL_METHOD') == false) {
                $tab->active = false;
                $tab->save();
            } elseif ($method_payment = Configuration::get('PAYPAL_METHOD')) {
                $method = AbstractMethodPaypal::load($method_payment);
                if ($tab->active == false && $method->isConfigured() == true) {
                    $tab->active = true;
                    $tab->save();
                } elseif ($tab->active == true && $method->isConfigured() == false) {
                    $tab->active = false;
                    $tab->save();
                }
            }
        }
    }

    public function isOneOrder($order_reference)
    {
        $query = new DBQuery();
        $query->select('COUNT(*)');
        $query->from('orders');
        $query->where('reference = "' . pSQL($order_reference) . '"');
        $countOrders = (int)DB::getInstance()->getValue($query);
        return $countOrders == 1;
    }
}
