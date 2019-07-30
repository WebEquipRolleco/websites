<?php

class AdminEmployeesController extends AdminEmployeesControllerCore {

	/**
	* OVERRIDE : ajout SAV
	**/
	public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'employee';
        $this->className = 'Employee';
        $this->lang = false;

        AdminController::__construct();

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->addRowActionSkipList('delete', array((int)$this->context->employee->id));

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash'
            )
        );
        /*
        check if there are more than one superAdmin
        if it's the case then we can delete a superAdmin
        */
        $super_admin = Employee::countProfile(_PS_ADMIN_PROFILE_, true);
        if ($super_admin == 1) {
            $super_admin_array = Employee::getEmployeesByProfile(_PS_ADMIN_PROFILE_, true);
            $super_admin_id = array();
            foreach ($super_admin_array as $val) {
                $super_admin_id[] = $val['id_employee'];
            }
            $this->addRowActionSkipList('delete', $super_admin_id);
        }

        $profiles = Profile::getProfiles($this->context->language->id);
        if (!$profiles) {
            $this->errors[] = $this->trans('No profile.', array(), 'Admin.Notifications.Error');
        } else {
            foreach ($profiles as $profile) {
                $this->profiles_array[$profile['name']] = $profile['name'];
            }
        }

        $this->fields_list = array(
            'id_employee' => array('title' => $this->trans('ID', array(), 'Admin.Global'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'firstname' => array('title' => $this->trans('First name', array(), 'Admin.Global')),
            'lastname' => array('title' => $this->trans('Last name', array(), 'Admin.Global')),
            'email' => array('title' => $this->trans('Email address', array(), 'Admin.Global')),
            'profile' => array('title' => $this->trans('Profile', array(), 'Admin.Advparameters.Feature'), 'type' => 'select', 'list' => $this->profiles_array,
                'filter_key' => 'pl!name', 'class' => 'fixed-width-lg'),
            'active' => array('title' => $this->trans('Active', array(), 'Admin.Global'), 'align' => 'center', 'active' => 'status',
                'type' => 'bool', 'class' => 'fixed-width-sm'),
            'sav' => array('title' => $this->trans('SAV', array(), 'Admin.Global'), 'align' => 'center', 'active' => 'status',
                'type' => 'bool', 'class' => 'fixed-width-sm')
        );

        $this->fields_options = array(
            'general' => array(
                'title' =>    $this->trans('Employee options', array(), 'Admin.Advparameters.Feature'),
                'fields' =>    array(
                    'PS_PASSWD_TIME_BACK' => array(
                        'title' => $this->trans('Password regeneration', array(), 'Admin.Advparameters.Feature'),
                        'hint' => $this->trans('Security: Minimum time to wait between two password changes.', array(), 'Admin.Advparameters.Feature'),
                        'cast' => 'intval',
                        'type' => 'text',
                        'suffix' => ' '.$this->trans('minutes', array(), 'Admin.Advparameters.Feature'),
                        'visibility' => Shop::CONTEXT_ALL
                    ),
                    'PS_BO_ALLOW_EMPLOYEE_FORM_LANG' => array(
                        'title' => $this->trans('Memorize the language used in Admin panel forms', array(), 'Admin.Advparameters.Feature'),
                        'hint' => $this->trans('Allow employees to select a specific language for the Admin panel form.', array(), 'Admin.Advparameters.Feature'),
                        'cast' => 'intval',
                        'type' => 'select',
                        'identifier' => 'value',
                        'list' => array(
                            '0' => array('value' => 0, 'name' => $this->trans('No', array(), 'Admin.Global')),
                            '1' => array('value' => 1, 'name' => $this->trans('Yes', array(), 'Admin.Global')
                        )
                    ), 'visibility' => Shop::CONTEXT_ALL)
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions'))
            )
        );

        $home_tab = Tab::getInstanceFromClassName('AdminDashboard', $this->context->language->id);
        $this->tabs_list[$home_tab->id] = array(
            'name' => $home_tab->name,
            'id_tab' => $home_tab->id,
            'children' => array(array(
                'id_tab' => $home_tab->id,
                'name' => $home_tab->name
            ))
        );
        foreach (Tab::getTabs($this->context->language->id, 0) as $tab) {
            if (Tab::checkTabRights($tab['id_tab'])) {
                $this->tabs_list[$tab['id_tab']] = $tab;
                foreach (Tab::getTabs($this->context->language->id, $tab['id_tab']) as $children) {
                    if (Tab::checkTabRights($children['id_tab'])) {
                        foreach (Tab::getTabs($this->context->language->id, $children['id_tab']) as $subchild) {
                            if (Tab::checkTabRights($subchild['id_tab'])) {
                                $this->tabs_list[$tab['id_tab']]['children'][] = $subchild;
                            }
                        }
                    }
                }
            }
        }

        // An employee can edit its own profile
        if ($this->context->employee->id == Tools::getValue('id_employee')) {
            $this->tabAccess['view'] = '1';
            $this->restrict_edition = true;
            $this->tabAccess['edit'] = '1';
        }
    }

    /**
	* OVERRIDE : ajout SAV
	**/
    public function renderForm()
    {
        /** @var Employee $obj */
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $available_profiles = Profile::getProfiles($this->context->language->id);

        if ($obj->id_profile == _PS_ADMIN_PROFILE_ && $this->context->employee->id_profile != _PS_ADMIN_PROFILE_) {
            $this->errors[] = $this->trans('You cannot edit the SuperAdmin profile.', array(), 'Admin.Advparameters.Notification');
            return AdminController::renderForm();
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Employees', array(), 'Admin.Advparameters.Feature'),
                'icon' => 'icon-user'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'class' => 'fixed-width-xl',
                    'label' => $this->trans('First name', array(), 'Admin.Global'),
                    'name' => 'firstname',
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'class' => 'fixed-width-xl',
                    'label' => $this->trans('Last name', array(), 'Admin.Global'),
                    'name' => 'lastname',
                    'required' => true
                ),
                array(
	                'type' => 'switch',
	                'label' => $this->trans('SAV', array(), 'Admin.Global'),
	                'name' => 'sav',
	                'required' => false,
	                'is_bool' => true,
	                'values' => array(
	                    array(
	                        'id' => 'sav_on',
	                        'value' => 1,
	                        'label' => $this->trans('Yes', array(), 'Admin.Global')
	                    ),
	                    array(
	                        'id' => 'sav_off',
	                        'value' => 0,
	                        'label' => $this->trans('No', array(), 'Admin.Global')
	                    )
	                ),
	                'hint' => $this->trans('Recevoir les rappels liés au SAV', array(), 'Admin.Advparameters.Help')
	            ),
                array(
                    'type' => 'html',
                    'name' => 'employee_avatar',
                    'html_content' => '<div id="employee-thumbnail"><a href="http://www.prestashop.com/forums/index.php?app=core&amp;module=usercp" target="_blank" style="background-image:url('.$obj->getImage().')"></a></div>
					<div id="employee-avatar-thumbnail" class="alert alert-info">'.$this->trans(
                        'Your avatar in PrestaShop 1.7.x is your profile picture on %url%. To change your avatar, log in to PrestaShop.com with your email %email% and follow the on-screen instructions.',
                        array(
                            '%url%' => '<a href="http://www.prestashop.com/forums/index.php?app=core&amp;module=usercp" class="alert-link" target="_blank">PrestaShop.com</a>',
                            '%email%' => $obj->email,
                        ),
                        'Admin.Advparameters.Help'
                        ).'
                    </div>',
                ),
                array(
                    'type' => 'text',
                    'class'=> 'fixed-width-xxl',
                    'prefix' => '<i class="icon-envelope-o"></i>',
                    'label' => $this->trans('Email address', array(), 'Admin.Global'),
                    'name' => 'email',
                    'required' => true,
                    'autocomplete' => false
                ),
            ),
        );

        if ($this->restrict_edition) {
            $this->fields_form['input'][] = array(
                'type' => 'change-password',
                'label' => $this->trans('Password', array(), 'Admin.Global'),
                'name' => 'passwd'
                );

            if (Tab::checkTabRights(Tab::getIdFromClassName('AdminModulesController'))) {
                $this->fields_form['input'][] = array(
                    'type' => 'prestashop_addons',
                    'label' => 'PrestaShop Addons',
                    'name' => 'prestashop_addons',
                );
            }
        } else {
            $this->fields_form['input'][] = array(
                'type' => 'password',
                'label' => $this->trans('Password', array(), 'Admin.Global'),
                'hint' => $this->trans('Password should be at least %num% characters long.', array('%num%' => Validate::ADMIN_PASSWORD_LENGTH), 'Admin.Advparameters.Help'),
                'name' => 'passwd'
                );
        }

        $this->fields_form['input'] = array_merge($this->fields_form['input'], array(
            array(
                'type' => 'switch',
                'label' => $this->trans('Subscribe to PrestaShop newsletter', array(), 'Admin.Advparameters.Feature'),
                'name' => 'optin',
                'required' => false,
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'optin_on',
                        'value' => 1,
                        'label' => $this->trans('Yes', array(), 'Admin.Global')
                    ),
                    array(
                        'id' => 'optin_off',
                        'value' => 0,
                        'label' => $this->trans('No', array(), 'Admin.Global')
                    )
                ),
                'hint' => $this->trans('PrestaShop can provide you with guidance on a regular basis by sending you tips on how to optimize the management of your store which will help you grow your business. If you do not wish to receive these tips, you can disable this option.', array(), 'Admin.Advparameters.Help')
            ),
            array(
                'type' => 'default_tab',
                'label' => $this->trans('Default page', array(), 'Admin.Advparameters.Feature'),
                'name' => 'default_tab',
                'hint' => $this->trans('This page will be displayed just after login.', array(), 'Admin.Advparameters.Help'),
                'options' => $this->tabs_list
            ),
            array(
                'type' => 'select',
                'label' => $this->trans('Language', array(), 'Admin.Global'),
                'name' => 'id_lang',
                //'required' => true,
                'options' => array(
                    'query' => Language::getLanguages(false),
                    'id' => 'id_lang',
                    'name' => 'name'
                )
            ),
        ));

        if ((int)$this->access('edit') && !$this->restrict_edition) {
            $this->fields_form['input'][] = array(
                'type' => 'switch',
                'label' => $this->trans('Active', array(), 'Admin.Global'),
                'name' => 'active',
                'required' => false,
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
                'hint' => $this->trans('Allow or disallow this employee to log in to the Admin panel.', array(), 'Admin.Advparameters.Help')
            );

            // if employee is not SuperAdmin (id_profile = 1), don't make it possible to select the admin profile
            if ($this->context->employee->id_profile != _PS_ADMIN_PROFILE_) {
                foreach ($available_profiles as $i => $profile) {
                    if ($available_profiles[$i]['id_profile'] == _PS_ADMIN_PROFILE_) {
                        unset($available_profiles[$i]);
                        break;
                    }
                }
            }
            $this->fields_form['input'][] = array(
                'type' => 'select',
                'label' => $this->trans('Permission profile', array(), 'Admin.Advparameters.Feature'),
                'name' => 'id_profile',
                'required' => true,
                'options' => array(
                    'query' => $available_profiles,
                    'id' => 'id_profile',
                    'name' => 'name',
                    'default' => array(
                        'value' => '',
                        'label' => $this->trans('-- Choose --', array(), 'Admin.Advparameters.Help'),
                    )
                )
            );

            if (Shop::isFeatureActive()) {
                $this->context->smarty->assign('_PS_ADMIN_PROFILE_', (int)_PS_ADMIN_PROFILE_);
                $this->fields_form['input'][] = array(
                    'type' => 'shop',
                    'label' => $this->trans('Shop association', array(), 'Admin.Global'),
                    'hint' => $this->trans('Select the shops the employee is allowed to access.', array(), 'Admin.Advparameters.Help'),
                    'name' => 'checkBoxShopAsso',
                );
            }
        }

        $this->fields_form['submit'] = array(
            'title' => $this->trans('Save', array(), 'Admin.Actions'),
        );

        $this->fields_value['passwd'] = false;
        $this->fields_value['bo_theme_css'] = $obj->bo_theme.'|'.$obj->bo_css;

        if (empty($obj->id)) {
            $this->fields_value['id_lang'] = $this->context->language->id;
        }

        return AdminController::renderForm();
    }

}