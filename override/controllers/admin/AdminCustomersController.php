<?php

/**
* @property Customer $object
**/
class AdminCustomersController extends AdminCustomersControllerCore {

	public function __construct() {

        $this->bootstrap = true;
        $this->required_database = true;
        $this->table = 'customer';
        $this->className = 'Customer';
        $this->lang = false;
        $this->deleted = true;
        $this->explicitSelect = true;

        $this->allow_export = true;

        AdminController::__construct();

        $this->required_fields = array(
            array(
                'name' => 'optin',
                'label' => $this->trans('Partner offers', array(), 'Admin.Orderscustomers.Feature')
            ),
        );

        $this->addRowAction('edit');
        $this->addRowAction('view');
        $this->addRowAction('delete');
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Notifications.Info'),
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Info'),
                'icon' => 'icon-trash'
            )
        );

        $this->default_form_language = $this->context->language->id;

        $titles_array = array();
        $genders = Gender::getGenders($this->context->language->id);
        foreach ($genders as $gender) {
            /** @var Gender $gender */
            $titles_array[$gender->id_gender] = $gender->name;
        }

        $this->_join = 'LEFT JOIN '._DB_PREFIX_.'gender_lang gl ON (a.id_gender = gl.id_gender AND gl.id_lang = '.(int)$this->context->language->id.')';
        $this->_join .= ' LEFT JOIN '._DB_PREFIX_.AccountType::TABLE_NAME.' ac ON (a.id_account_type = ac.id_account_type)';
        $this->_use_found_rows = false;
        $this->fields_list = array(
            'id_customer' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'reference' => array(
                'title' => $this->trans('Référence', array(), 'Admin.Global')
            ),
            'title' => array(
                'title' => $this->trans('Social title', array(), 'Admin.Global'),
                'filter_key' => 'a!id_gender',
                'type' => 'select',
                'list' => $titles_array,
                'filter_type' => 'int',
                'order_key' => 'gl!name'
            ),
            'firstname' => array(
                'title' => $this->trans('First name', array(), 'Admin.Global')
            ),
            'lastname' => array(
                'title' => $this->trans('Last name', array(), 'Admin.Global')
            ),
            'id_account_type' => array(
                'title' => $this->trans('Type de compte', array(), 'Admin.Global'),
                'filter_key' => 'ac!name'
            ),
            'email' => array(
                'title' => $this->trans('Email address', array(), 'Admin.Global')
            ),
        );

        if (Configuration::get('PS_B2B_ENABLE')) {
            $this->fields_list = array_merge($this->fields_list, array(
                'company' => array(
                    'title' => $this->trans('Company', array(), 'Admin.Global'),
                    'filter_key' => 'a!company'
                ),
            ));
        }

        $this->fields_list = array_merge($this->fields_list, array(
            'total_spent' => array(
                'title' => $this->trans('Sales', array(), 'Admin.Global'),
                'type' => 'price',
                'search' => false,
                'havingFilter' => true,
                'align' => 'text-right',
                'badge_success' => true
            ),
            'active' => array(
                'title' => $this->trans('Enabled', array(), 'Admin.Global'),
                'align' => 'text-center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'a!active'
            ),
            'newsletter' => array(
                'title' => $this->trans('Newsletter', array(), 'Admin.Global'),
                'align' => 'text-center',
                'callback' => 'printNewsIcon',
            ),
            'optin' => array(
                'title' => $this->trans('Partner offers', array(), 'Admin.Orderscustomers.Feature'),
                'align' => 'text-center',
                'callback' => 'printOptinIcon',
            ),
            'date_add' => array(
                'title' => $this->trans('Registration', array(), 'Admin.Orderscustomers.Feature'),
                'type' => 'date',
                'align' => 'text-right'
            )
        ));

        $this->shopLinkType = 'shop';
        $this->shopShareDatas = Shop::SHARE_CUSTOMER;

        $this->_select = '
        a.date_add, gl.name as title, (
            SELECT SUM(total_paid_real / conversion_rate)
            FROM '._DB_PREFIX_.'orders o
            WHERE o.id_customer = a.id_customer
            '.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
            AND o.valid = 1
        ) as total_spent, (
            SELECT c.date_add FROM '._DB_PREFIX_.'guest g
            LEFT JOIN '._DB_PREFIX_.'connections c ON c.id_guest = g.id_guest
            WHERE g.id_customer = a.id_customer
            ORDER BY c.date_add DESC
            LIMIT 1
        ) as connect';

        // Check if we can add a customer
        if (Shop::isFeatureActive() && (Shop::getContext() == Shop::CONTEXT_ALL || Shop::getContext() == Shop::CONTEXT_GROUP)) {
            $this->can_add_customer = false;
        }

        self::$meaning_status = array(
            'open' => $this->trans('Open', array(), 'Admin.Orderscustomers.Feature'),
            'closed' => $this->trans('Closed', array(), 'Admin.Orderscustomers.Feature'),
            'pending1' => $this->trans('Pending 1', array(), 'Admin.Orderscustomers.Feature'),
            'pending2' => $this->trans('Pending 2', array(), 'Admin.Orderscustomers.Feature')
        );
    }

	public function renderForm() {

        /** @var Customer $obj */
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $genders = Gender::getGenders();
        $list_genders = array();
        foreach ($genders as $key => $gender) {
            /** @var Gender $gender */
            $list_genders[$key]['id'] = 'gender_'.$gender->id;
            $list_genders[$key]['value'] = $gender->id;
            $list_genders[$key]['label'] = $gender->name;
        }

        $list_types = array();
        foreach(AccountType::getAccountTypes() as $accountType) {
        	/** @var AccountType $accountType */
        	$list_types[$accountType->id]['name'] = $accountType->name;
        	$list_types[$accountType->id]['value'] = $accountType->id;
        }

        $list_states[] = array('value'=>0, 'name'=>'Choisir un état');
        foreach(CustomerState::getCustomerStates() as $customerState) {
            $list_states[$customerState->id]['name'] = $customerState->name;
            $list_states[$customerState->id]['value'] = $customerState->id;
        }

        $years = Tools::dateYears();
        $months = Tools::dateMonths();
        $days = Tools::dateDays();

        $groups = Group::getGroups($this->default_form_language, true);
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Customer', array(), 'Admin.Global'),
                'icon' => 'icon-user'
            ),
            'input' => array(
                array(
                    'type' => 'radio',
                    'label' => $this->trans('Social title', array(), 'Admin.Global'),
                    'name' => 'id_gender',
                    'required' => false,
                    'class' => 't',
                    'values' => $list_genders
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('First name', array(), 'Admin.Global'),
                    'name' => 'firstname',
                    'required' => true,
                    'col' => 4,
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' 0-9!&lt;&gt;,;?=+()@#"°{}_$%:'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Last name', array(), 'Admin.Global'),
                    'name' => 'lastname',
                    'required' => true,
                    'col' => 4,
                    'hint' => $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' 0-9!&lt;&gt;,;?=+()@#"°{}_$%:'
                ),
                array(
                    'type' => 'select',
                    'label' => $this->trans('Type de compte', array(), 'Admin.Orderscustomers.Feature'),
                    'name' => 'id_account_type',
                    'col' => 4,
                    'options' => array(
                        'query' => $list_types,
                        'id' => 'value',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->trans('Statut du client', array(), 'Admin.Orderscustomers.Feature'),
                    'name' => 'id_customer_state',
                    'col' => 4,
                    'options' => array(
                        'query' => $list_states,
                        'id' => 'value',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->trans('Commentaire statut', array(), 'Admin.Global'),
                    'name' => 'comment',
                    'col' => 4,
                    'rows' => 3
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Solvable', array(), 'Admin.Global'),
                    'name' => 'funding',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'funding_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', array(), 'Admin.Global')
                        ),
                        array(
                            'id' => 'funding_off',
                            'value' => 0,
                            'label' => $this->trans('No', array(), 'Admin.Global')
                        )
                    )
                ),
                array(
                    'type' => 'date',
                    'label' => $this->trans('Date solvabilité', array(), 'Admin.Global'),
                    'name' => 'date_funding'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('E-deal', array(), 'Admin.Global'),
                    'name' => 'reference',
                    'col' => 4
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Chorus', array(), 'Admin.Global'),
                    'name' => 'chorus',
                    'col' => 4
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('TVA interne', array(), 'Admin.Global'),
                    'name' => 'tva',
                    'col' => 4
                ),
                array(
                    'type' => 'text',
                    'prefix' => '<i class="icon-envelope-o"></i>',
                    'label' => $this->trans('Email address', array(), 'Admin.Global'),
                    'name' => 'email',
                    'col' => 4,
                    'required' => true,
                    'autocomplete' => false
                ),
                array(
                    'type' => 'text',
                    'prefix' => '<i class="icon-file-o"></i>',
                    'label' => $this->trans('E-mail facturation', array(), 'Admin.Global'),
                    'name' => 'email_invoice',
                    'col' => 4,
                    'autocomplete' => false
                ),
                array(
                    'type' => 'text',
                    'prefix' => '<i class="icon-truck"></i>',
                    'label' => $this->trans('E-mail livraison', array(), 'Admin.Global'),
                    'name' => 'email_tracking',
                    'col' => 4,
                    'autocomplete' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Rollcash', array(), 'Admin.Global'),
                    'name' => 'rollcash',
                    'col' => 4,
                    'autocomplete' => false,
                    'hint' => $this->trans("Décimales séparées par un '.'"),
                    'suffix' => $this->context->currency->sign
                ),
                array(
                    'type' => 'password',
                    'label' => $this->trans('Password', array(), 'Admin.Global'),
                    'name' => 'passwd',
                    'required' => ($obj->id ? false : true),
                    'col' => 4,
                    'hint' => ($obj->id ? $this->trans('Leave this field blank if there\'s no change.', array(), 'Admin.Orderscustomers.Help') :
                        $this->trans('Password should be at least %length% characters long.', array('%length%' => Validate::PASSWORD_LENGTH), 'Admin.Orderscustomers.Help'))
                ),
                array(
                    'type' => 'birthday',
                    'label' => $this->trans('Birthday', array(), 'Admin.Orderscustomers.Feature'),
                    'name' => 'birthday',
                    'options' => array(
                        'days' => $days,
                        'months' => $months,
                        'years' => $years
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Enabled', array(), 'Admin.Global'),
                    'name' => 'active',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled', array(), 'Admin.Global')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled', array(), 'Admin.Global')
                        )
                    ),
                    'hint' => $this->trans('Enable or disable customer login.', array(), 'Admin.Orderscustomers.Help')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Partner offers', array(), 'Admin.Orderscustomers.Feature'),
                    'name' => 'optin',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'optin_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled', array(), 'Admin.Global')
                        ),
                        array(
                            'id' => 'optin_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled', array(), 'Admin.Global')
                        )
                    ),
                    'disabled' =>  (bool)!Configuration::get('PS_CUSTOMER_OPTIN'),
                    'hint' => $this->trans('This customer will receive your ads via email.', array(), 'Admin.Orderscustomers.Help')
                ),
            )
        );

        // if we add a customer via fancybox (ajax), it's a customer and he doesn't need to be added to the visitor and guest groups
        if (Tools::isSubmit('addcustomer') && Tools::isSubmit('submitFormAjax')) {
            $visitor_group = Configuration::get('PS_UNIDENTIFIED_GROUP');
            $guest_group = Configuration::get('PS_GUEST_GROUP');
            foreach ($groups as $key => $g) {
                if (in_array($g['id_group'], array($visitor_group, $guest_group))) {
                    unset($groups[$key]);
                }
            }
        }

        $this->fields_form['input'] = array_merge(
            $this->fields_form['input'],
            array(
                array(
                    'type' => 'group',
                    'label' => $this->trans('Group access', array(), 'Admin.Orderscustomers.Feature'),
                    'name' => 'groupBox',
                    'values' => $groups,
                    'required' => true,
                    'col' => 6,
                    'hint' => $this->trans('Select all the groups that you would like to apply to this customer.', array(), 'Admin.Orderscustomers.Help')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->trans('Default customer group', array(), 'Admin.Orderscustomers.Feature'),
                    'name' => 'id_default_group',
                    'options' => array(
                        'query' => $groups,
                        'id' => 'id_group',
                        'name' => 'name'
                    ),
                    'col' => 4,
                    'hint' => array(
                        $this->trans('This group will be the user\'s default group.', array(), 'Admin.Orderscustomers.Help'),
                        $this->trans('Only the discount for the selected group will be applied to this customer.', array(), 'Admin.Orderscustomers.Help')
                    )
                )
            )
        );

        // if customer is a guest customer, password hasn't to be there
        if ($obj->id && ($obj->is_guest && $obj->id_default_group == Configuration::get('PS_GUEST_GROUP'))) {
            foreach ($this->fields_form['input'] as $k => $field) {
                if ($field['type'] == 'password') {
                    array_splice($this->fields_form['input'], $k, 1);
                }
            }
        }

        if (Configuration::get('PS_B2B_ENABLE')) {
            $risks = Risk::getRisks();

            $list_risks = array();
            foreach ($risks as $key => $risk) {
                /** @var Risk $risk */
                $list_risks[$key]['id_risk'] = (int)$risk->id;
                $list_risks[$key]['name'] = $risk->name;
            }

            $this->fields_form['input'][] = array(
                'type' => 'text',
                'label' => $this->trans('Company', array(), 'Admin.Global'),
                'name' => 'company'
            );
            $this->fields_form['input'][] = array(
                'type' => 'text',
                'label' => $this->trans('SIRET', array(), 'Admin.Orderscustomers.Feature'),
                'name' => 'siret'
            );
            $this->fields_form['input'][] = array(
                'type' => 'text',
                'label' => $this->trans('APE', array(), 'Admin.Orderscustomers.Feature'),
                'name' => 'ape'
            );
            $this->fields_form['input'][] = array(
                'type' => 'text',
                'label' => $this->trans('Website', array(), 'Admin.Orderscustomers.Feature'),
                'name' => 'website'
            );
            $this->fields_form['input'][] = array(
                'type' => 'text',
                'label' => $this->trans('Allowed outstanding amount', array(), 'Admin.Orderscustomers.Feature'),
                'name' => 'outstanding_allow_amount',
                'hint' => $this->trans('Valid characters:', array(), 'Admin.Orderscustomers.Help').' 0-9',
                'suffix' => $this->context->currency->sign
            );
            $this->fields_form['input'][] = array(
                'type' => 'text',
                'label' => $this->trans('Maximum number of payment days', array(), 'Admin.Orderscustomers.Feature'),
                'name' => 'max_payment_days',
                'hint' => $this->trans('Valid characters:', array(), 'Admin.Orderscustomers.Help').' 0-9'
            );
            $this->fields_form['input'][] = array(
                'type' => 'select',
                'label' => $this->trans('Risk rating', array(), 'Admin.Orderscustomers.Feature'),
                'name' => 'id_risk',
                'required' => false,
                'class' => 't',
                'options' => array(
                    'query' => $list_risks,
                    'id' => 'id_risk',
                    'name' => 'name'
                ),
            );
        }

        $this->fields_form['submit'] = array(
            'title' => $this->trans('Save', array(), 'Admin.Actions'),
        );

        $birthday = explode('-', $this->getFieldValue($obj, 'birthday'));

        $this->fields_value = array(
            'years' => $this->getFieldValue($obj, 'birthday') ? $birthday[0] : 0,
            'months' => $this->getFieldValue($obj, 'birthday') ? $birthday[1] : 0,
            'days' => $this->getFieldValue($obj, 'birthday') ? $birthday[2] : 0,
        );

        // Added values of object Group
        if (!Validate::isUnsignedId($obj->id)) {
            $customer_groups = array();
        } else {
            $customer_groups = $obj->getGroups();
        }
        $customer_groups_ids = array();
        if (is_array($customer_groups)) {
            foreach ($customer_groups as $customer_group) {
                $customer_groups_ids[] = $customer_group;
            }
        }

        // if empty $carrier_groups_ids : object creation : we set the default groups
        if (empty($customer_groups_ids)) {
            $preselected = array(Configuration::get('PS_UNIDENTIFIED_GROUP'), Configuration::get('PS_GUEST_GROUP'), Configuration::get('PS_CUSTOMER_GROUP'));
            $customer_groups_ids = array_merge($customer_groups_ids, $preselected);
        }

        foreach ($groups as $group) {
            $this->fields_value['groupBox_'.$group['id_group']] =
                Tools::getValue('groupBox_'.$group['id_group'], in_array($group['id_group'], $customer_groups_ids));
        }

        return AdminController::renderForm();
    }
}