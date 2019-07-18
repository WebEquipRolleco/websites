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

//include_once(_PS_MODULE_DIR_.'paypal/vendor/braintree/braintree_php/lib/Braintree.php');
include_once 'PaypalCustomer.php';
include_once 'PaypalVaulting.php';

use PaypalAddons\classes\PaypalException;
use PaypalPPBTlib\Extensions\ProcessLogger\ProcessLoggerHandler;

/**
 * Class MethodBT
 * @see https://developers.braintreepayments.com/guides/overview BT developper documentation
 */
class MethodBT extends AbstractMethodPaypal
{
    /** @var string token*/
    public $token;

    /** @var string sandbox or live*/
    public $mode;

    /** @var  string A secure, one-time-use reference to payment information */
    private $payment_method_nonce;

    /** @var  string  BT_CARD_PAYMENT or BT_PAYPAL_PAYMENT*/
    private $payment_method_bt;

    /** @var  string Vaulted token for cards */
    private $bt_vaulting_token;

    /** @var  string Vaulted token for paypal */
    private $pbt_vaulting_token;

    /** @var  bool vaulting checkbox */
    private $save_card_in_vault;

    /** @var  bool vaulting checkbox */
    private $save_account_in_vault;

    protected $payment_method = 'Braintree';

    /**
     * @param $values array replace for tools::getValues()
     */
    public function setParameters($values)
    {
        foreach ($values as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * @see AbstractMethodPaypal::getConfig()
     */
    public function getConfig(PayPal $module)
    {
        $params = array('inputs' => array(
            array(
                'type' => 'select',
                'label' => $module->l('Payment action', get_class($this)),
                'name' => 'paypal_intent',
                'desc' => $module->l('', get_class($this)),
                'hint' => $module->l('Sale: the money moves instantly from the buyer\'s account to the seller\'s account at the time of payment. Authorization/capture: The authorized mode is a deferred mode of payment that requires the funds to be collected manually when you want to transfer the money. This mode is used if you want to ensure that you have the merchandise before depositing the money, for example. Be careful, you have 29 days to collect the funds.', get_class($this)),
                'options' => array(
                    'query' => array(
                        array(
                            'id' => 'sale',
                            'name' => $module->l('Sale', get_class($this))
                        ),
                        array(
                            'id' => 'authorization',
                            'name' => $module->l('Authorize', get_class($this))
                        )
                    ),
                    'id' => 'id',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $module->l('Show PayPal benefits to your customers', get_class($this)),
                'name' => 'paypal_show_advantage',
                'desc' => $module->l('', get_class($this)),
                'is_bool' => true,
                'hint' => $module->l('You can increase your conversion rate by presenting PayPal benefits to your customers on payment methods selection page.', get_class($this)),
                'values' => array(
                    array(
                        'id' => 'paypal_show_advantage_on',
                        'value' => 1,
                        'label' => $module->l('Enabled', get_class($this)),
                    ),
                    array(
                        'id' => 'paypal_show_advantage_off',
                        'value' => 0,
                        'label' => $module->l('Disabled', get_class($this)),
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $module->l('Accept PayPal Payments', get_class($this)),
                'name' => 'activate_paypal',
                'desc' => $module->l('', get_class($this)),
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'activate_paypal_on',
                        'value' => 1,
                        'label' => $module->l('Enabled', get_class($this)),
                    ),
                    array(
                        'id' => 'activate_paypal_off',
                        'value' => 0,
                        'label' => $module->l('Disabled', get_class($this)),
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $module->l('Enable Vault', get_class($this)),
                'name' => 'paypal_vaulting',
                'is_bool' => true,
                'hint' => $module->l('The Vault is used to process payments so your customers don\'t need to re-enter their information each time they make a purchase from you.', get_class($this)),
                'values' => array(
                    array(
                        'id' => 'paypal_vaulting_on',
                        'value' => 1,
                        'label' => $module->l('Enabled', get_class($this)),
                    ),
                    array(
                        'id' => 'paypal_vaulting_off',
                        'value' => 0,
                        'label' => $module->l('Disabled', get_class($this)),
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $module->l('Enable Card verification', get_class($this)),
                'name' => 'card_verification',
                'is_bool' => true,
                'hint' => $module->l('Card verification is a strong first-line defense against potentially fraudulent cards. It ensures that the credit card number provided is associated with a valid, open account and can be stored in the Vault and charged successfully.', get_class($this)),
                'values' => array(
                    array(
                        'id' => 'card_verification_on',
                        'value' => 1,
                        'label' => $module->l('Enabled', get_class($this)),
                    ),
                    array(
                        'id' => 'card_verification_off',
                        'value' => 0,
                        'label' => $module->l('Disabled', get_class($this)),
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $module->l('Activate 3D Secure for Braintree', get_class($this)),
                'name' => 'paypal_3DSecure',
                'desc' => $module->l('', get_class($this)),
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'paypal_3DSecure_on',
                        'value' => 1,
                        'label' => $module->l('Enabled', get_class($this)),
                    ),
                    array(
                        'id' => 'paypal_3DSecure_off',
                        'value' => 0,
                        'label' => $module->l('Disabled', get_class($this)),
                    )
                ),
            ),
            array(
                'type' => 'text',
                'label' => $module->l('Amount for 3DS in ', get_class($this)).Currency::getCurrency(Configuration::get('PS_CURRENCY_DEFAULT'))['iso_code'],
                'name' => 'paypal_3DSecure_amount',
                'hint' => $module->l('Activate 3D Secure only for orders which total is bigger that this amount in your context currency', get_class($this)),
            ),
        ));

        $params['fields_value'] = array(
            'paypal_intent' => Configuration::get('PAYPAL_API_INTENT'),
            'paypal_show_advantage' => Configuration::get('PAYPAL_API_ADVANTAGES'),
            'activate_paypal' => Configuration::get('PAYPAL_BY_BRAINTREE'),
            'paypal_3DSecure' => Configuration::get('PAYPAL_USE_3D_SECURE'),
            'paypal_3DSecure_amount' => Configuration::get('PAYPAL_3D_SECURE_AMOUNT'),
            'paypal_vaulting' => Configuration::get('PAYPAL_VAULTING'),
            'card_verification' => Configuration::get('PAYPAL_BT_CARD_VERIFICATION'),
        );
        $context = Context::getContext();
        $context->smarty->assign(array(
            'bt_paypal_active' => Configuration::get('PAYPAL_BY_BRAINTREE'),
            'bt_active' => Configuration::get('PAYPAL_BRAINTREE_ENABLED'),
        ));


        $params['form'] = $this->getMerchantCurrenciesForm($module);

        return $params;
    }

    public function getMerchantCurrenciesForm($module)
    {
        $mode = Configuration::get('PAYPAL_SANDBOX') ? 'SANDBOX' : 'LIVE';
        $merchant_accounts = (array)Tools::jsonDecode(Configuration::get('PAYPAL_'.$mode.'_BRAINTREE_ACCOUNT_ID'));

        $ps_currencies = Currency::getCurrencies();
        $fields_form2 = array();
        $fields_form2[0]['form'] = array(
            'legend' => array(
                'title' => $module->l('Braintree merchant accounts', get_class($this)),
                'icon' => 'icon-cogs',
            ),
        );
        $fields_value = array();
        foreach ($ps_currencies as $curr) {
            $fields_form2[0]['form']['input'][] =
                array(
                    'type' => 'text',
                    'label' => $module->l('Merchant account Id for ', get_class($this)).$curr['iso_code'],
                    'name' => 'braintree_curr_'.$curr['iso_code'],
                    'value' => isset($merchant_accounts[$curr['iso_code']])?$merchant_accounts[$curr['iso_code']] : ''
                );
            $fields_value['braintree_curr_'.$curr['iso_code']] =  isset($merchant_accounts[$curr['iso_code']])?$merchant_accounts[$curr['iso_code']] : '';
        }
        $fields_form2[0]['form']['submit'] = array(
            'title' => $module->l('Save', get_class($this)),
            'class' => 'btn btn-default pull-right button',
        );

        $helper = new HelperForm();
        $helper->module = $module;
        $helper->name_controller = 'bt_currency_form';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$module->name;
        $helper->title = $module->displayName;
        $helper->show_toolbar = false;
        $helper->submit_action = 'paypal_braintree_curr';
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->tpl_vars = array(
            'fields_value' => $fields_value,
            'id_language' => Context::getContext()->language->id,
            'back_url' => $module->module_link.'#paypal_params'
        );
        return $helper->generateForm($fields_form2);
    }

    /**
     * @see AbstractMethodPaypal::setConfig()
     */
    public function setConfig($params)
    {
        $mode = Configuration::get('PAYPAL_SANDBOX') ? 'SANDBOX' : 'LIVE';
        $paypal = Module::getInstanceByName($this->name);
        $ps_currencies = Currency::getCurrencies();
        $new_accounts = array();
        if (Tools::isSubmit('paypal_braintree_curr')) {
            foreach ($ps_currencies as $curr) {
                $new_accounts[$curr['iso_code']] = Tools::getValue('braintree_curr_'.$curr['iso_code']);
            }
            Configuration::updateValue('PAYPAL_'.$mode.'_BRAINTREE_ACCOUNT_ID', Tools::jsonEncode($new_accounts));
        }

        if (Tools::getValue('accessToken') && Tools::getValue('expiresAt') && Tools::getValue('refreshToken') && Tools::getValue('merchantId')) {
            Configuration::updateValue('PAYPAL_METHOD', 'BT');
            Configuration::updateValue('PAYPAL_BRAINTREE_ENABLED', 1);
            $method_bt = AbstractMethodPaypal::load('BT');
            Configuration::updateValue('PAYPAL_'.$mode.'_BRAINTREE_ACCESS_TOKEN', Tools::getValue('accessToken'));
            Configuration::updateValue('PAYPAL_'.$mode.'_BRAINTREE_EXPIRES_AT', Tools::getValue('expiresAt'));
            Configuration::updateValue('PAYPAL_'.$mode.'_BRAINTREE_REFRESH_TOKEN', Tools::getValue('refreshToken'));
            Configuration::updateValue('PAYPAL_'.$mode.'_BRAINTREE_MERCHANT_ID', Tools::getValue('merchantId'));
            $existing_merchant_accounts = $method_bt->getAllCurrency();

            $new_merchant_accounts = $method_bt->createForCurrency();

            $all_merchant_accounts = array_merge((array)$existing_merchant_accounts, (array)$new_merchant_accounts);
            unset($all_merchant_accounts[0]);
            if ($all_merchant_accounts) {
                Configuration::updateValue('PAYPAL_'.$mode.'_BRAINTREE_ACCOUNT_ID', Tools::jsonEncode($all_merchant_accounts));
            }
            Tools::redirect($paypal->module_link);
        }

        if (Tools::isSubmit('paypal_config')) {
            Configuration::updateValue('PAYPAL_API_INTENT', $params['paypal_intent']);
            Configuration::updateValue('PAYPAL_BY_BRAINTREE', $params['activate_paypal']);
            Configuration::updateValue('PAYPAL_USE_3D_SECURE', $params['paypal_3DSecure']);
            Configuration::updateValue('PAYPAL_3D_SECURE_AMOUNT', (int)$params['paypal_3DSecure_amount']);
            Configuration::updateValue('PAYPAL_API_ADVANTAGES', $params['paypal_show_advantage']);
            Configuration::updateValue('PAYPAL_VAULTING', $params['paypal_vaulting']);
            Configuration::updateValue('PAYPAL_BT_CARD_VERIFICATION', $params['card_verification']);
        }

        if (isset($params['method'])) {
            if (isset($params['with_paypal'])) {
                Configuration::updateValue('PAYPAL_BY_BRAINTREE', $params['with_paypal']);
            }
            if ((isset($params['modify']) && $params['modify']) || (Configuration::get('PAYPAL_METHOD') != $params['method'])) {
                $response = $paypal->getBtConnectUrl();
                $result = Tools::jsonDecode($response);
                if ($result->error) {
                    $paypal->errors .= $paypal->displayError($paypal->l('Error onboarding Braintree : ', get_class($this)) . $result->error);
                } elseif (isset($result->data->url_connect)) {
                    Tools::redirectLink($result->data->url_connect);
                }
            }
        }

        if ($mode == 'SANDBOX' && (!Configuration::get('PAYPAL_'.$mode.'_BRAINTREE_ACCESS_TOKEN') || !Configuration::get('PAYPAL_'.$mode.'_BRAINTREE_EXPIRES_AT')
            || !Configuration::get('PAYPAL_'.$mode.'_BRAINTREE_MERCHANT_ID'))) {
            $paypal->errors .= $paypal->displayError($paypal->l('You are trying to switch to sandbox account. You should use your test credentials. Please go to the "Products" tab and click on "Modify\' for activating the sandbox version of the selected product.', get_class($this)));
        }
        if ($mode == 'LIVE' && (!Configuration::get('PAYPAL_'.$mode.'_BRAINTREE_ACCESS_TOKEN') || !Configuration::get('PAYPAL_'.$mode.'_BRAINTREE_EXPIRES_AT')
                || !Configuration::get('PAYPAL_'.$mode.'_BRAINTREE_MERCHANT_ID'))) {
            $paypal->errors .= $paypal->displayError($paypal->l('You are trying to switch to production account. You should use your production credentials. Please go to the "Products" tab and click on "Modify\' for activating the production version of the selected product.', get_class($this)));
        }
    }

    /**
     * Init class configurations
     */
    private function initConfig($order_mode = null)
    {
        if ($order_mode !== null) {
            $this->mode = $order_mode ? 'SANDBOX' : 'LIVE';
        } else {
            $this->mode = Configuration::get('PAYPAL_SANDBOX') ? 'SANDBOX' : 'LIVE';
        }
        $this->gateway = new Braintree_Gateway(array('accessToken' => Configuration::get('PAYPAL_'.$this->mode.'_BRAINTREE_ACCESS_TOKEN')));
        $this->error = '';
    }

    /**
     * @see AbstractMethodPaypal::init()
     */
    public function init()
    {
        try {
            $this->initConfig();
            $clientToken = $this->gateway->clientToken()->generate();
            return $clientToken;
        } catch (Exception $e) {
            return array('error_code' => $e->getCode(), 'error_msg' => $e->getMessage());
        }
    }

    /**
     * Get all activated currencies from BT account
     * @return array [curr_iso_code => account_id]
     */
    public function getAllCurrency()
    {
        $this->initConfig();
        $result = array();
        try {
            $response = $this->gateway->merchantAccount()->all();
            foreach ($response as $account) {
                $result[$account->currencyIsoCode] = $account->id;
            }
        } catch (Exception $e) {
        }
        return $result;
    }

    /**
     * Create new BT account for currency added on PS
     * @param string $currency iso code
     * @return array [curr_iso_code => account_id]
     */
    public function createForCurrency($currency = null)
    {
        $this->initConfig();
        $result = array();

        if ($currency) {
            try {
                $response = $this->gateway->merchantAccount()->createForCurrency(array(
                    'currency' => $currency,
                ));
                if ($response->success) {
                    $result[$response->merchantAccount->currencyIsoCode] = $response->merchantAccount->id;
                }
            } catch (Exception $e) {
            }
        } else {
            $currencies = Currency::getCurrencies();
            foreach ($currencies as $curr) {
                try {
                    $response = $this->gateway->merchantAccount()->createForCurrency(array(
                        'currency' => $curr['iso_code'],
                    ));
                    if ($response->success) {
                        $result[$response->merchantAccount->currencyIsoCode] = $response->merchantAccount->id;
                    }
                } catch (Exception $e) {
                }
            }
        }

        return $result;
    }

    /**
     * Get current Transaction status from BT
     * @param PaypalOrder $orderPayPal
     * @return string|boolean
     */
    public function getTransactionStatus($orderPayPal)
    {
        $this->initConfig($orderPayPal->sandbox);
        try {
            $result = $this->gateway->transaction()->find($orderPayPal->id_transaction);
            return $result->status;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @see AbstractMethodPaypal::validation()
     */
    public function validation()
    {
        $paypal = new PayPal();
        $transaction = $this->sale(context::getContext()->cart, $this->payment_method_nonce);

        if (!$transaction) {
            throw new Exception('Error during transaction validation', '00000');
        }
        $this->setDetailsTransaction($transaction);
        if (Configuration::get('PAYPAL_API_INTENT') == "sale" && $transaction->paymentInstrumentType == "paypal_account" && $transaction->status == "settling") { // or submitted for settlement?
            $order_state = Configuration::get('PAYPAL_BRAINTREE_OS_AWAITING_VALIDATION');
        } else if ((Configuration::get('PAYPAL_API_INTENT') == "sale" && $transaction->paymentInstrumentType == "paypal_account" && $transaction->status == "settled")
        || (Configuration::get('PAYPAL_API_INTENT') == "sale" && $transaction->paymentInstrumentType == "credit_card")) {
            $order_state = Configuration::get('PS_OS_PAYMENT');
        } else {
            $order_state = Configuration::get('PAYPAL_BRAINTREE_OS_AWAITING');
        }
        $paypal->validateOrder(context::getContext()->cart->id, $order_state, $transaction->amount, $this->getPaymentMethod(), $paypal->l('Payment accepted.', get_class($this)), $this->getDetailsTransaction(), context::getContext()->cart->id_currency, false, context::getContext()->customer->secure_key);
    }

    public function setDetailsTransaction($transaction)
    {
        $this->transaction_detail = array(
            'method' => 'BT',
            'currency' => pSQL($transaction->currencyIsoCode),
            'transaction_id' => pSQL($transaction->id),
            'payment_method' => $transaction->type,
            'payment_status' => $transaction->status,
            'id_payment' => $this->payment_method_nonce,
            'capture' => $transaction->status == "authorized" ? true : false,
            'payment_tool' => $transaction->paymentInstrumentType,
            'date_transaction' => $this->getDateTransaction($transaction)
        );
    }

    public function getDateTransaction($transaction)
    {
        return $transaction->updatedAt->format('Y-m-d H:i:s');
    }

    /**
     * Get order id for BT sale. Use secure key to avoid duplicate orderId error.
     * @param object $cart
     * @return string
     */
    public function getOrderId($cart)
    {
        return $cart->secure_key.'_'.$cart->id;
    }

    public function formatPrice($price)
    {
        $context = Context::getContext();
        $context_currency = $context->currency;
        $paypal = Module::getInstanceByName($this->name);
        if ($id_currency_to = $paypal->needConvert()) {
            $currency_to_convert = new Currency($id_currency_to);
            $price = Tools::ps_round(Tools::convertPriceFull($price, $context_currency, $currency_to_convert), _PS_PRICE_COMPUTE_PRECISION_);
        }
        return $price;
    }

    /**
     * @param $cart
     * @param $token_payment
     * @return bool|mixed
     * @throws Exception
     * @throws PaypalException
     */
    public function sale($cart, $token_payment)
    {
        $this->initConfig();
        $bt_method = $this->payment_method_bt;
        $vault_token = '';
        if ($bt_method == BT_PAYPAL_PAYMENT) {
            $options = array(
                'submitForSettlement' => Configuration::get('PAYPAL_API_INTENT') == "sale" ? true : false,
                'threeDSecure' => array(
                    'required' => Configuration::get('PAYPAL_USE_3D_SECURE')
                )
            );
        } else {
            $options = array(
                'submitForSettlement' => Configuration::get('PAYPAL_API_INTENT') == "sale" ? true : false,
            );
        }

        $merchant_accounts = (array)Tools::jsonDecode(Configuration::get('PAYPAL_'.$this->mode.'_BRAINTREE_ACCOUNT_ID'));
        $address_billing = new Address($cart->id_address_invoice);
        $country_billing = new Country($address_billing->id_country);
        $address_shipping = new Address($cart->id_address_delivery);
        $country_shipping = new Country($address_shipping->id_country);
        $amount = $this->formatPrice($cart->getOrderTotal());
        $paypal = Module::getInstanceByName($this->name);
        $currency = $paypal->getPaymentCurrencyIso();
        $iso_state = '';
        if ($address_shipping->id_state) {
            $state = new State((int) $address_shipping->id_state);
            $iso_state = $state->iso_code;
        }


        $data = array(
            'amount'                => $amount,
            'merchantAccountId'     => $merchant_accounts[$currency],
            'orderId'               => $this->getOrderId($cart),
            'channel'               => (getenv('PLATEFORM') == 'PSREAD')?'PrestaShop_Cart_Ready_Braintree':'PrestaShop_Cart_Braintree',
            'billing' => array(
                'firstName'         => $address_billing->firstname,
                'lastName'          => $address_billing->lastname,
                'company'           => $address_billing->company,
                'streetAddress'     => $address_billing->address1,
                'extendedAddress'   => $address_billing->address2,
                'locality'          => $address_billing->city,
                'postalCode'        => $address_billing->postcode,
                'countryCodeAlpha2' => $country_billing->iso_code,
                'region'            => $iso_state,
            ),
            'shipping' => array(
                'firstName'         => $address_shipping->firstname,
                'lastName'          => $address_shipping->lastname,
                'company'           => $address_shipping->company,
                'streetAddress'     => $address_shipping->address1,
                'extendedAddress'   => $address_shipping->address2,
                'locality'          => $address_shipping->city,
                'postalCode'        => $address_shipping->postcode,
                'countryCodeAlpha2' => $country_shipping->iso_code,
                'region'            => $iso_state,
            ),
            "deviceData"            => '',
        );

        $paypal_customer = PaypalCustomer::loadCustomerByMethod(Context::getContext()->customer->id, 'BT', (int)Configuration::get('PAYPAL_SANDBOX'));
        if (!$paypal_customer->id) {
            $paypal_customer = $this->createCustomer();
        } else {
            $this->updateCustomer($paypal_customer);
        }

        $paypal = Module::getInstanceByName($this->name);
        if (Configuration::get('PAYPAL_VAULTING')) {
            if ($bt_method == BT_CARD_PAYMENT) {
                $vault_token = $this->bt_vaulting_token;
            } elseif ($bt_method == BT_PAYPAL_PAYMENT) {
                $vault_token = $this->pbt_vaulting_token;
            }

            if ($vault_token && $paypal_customer->id) {
                if (PaypalVaulting::vaultingExist($vault_token, $paypal_customer->id)) {
                    $data['paymentMethodToken'] = $vault_token;
                }
            } else {
                if ($this->save_card_in_vault || $this->save_account_in_vault) {
                    if (Configuration::get('PAYPAL_BT_CARD_VERIFICATION') && $this->save_card_in_vault) {
                        $payment_method = $this->gateway->paymentMethod()->create(array(
                            'customerId' => $paypal_customer->reference,
                            'paymentMethodNonce' => $token_payment,
                            'options' => array('verifyCard' => true),
                        ));

                        if (isset($payment_method->verification) && $payment_method->verification->status != 'verified') {
                            $error_msg = $paypal->l('Card verification repond with status', get_class($this)).' '.$payment_method->verification->status.'. ';
                            $error_msg .= $paypal->l('The reason : ', get_class($this)).' '.$payment_method->verification->processorResponseText.'. ';
                            if ($payment_method->verification->gatewayRejectionReason) {
                                $error_msg .= $paypal->l('Rejection reason : ', get_class($this)).' '.$payment_method->verification->gatewayRejectionReason;
                            }
                            throw new Exception($error_msg, '00000');
                        }
                        $paymentMethodToken = $payment_method->paymentMethod->token;
                    }
                    $options['storeInVaultOnSuccess'] = true;
                    $data['customerId'] = $paypal_customer->reference;
                }
                if (isset($paymentMethodToken)) {
                    $data['paymentMethodToken'] = $paymentMethodToken;
                } else {
                    $data['paymentMethodNonce'] = $token_payment;
                }
            }
        } else {
            $data['paymentMethodNonce'] = $token_payment;
        }

        $data['options'] = $options;

        try {
            $result = $this->gateway->transaction()->sale($data);
        } catch (Braintree\Exception\Authorization $e) {
            throw new Exception('Braintree Authorization exception', '00000');
        }

        if (($result instanceof Braintree_Result_Successful) && $result->success && $this->isValidStatus($result->transaction->status)) {
            if (Configuration::get('PAYPAL_VAULTING')
                && (($this->save_card_in_vault && $bt_method == BT_CARD_PAYMENT)
                    || ($this->save_account_in_vault && $bt_method == BT_PAYPAL_PAYMENT))
                && !PaypalVaulting::vaultingExist($result->transaction->creditCard['token'], $paypal_customer->id)) {
                $this->createVaulting($result, $paypal_customer);
            }
            return $result->transaction;
        } else {
            $errors = $result->errors->deepAll();
            if ($errors) {
                throw new PaypalException($errors[0]->code, $errors[0]->message);
            } else {
                throw new PaypalException($result->transaction->processorResponseCode, $result->message);
            }
        }

        return false;
    }

    /**
     * Add PaypalVaulting
     * @param object $result payment transaction result object
     * @param object $paypal_customer
     */
    public function createVaulting($result, $paypal_customer)
    {
        $vaulting = new PaypalVaulting();
        $vaulting->id_paypal_customer = $paypal_customer->id;
        $vaulting->payment_tool = $this->payment_method_bt;
        if ($vaulting->payment_tool == BT_CARD_PAYMENT) {
            $vaulting->token = $result->transaction->creditCard['token'];
            $vaulting->info = $result->transaction->creditCard['cardType'].': *';
            $vaulting->info .= $result->transaction->creditCard['last4'].' ';
            $vaulting->info .= $result->transaction->creditCard['expirationMonth'].'/';
            $vaulting->info .= $result->transaction->creditCard['expirationYear'];
        } elseif ($vaulting->payment_tool == BT_PAYPAL_PAYMENT) {
            $vaulting->token = $result->transaction->paypal['token'];
            $vaulting->info = $result->transaction->paypal['payerFirstName'].' ';
            $vaulting->info .= $result->transaction->paypal['payerLastName'].' ';
            $vaulting->info .= $result->transaction->paypal['payerEmail'];
        }
        $vaulting->save();
    }

    /**
     * Update customer info on BT
     * @param PaypalCustomer $paypal_customer
     * @throws Exception
     */
    public function updateCustomer($paypal_customer)
    {
        $context = Context::getContext();
        $data = array(
            'firstName' => $context->customer->firstname,
            'lastName' => $context->customer->lastname,
            'email' => $context->customer->email
        );
        try {
            $this->gateway->customer()->update($paypal_customer->reference, $data);
        } catch (Braintree\Exception\NotFound $e) {
            $paypal_customer->sandbox = !$paypal_customer->sandbox;
            $paypal_customer->save();
            $paypal = Module::getInstanceByName($this->name);
            $mode  = Configuration::get('PAYPAL_SANDBOX') ? 'Sandbox' : 'Live';
            $mode2  = !Configuration::get('PAYPAL_SANDBOX') ? 'Sandbox' : 'Live';
            $msg = sprintf($paypal->l('This client is not found in %s mode.', get_class($this)), $mode);
            $msg .= sprintf($paypal->l('Probably this customer has been already created in %s mode. Please create new prestashop client for this mode.', get_class($this)), $mode2);
            throw new Exception($msg);
        }
    }

    /**
     * Create new customer on BT and PS
     * @return object PaypalCustomer
     */
    public function createCustomer()
    {
        $context = Context::getContext();
        $data = array(
            'firstName' => $context->customer->firstname,
            'lastName' => $context->customer->lastname,
            'email' => $context->customer->email
        );

        $result = $this->gateway->customer()->create($data);
        $customer = new PaypalCustomer();
        $customer->id_customer = $context->customer->id;
        $customer->reference = $result->customer->id;
        $customer->method = 'BT';
        $customer->sandbox = (int) Configuration::get('PAYPAL_SANDBOX');
        $customer->save();
        return $customer;
    }

    /**
     * Deleted vaulted method from BT
     * @param object $payment_method PaypalVaulting
     */
    public function deleteVaultedMethod($payment_method)
    {
        $this->initConfig();
        $this->gateway->paymentMethod()->delete($payment_method->token);
    }

    /**
     * Check if status is valid for vaulting
     * @param $status
     * @return bool
     */
    public function isValidStatus($status)
    {
        return in_array($status, array('submitted_for_settlement','authorized','settled', 'settling'));
    }

    /**
     * @see AbstractMethodPaypal::confirmCapture()
     */
    public function confirmCapture($paypal_order)
    {
        try {
            $this->initConfig($paypal_order->sandbox);
            $result = $this->gateway->transaction()->submitForSettlement($paypal_order->id_transaction, number_format($paypal_order->total_paid, 2, ".", ''));
            if ($result instanceof Braintree_Result_Successful && $result->success) {
                PaypalCapture::updateCapture($result->transaction->id, $result->transaction->amount, $result->transaction->status, $paypal_order->id);
                $response =  array(
                    'success' => true,
                    'authorization_id' => $result->transaction->id,
                    'status' => $result->transaction->status,
                    'amount' => $result->transaction->amount,
                    'currency' => $result->transaction->currencyIsoCode,
                    'payment_type' => isset($result->transaction->payment_type) ? $result->transaction->payment_type : '',
                    'merchantAccountId' => $result->transaction->merchantAccountId,
                    'date_transaction' => $this->getDateTransaction($result->transaction)
                );
            } else if ($result->transaction->status == Braintree_Transaction::SETTLEMENT_DECLINED) {
                $order = new Order(Tools::getValue('id_order'));
                $order->setCurrentState(Configuration::get('PS_OS_ERROR'));
            } else {
                $errors = $result->errors->deepAll();

                foreach ($errors as $error) {
                    $response = array(
                        'transaction_capture_id' => $result->transaction->id,
                        'status' => $result->transaction->status,
                        'error_code' => $error->code,
                        'error_message' => $error->message,
                    );
                    if ($error->code == Braintree_Error_Codes::TRANSACTION_CANNOT_SUBMIT_FOR_SETTLEMENT) {
                        $response['already_captured'] = true;
                    }
                }
            }
            return $response;
        } catch (Exception $e) {
            $response =  array(
                'error_message' => $e->getCode().'=>'.$e->getMessage(),
            );
            return $response;
        }
    }

    /**
     * @see AbstractMethodPaypal::refund()
     */
    public function refund($paypal_order)
    {
        try {
            $this->initConfig($paypal_order->sandbox);
            $capture = PaypalCapture::loadByOrderPayPalId($paypal_order->id);
            $id_transaction = Validate::isLoadedObject($capture) ? $capture->id_capture : $paypal_order->id_transaction;

            $result = $this->gateway->transaction()->refund($id_transaction, number_format($paypal_order->total_paid, 2, ".", ''));
            if ($result->success) {
                $response =  array(
                    'success' => true,
                    'refund_id' => $result->transaction->refundedTransactionId,
                    'transaction_id' => $result->transaction->id,
                    'status' => $result->transaction->status,
                    'amount' => $result->transaction->amount,
                    'currency' => $result->transaction->currencyIsoCode,
                    'payment_type' => $result->transaction->payment_type,
                    'merchantAccountId' => $result->transaction->merchantAccountId,
                    'date_transaction' => $this->getDateTransaction($result->transaction)
                );
            } elseif ($result->transaction->status == Braintree_Transaction::SETTLEMENT_DECLINED) {
                $order = new Order(Tools::getValue('id_order'));
                $order->setCurrentState(Configuration::get('PS_OS_ERROR'));
                $response =  array(
                    'transaction_id' => $result->params['id'],
                    'error_message' => $result->message,
                );
            } else {
                $errors = $result->errors->deepAll();
                foreach ($errors as $error) {
                    $response = array(
                        'transaction_id' => $result->transaction->refundedTransactionId,
                        'status' => 'Failure',
                        'error_code' => $error->code,
                        'error_message' => $error->message,
                    );
                    if ($error->code == Braintree_Error_Codes::TRANSACTION_HAS_ALREADY_BEEN_REFUNDED) {
                        $response['already_refunded'] = true;
                    }
                }
            }
            return $response;
        } catch (Exception $e) {
            $response =  array(
                'error_message' => $e->getCode().'=>'.$e->getMessage(),
            );
            return $response;
        }
    }

    /**
     * @see AbstractMethodPaypal::partialRefund()
     */
    public function partialRefund($params)
    {
        try {
            $paypal_order = PaypalOrder::loadByOrderId(Tools::getValue('id_order'));
            $this->initConfig($paypal_order->sandbox);
            $capture = PaypalCapture::loadByOrderPayPalId($paypal_order->id);
            $id_transaction = Validate::isLoadedObject($capture) ? $capture->id_capture : $paypal_order->id_transaction;
            $amount = 0;
            foreach ($params['productList'] as $product) {
                $amount += $product['amount'];
            }
            if (Tools::getValue('partialRefundShippingCost')) {
                $amount += Tools::getValue('partialRefundShippingCost');
            }
            $result = $this->gateway->transaction()->refund($id_transaction, number_format($amount, 2, ".", ''));

            if ($result->success) {
                $response =  array(
                    'success' => true,
                    'refundedTransactionId' => $result->transaction->refundedTransactionId,
                    'refund_id' => $result->transaction->id,
                    'status' => $result->transaction->status,
                    'amount' => $result->transaction->amount,
                    'currency' => $result->transaction->currencyIsoCode,
                    'payment_type' => $result->transaction->payment_type,
                    'merchantAccountId' => $result->transaction->merchantAccountId,
                );
            } else {
                $errors = $result->errors->deepAll();
                foreach ($errors as $error) {
                    $response = array(
                        'refundedTransactionId' => $result->transaction->refundedTransactionId,
                        'status' => 'Failure',
                        'error_code' => $error->code,
                        'error_message' => $error->message,
                    );
                    if ($error->code == Braintree_Error_Codes::TRANSACTION_HAS_ALREADY_BEEN_REFUNDED) {
                        $response['already_refunded'] = true;
                    }
                }
            }
            return $response;
        } catch (Exception $e) {
            $response =  array(
                'error_message' => $e->getCode().'=>'.$e->getMessage(),
            );
            return $response;
        }
    }

    /**
     * @see AbstractMethodPaypal::void()
     */
    public function void($orderPayPal)
    {
        $this->initConfig($orderPayPal->sandbox);
        try {
            $result = $this->gateway->transaction()->void($orderPayPal->id_transaction);
            if ($result instanceof Braintree_Result_Successful && $result->success) {
                $response =  array(
                    'success' => true,
                    'transaction_id' => $result->transaction->id,
                    'status' => $result->transaction->status,
                    'amount' => $result->transaction->amount,
                    'currency' => $result->transaction->currencyIsoCode,
                    'date_transaction' => $this->getDateTransaction($result->transaction)
                );
            } elseif ($result->transaction->status == Braintree_Transaction::SETTLEMENT_DECLINED) {
                $order = new Order(Tools::getValue('id_order'));
                $order->setCurrentState(Configuration::get('PS_OS_ERROR'));
                $response =  array(
                    'transaction_id' => $result->params['id'],
                    'error_message' => $result->message,
                );
            } else {
                $response =  array(
                    'transaction_id' => $result->params['id'],
                    'error_message' => $result->message,
                );
            }
            return $response;
        } catch (Exception $e) {
            $response =  array(
                'error_message' => $e->getCode().'=>'.$e->getMessage(),
            );
            return $response;
        }
    }

    /**
     * @param array $ids
     * @return mixed
     */
    public function searchTransactions($paypalOrders)
    {
        $collection = array();
        foreach ($paypalOrders as $paypalOrder) {
            $transaction = $this->searchTransaction($paypalOrder);
            if ($transaction === false) {
                continue;
            }
            $collection[] = $transaction;
        }
        return $collection;
    }

    /**
     * @param PaypalOrder $paypalOrder
     * @return mixed
     */
    public function searchTransaction($paypalOrder)
    {
        $this->initConfig($paypalOrder->sandbox);
        try {
            $transaction = $this->gateway->transaction()->find($paypalOrder->id_transaction);
            return $transaction;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Create payment method nonce
     * @param $token
     * @return mixed
     */
    public function createMethodNonce($token)
    {
        $this->initConfig();
        $nonce = $this->gateway->paymentMethodNonce()->create($token);
        return $nonce->paymentMethodNonce->nonce;
    }

    /**
     * @see AbstractMethodPaypal::getLinkToTransaction()
     */
    public function getLinkToTransaction($id_transaction, $sandbox)
    {
        if ($sandbox) {
            $url = 'https://sandbox.braintreegateway.com/merchants/' . Configuration::get('PAYPAL_SANDBOX_BRAINTREE_MERCHANT_ID') . '/transactions/';
        } else {
            $url = 'https://www.braintreegateway.com/merchants/' . Configuration::get('PAYPAL_LIVE_BRAINTREE_MERCHANT_ID') . '/transactions/';
        }
        return $url . $id_transaction;
    }

    /**
     * @return bool
     */
    public function isConfigured()
    {
        if (Configuration::get('PAYPAL_SANDBOX')) {
            return (bool)Configuration::get('PAYPAL_SANDBOX_BRAINTREE_MERCHANT_ID');
        } else {
            return (bool)Configuration::get('PAYPAL_LIVE_BRAINTREE_MERCHANT_ID');
        }
    }
}
